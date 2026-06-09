<?php

namespace App\Providers;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\KycSubmission;
use App\Domain\Payment\Models\Payment;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Subscription\Models\UserSubscription;
use App\Facades\Settings;
use App\Models\Admin;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\User;
use App\Observers\ActivityObserver;
use App\Repositories\DatabaseSettingsRepository;
use App\Support\AdminNavigation;
use App\Support\RegistrationFields;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DatabaseSettingsRepository::class);
    }

    public function boot(): void
    {
        $this->configureVite();
        $this->configureAssets();
        $this->configureMail();
        $this->configureSocialite();
        $this->registerAdminNavigation();
        $this->registerAdminComponents();
        $this->registerRegistrationFields();
        $this->registerActivityObserver();
        $this->configureGuestRedirect();
        $this->configureAuthRedirect();
    }

    private function registerAdminComponents(): void
    {
        Blade::anonymousComponentPath(
            resource_path('views/admin/components'),
            'admin'
        );

        Blade::directive('viteAssets', function ($expression) {
            return "<?php echo \\App\\Support\\ViteAssets::includeAssets({$expression}); ?>";
        });
    }

    private function configureGuestRedirect(): void
    {
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            if ($request->is('admin/*') && auth('admin')->check()) {
                return route('admin.dashboard');
            }

            return route('user.dashboard');
        });
    }

    /**
     * Send unauthenticated visitors to the correct guard's login page,
     * preserving the page they were trying to reach via Laravel's
     * intended-URL mechanism (redirect()->guest() stores it for us).
     */
    private function configureAuthRedirect(): void
    {
        $resolver = function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        };

        Authenticate::redirectUsing($resolver);
        AuthenticationException::redirectUsing($resolver);
    }

    private function configureVite(): void
    {
        Vite::useBuildDirectory('../../assets/vite');
    }

    private function configureAssets(): void
    {
        $request = request();
        $configUrl = config('app.url');
        $currentSchemeHost = $request->getSchemeAndHttpHost();
        $configPath = parse_url($configUrl, PHP_URL_PATH) ?: '';

        if ($configPath) {
            $configUrl = $currentSchemeHost.$configPath;
        } else {
            $configUrl = $currentSchemeHost;
        }

        config(['app.url' => $configUrl]);
    }

    private function configureMail(): void
    {
        try {
            $driver = Settings::string('mail.driver', 'log');

            config(['mail.default' => $driver]);
            config(['mail.from.address' => Settings::string('mail.from_address', config('mail.from.address'))]);
            config(['mail.from.name' => Settings::string('mail.from_name', config('mail.from.name'))]);

            match ($driver) {
                'smtp' => $this->configureSmtp(),
                'mailgun' => $this->configureMailgun(),
                'ses' => $this->configureSes(),
                'postmark' => config(['mail.mailers.postmark.token' => Settings::string('mail.postmark.token', '')]),
                'resend' => config(['resend.api_key' => Settings::string('mail.resend.key', '')]),
                'sendmail' => config(['mail.mailers.sendmail.path' => '/usr/sbin/sendmail -bs -i']),
                default => null,
            };
        } catch (\Throwable) {
            // DB not ready — skip
        }
    }

    private function configureSmtp(): void
    {
        $encryption = Settings::string('mail.smtp.encryption', 'tls');
        $port = (int) Settings::string('mail.smtp.port', '587');

        // Symfony Mailer schemes: "smtps" = implicit SSL (port 465), "smtp" = STARTTLS/plain
        $scheme = ($encryption === 'ssl' || $port === 465) ? 'smtps' : 'smtp';

        config([
            'mail.mailers.smtp.host' => Settings::string('mail.smtp.host', ''),
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.username' => Settings::string('mail.smtp.username', ''),
            'mail.mailers.smtp.password' => Settings::string('mail.smtp.password', ''),
            'mail.mailers.smtp.encryption' => $encryption ?: null,
            'mail.mailers.smtp.scheme' => $scheme,
        ]);
    }

    private function configureMailgun(): void
    {
        config([
            'services.mailgun.domain' => Settings::string('mail.mailgun.domain', ''),
            'services.mailgun.secret' => Settings::string('mail.mailgun.secret', ''),
            'services.mailgun.endpoint' => Settings::string('mail.mailgun.endpoint', 'api.mailgun.net'),
        ]);
    }

    private function configureSes(): void
    {
        config([
            'services.ses.key' => Settings::string('mail.ses.key', ''),
            'services.ses.secret' => Settings::string('mail.ses.secret', ''),
            'services.ses.region' => Settings::string('mail.ses.region', 'us-east-1'),
        ]);
    }

    private function configureSocialite(): void
    {
        try {
            $map = [
                'google' => 'google',
                'facebook' => 'facebook',
                'twitter' => 'twitter',
                'linkedin' => 'linkedin-openid',
            ];

            foreach ($map as $settingKey => $configKey) {
                $clientId = Settings::string('social.'.$settingKey.'.client_id');
                $clientSecret = Settings::string('social.'.$settingKey.'.client_secret');

                if ($clientId) {
                    config(['services.'.$configKey.'.client_id' => $clientId]);
                }
                if ($clientSecret) {
                    config(['services.'.$configKey.'.client_secret' => $clientSecret]);
                }
            }
        } catch (\Throwable) {
            // DB not ready (e.g. during migrations) — skip
        }
    }

    private function registerAdminNavigation(): void
    {
        AdminNavigation::register([
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'icon' => 'heroicon-o-home',
                'route' => 'admin.dashboard',
                'permission' => 'dashboard.view',
                'sort' => 100,
            ],
            [
                'key' => 'users',
                'label' => 'Users',
                'icon' => 'heroicon-o-users',
                'permission' => 'users.view',
                'sort' => 200,
                'children' => [
                    ['key' => 'users_list',  'label' => 'Users',       'route' => 'admin.users.index',  'permission' => 'users.view', 'icon' => 'heroicon-o-user'],
                    ['key' => 'subscribers', 'label' => 'Subscribers', 'route' => 'admin.cms.subscribers.index', 'permission' => 'cms.manage', 'icon' => 'heroicon-o-envelope'],
                ],
            ],
            [
                'key' => 'roles',
                'label' => 'Roles & Staff',
                'icon' => 'heroicon-o-shield-check',
                'permission' => 'roles.manage',
                'sort' => 300,
                'children' => [
                    ['key' => 'staff', 'label' => 'Staff', 'route' => 'admin.staff.index', 'permission' => 'staff.manage', 'icon' => 'heroicon-o-user-group'],
                    ['key' => 'roles_list', 'label' => 'Roles', 'route' => 'admin.roles.index', 'permission' => 'roles.manage', 'icon' => 'heroicon-o-shield-check'],
                ],
            ],
            [
                'key' => 'payments',
                'label' => 'Finance',
                'icon' => 'heroicon-o-credit-card',
                'permission' => 'payments.view',
                'sort' => 400,
                'children' => [
                    ['key' => 'payments',           'label' => 'Payments',      'route' => 'admin.payments.index',      'permission' => 'payments.view',   'icon' => 'heroicon-o-arrow-down-tray'],
                    ['key' => 'transactions',       'label' => 'Transactions',  'route' => 'admin.transactions.index',  'permission' => 'payments.view',   'icon' => 'heroicon-o-queue-list'],
                    ['key' => 'gateways',           'label' => 'Gateways',      'route' => 'admin.gateways.index',      'permission' => 'payments.manage', 'icon' => 'heroicon-o-credit-card'],
                ],
            ],
            [
                'key' => 'compliance',
                'label' => 'Compliance',
                'icon' => 'heroicon-o-shield-check',
                'permission' => 'users.manage',
                'sort' => 500,
                'children' => [
                    ['key' => 'kyc',           'label' => 'KYC Verification', 'route' => 'admin.kyc.index',           'permission' => 'users.manage', 'icon' => 'heroicon-o-identification'],
                    ['key' => 'plans',         'label' => 'Plans',            'route' => 'admin.plans.index',         'permission' => 'plans.manage', 'icon' => 'heroicon-o-tag'],
                    ['key' => 'subscriptions', 'label' => 'Subscriptions',    'route' => 'admin.subscriptions.index', 'permission' => 'plans.manage', 'icon' => 'heroicon-o-arrow-path'],
                ],
            ],
            [
                'key' => 'contracts',
                'label' => 'Contracts',
                'icon' => 'heroicon-o-document-check',
                'permission' => 'contracts.manage',
                'sort' => 250,
                'children' => [
                    ['key' => 'contract_templates',   'label' => 'Templates',   'route' => 'admin.contracts.templates.index',   'permission' => 'contracts.manage', 'icon' => 'heroicon-o-document-text'],
                    ['key' => 'contract_categories',  'label' => 'Categories',  'route' => 'admin.contracts.categories.index',  'permission' => 'contracts.manage', 'icon' => 'heroicon-o-folder-open'],
                ],
            ],
            [
                'key' => 'contents',
                'label' => 'Contents',
                'icon' => 'heroicon-o-document-text',
                'permission' => 'cms.view',
                'sort' => 700,
                'children' => [
                    ['key' => 'cms_pages',       'label' => 'Pages',       'route' => 'admin.cms.pages.index',       'permission' => 'cms.manage', 'icon' => 'heroicon-o-document'],
                    ['key' => 'cms_menus',       'label' => 'Menus',       'route' => 'admin.cms.menus.index',       'permission' => 'cms.manage', 'icon' => 'heroicon-o-bars-3'],
                ],
            ],
            [
                'key' => 'blog',
                'label' => 'Blog',
                'icon' => 'heroicon-o-pencil-square',
                'permission' => 'blog.view',
                'sort' => 800,
                'children' => [
                    ['key' => 'blog_posts',      'label' => 'Posts',      'route' => 'admin.blog.posts.index',      'permission' => 'blog.view', 'icon' => 'heroicon-o-pencil-square'],
                    ['key' => 'blog_categories', 'label' => 'Categories', 'route' => 'admin.blog.categories.index', 'permission' => 'blog.view', 'icon' => 'heroicon-o-folder-open'],
                    ['key' => 'blog_tags',       'label' => 'Tags',       'route' => 'admin.blog.tags.index',       'permission' => 'blog.view', 'icon' => 'heroicon-o-tag'],
                ],
            ],
            [
                'key' => 'notifications',
                'label' => 'Notifications',
                'icon' => 'heroicon-o-bell',
                'permission' => 'notifications.manage',
                'sort' => 900,
                'children' => [
                    ['key' => 'notif_templates', 'label' => 'Templates',     'route' => 'admin.notifications.templates.index', 'permission' => 'notifications.manage', 'icon' => 'heroicon-o-document-text'],
                    ['key' => 'notif_logs',      'label' => 'Logs',          'route' => 'admin.notifications.logs.index',      'permission' => 'notifications.manage', 'icon' => 'heroicon-o-list-bullet'],
                    ['key' => 'email_health',    'label' => 'Email Health',  'route' => 'admin.notifications.health.index',    'permission' => 'notifications.manage', 'icon' => 'heroicon-o-heart'],
                ],
            ],
            [
                'key' => 'operations',
                'label' => 'Operations',
                'icon' => 'heroicon-o-cog-6-tooth',
                'permission' => 'support.view',
                'sort' => 1000,
                'children' => [
                    ['key' => 'support',  'label' => 'Support Tickets', 'route' => 'admin.support.tickets.index', 'permission' => 'support.view',  'icon' => 'heroicon-o-chat-bubble-left-right'],
                    ['key' => 'reports',  'label' => 'Reports',         'route' => 'admin.reports.index',        'permission' => 'reports.view', 'icon' => 'heroicon-o-chart-bar'],
                ],
            ],
            [
                'key' => 'settings',
                'label' => 'Settings',
                'icon' => 'heroicon-o-cog-6-tooth',
                'permission' => 'settings.manage',
                'sort' => 1200,
                'children' => [
                    ['key' => 'site_settings', 'label' => 'Site Settings',  'route' => 'admin.settings.index',       'icon' => 'heroicon-o-cog-6-tooth'],
                    ['key' => 'languages',     'label' => 'Languages',      'route' => 'admin.languages.index',      'icon' => 'heroicon-o-language'],
                    ['key' => 'activity_log',  'label' => 'Activity Log',   'route' => 'admin.activity-log.index',   'icon' => 'heroicon-o-list-bullet'],
                ],
            ],
            [
                'key' => 'system',
                'label' => 'System',
                'icon' => 'heroicon-o-server',
                'permission' => 'system.view',
                'sort' => 1400,
                'children' => [
                    ['key' => 'system_health',    'label' => 'System Health',    'route' => 'admin.system.index',       'permission' => 'system.view', 'icon' => 'heroicon-o-server'],
                    ['key' => 'scheduler',        'label' => 'Scheduler',        'route' => 'admin.ops.scheduler.index', 'permission' => 'system.view', 'icon' => 'heroicon-o-clock'],
                    ['key' => 'bug_reports',      'label' => 'Bug Reports',      'route' => 'admin.ops.bug-reports.index', 'permission' => 'system.view', 'icon' => 'heroicon-o-exclamation-triangle'],
                ],
            ],
        ]);
    }

    private function registerActivityObserver(): void
    {
        $models = [
            User::class,
            Admin::class,
            Role::class,
            Page::class,
            PageSection::class,
            BlogPost::class,
            BlogCategory::class,
            SupportTicket::class,
            Language::class,
            Payment::class,
            KycSubmission::class,
            Gateway::class,
            Plan::class,
            UserSubscription::class,
        ];

        foreach ($models as $model) {
            $model::observe(ActivityObserver::class);
        }
    }

    private function registerRegistrationFields(): void
    {
        try {
            if (Settings::string('controls.sms_verification') === '1') {
                RegistrationFields::add('mobile', [
                    'label' => __('Phone number'),
                    'type' => 'tel',
                    'required' => true,
                    'rules' => ['required', 'string', 'max:20'],
                    'sort' => 10,
                ]);
                RegistrationFields::add('mobile_country_code', [
                    'label' => '',
                    'type' => 'hidden',
                    'required' => false,
                    'rules' => ['nullable', 'string', 'max:10'],
                    'sort' => 11,
                ]);
            }
        } catch (\Throwable) {
            // Settings table may not exist during install
        }
    }
}
