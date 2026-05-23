<?php

namespace App\Constants;

enum Permissions: string
{
    // Dashboard
    case DASHBOARD_VIEW = 'dashboard.view';

    // Users
    case USERS_VIEW = 'users.view';
    case USERS_MANAGE = 'users.manage';
    case USERS_DELETE = 'users.delete';

    // Roles & Staff
    case ROLES_MANAGE = 'roles.manage';
    case STAFF_MANAGE = 'staff.manage';

    // Payments
    case PAYMENTS_VIEW = 'payments.view';
    case PAYMENTS_MANAGE = 'payments.manage';

    // KYC
    case KYC_REVIEW = 'kyc.review';

    // Plans
    case PLANS_MANAGE = 'plans.manage';

    // CMS
    case CMS_VIEW = 'cms.view';
    case CMS_MANAGE = 'cms.manage';
    case CMS_CUSTOM_HTML_RAW = 'cms.custom_html.raw';

    // Blog
    case BLOG_VIEW = 'blog.view';
    case BLOG_MANAGE = 'blog.manage';

    // Notifications
    case NOTIFICATIONS_MANAGE = 'notifications.manage';

    // Support
    case SUPPORT_VIEW = 'support.view';
    case SUPPORT_MANAGE = 'support.manage';

    // Reports
    case REPORTS_VIEW = 'reports.view';

    // Settings
    case SETTINGS_MANAGE = 'settings.manage';

    // Languages
    case LANGUAGES_MANAGE = 'languages.manage';

    // System
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
