<?php

namespace App\Constants;

enum Permissions: string
{
    case DASHBOARD_VIEW = 'dashboard.view';

    case USERS_VIEW = 'users.view';
    case USERS_MANAGE = 'users.manage';
    case USERS_DELETE = 'users.delete';

    case ROLES_MANAGE = 'roles.manage';
    case STAFF_MANAGE = 'staff.manage';

    case PAYMENTS_VIEW = 'payments.view';
    case PAYMENTS_MANAGE = 'payments.manage';

    case KYC_REVIEW = 'kyc.review';

    case PLANS_MANAGE = 'plans.manage';

    case CMS_VIEW = 'cms.view';
    case CMS_MANAGE = 'cms.manage';
    case CMS_CUSTOM_HTML_RAW = 'cms.custom_html.raw';

    case BLOG_VIEW = 'blog.view';
    case BLOG_MANAGE = 'blog.manage';

    case NOTIFICATIONS_MANAGE = 'notifications.manage';

    case SUPPORT_VIEW = 'support.view';
    case SUPPORT_MANAGE = 'support.manage';

    case REPORTS_VIEW = 'reports.view';

    case SETTINGS_MANAGE = 'settings.manage';

    case LANGUAGES_MANAGE = 'languages.manage';

    case SYSTEM_VIEW = 'system.view';

    public function group(): string
    {
        return explode('.', $this->value)[0];
    }

    public static function forRole(string $role): array
    {
        return match ($role) {
            'super_admin' => array_column(self::cases(), 'value'),
            'admin' => [
                self::DASHBOARD_VIEW->value,
                self::USERS_VIEW->value,
                self::USERS_MANAGE->value,
                self::PAYMENTS_VIEW->value,
                self::KYC_REVIEW->value,
                self::PLANS_MANAGE->value,
                self::CMS_VIEW->value,
                self::CMS_MANAGE->value,
                self::BLOG_VIEW->value,
                self::BLOG_MANAGE->value,
                self::NOTIFICATIONS_MANAGE->value,
                self::SUPPORT_VIEW->value,
                self::SUPPORT_MANAGE->value,
                self::REPORTS_VIEW->value,
                self::SETTINGS_MANAGE->value,
                self::LANGUAGES_MANAGE->value,
                self::SYSTEM_VIEW->value,
            ],
            'staff' => [
                self::DASHBOARD_VIEW->value,
                self::USERS_VIEW->value,
                self::SUPPORT_VIEW->value,
                self::SUPPORT_MANAGE->value,
            ],
            'user' => [],
            default => [],
        };
    }
}
