<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Blog\CategoryController as BlogCategoryController;
use App\Http\Controllers\Admin\Blog\PostController as BlogPostController;
use App\Http\Controllers\Admin\Blog\TagController as BlogTagController;
use App\Http\Controllers\Admin\Cms\MenuController;
use App\Http\Controllers\Admin\Cms\PageController;
use App\Http\Controllers\Admin\Cms\PageSectionController;
use App\Http\Controllers\Admin\Cms\SubscriberController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\GatewayController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Notifications\EmailHealthController;
use App\Http\Controllers\Admin\Notifications\NotificationLogController;
use App\Http\Controllers\Admin\Notifications\TemplateController;
use App\Http\Controllers\Admin\Ops\BugReportController;
use App\Http\Controllers\Admin\Ops\ReportController;
use App\Http\Controllers\Admin\Ops\SchedulerController;
use App\Http\Controllers\Admin\Ops\SystemController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\Support\TicketController as SupportTicketController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\PayoutMethodController;
use App\Models\Admin;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Route;

// Dev-only: auto-login as super admin to preview the shell
if (app()->isLocal()) {
    Route::get('admin/dev-login', function () {
        $admin = Admin::first();
        auth('admin')->login($admin);

        return redirect()->route('admin.dashboard');
    });
}

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'account.active'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', fn () => view('admin.dashboard.index'))->name('profile');

        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.view');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:users.view');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.manage');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.manage');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
        Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk')->middleware('permission:users.manage');
        Route::post('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email')->middleware('permission:users.manage');
        Route::post('users/{user}/unverify-email', [UserController::class, 'unverifyEmail'])->name('users.unverify-email')->middleware('permission:users.manage');
        Route::post('users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password')->middleware('permission:users.manage');
        Route::post('users/{user}/update-status', [UserController::class, 'updateStatus'])->name('users.update-status')->middleware('permission:users.manage');
        Route::post('users/{user}/update-kyc', [UserController::class, 'updateKyc'])->name('users.update-kyc')->middleware('permission:users.manage');
        Route::post('users/{user}/adjust-balance', [UserController::class, 'adjustBalance'])->name('users.adjust-balance')->middleware('permission:users.manage');
        Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate')->middleware('permission:users.manage');

        // Gateways
        Route::get('gateways', [GatewayController::class, 'index'])->name('gateways.index')->middleware('permission:payments.manage');
        Route::get('gateways/{driver}/edit', [GatewayController::class, 'edit'])->name('gateways.edit')->middleware('permission:payments.manage');
        Route::put('gateways/{driver}', [GatewayController::class, 'update'])->name('gateways.update')->middleware('permission:payments.manage');
        Route::post('gateways/{gateway}/toggle', [GatewayController::class, 'toggleEnabled'])->name('gateways.toggle')->middleware('permission:payments.manage');

        // Payments
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index')->middleware('permission:payments.view');
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('permission:payments.view');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show')->middleware('permission:payments.view');
        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve')->middleware('permission:payments.manage');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject')->middleware('permission:payments.manage');
        Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt')->middleware('permission:payments.view');

        // Payouts
        // Payout methods
        Route::get('payout-methods', [PayoutMethodController::class, 'index'])->name('payout-methods.index')->middleware('permission:payments.view');
        Route::get('payout-methods/create', [PayoutMethodController::class, 'create'])->name('payout-methods.create')->middleware('permission:payments.manage');
        Route::post('payout-methods', [PayoutMethodController::class, 'store'])->name('payout-methods.store')->middleware('permission:payments.manage');
        Route::get('payout-methods/{payoutMethod}/edit', [PayoutMethodController::class, 'edit'])->name('payout-methods.edit')->middleware('permission:payments.manage');
        Route::put('payout-methods/{payoutMethod}', [PayoutMethodController::class, 'update'])->name('payout-methods.update')->middleware('permission:payments.manage');
        Route::post('payout-methods/{payoutMethod}/toggle', [PayoutMethodController::class, 'toggleEnabled'])->name('payout-methods.toggle')->middleware('permission:payments.manage');
        Route::delete('payout-methods/{payoutMethod}', [PayoutMethodController::class, 'destroy'])->name('payout-methods.destroy')->middleware('permission:payments.manage');

        // Payouts
        Route::get('payouts', [PayoutController::class, 'index'])->name('payouts.index')->middleware('permission:payments.view');
        Route::get('payouts/{payout}', [PayoutController::class, 'show'])->name('payouts.show')->middleware('permission:payments.view');
        Route::post('payouts/{payout}/approve', [PayoutController::class, 'approve'])->name('payouts.approve')->middleware('permission:payments.manage');
        Route::post('payouts/{payout}/reject', [PayoutController::class, 'reject'])->name('payouts.reject')->middleware('permission:payments.manage');

        // KYC
        Route::get('kyc', [KycController::class, 'index'])->name('kyc.index')->middleware('permission:users.manage');
        Route::get('kyc/{submission}', [KycController::class, 'show'])->name('kyc.show')->middleware('permission:users.manage');
        Route::post('kyc/{submission}/approve', [KycController::class, 'approve'])->name('kyc.approve')->middleware('permission:users.manage');
        Route::post('kyc/{submission}/reject', [KycController::class, 'reject'])->name('kyc.reject')->middleware('permission:users.manage');
        Route::get('kyc/{submission}/document/{index}', [KycController::class, 'document'])->name('kyc.document')->middleware('permission:users.manage');

        // Plans
        Route::get('plans', [PlanController::class, 'index'])->name('plans.index')->middleware('permission:plans.manage');
        Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create')->middleware('permission:plans.manage');
        Route::post('plans', [PlanController::class, 'store'])->name('plans.store')->middleware('permission:plans.manage');
        Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit')->middleware('permission:plans.manage');
        Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update')->middleware('permission:plans.manage');
        Route::delete('plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy')->middleware('permission:plans.manage');
        Route::post('plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle')->middleware('permission:plans.manage');

        // User subscription management
        Route::post('users/{user}/assign-plan', [UserController::class, 'assignPlan'])->name('users.assign-plan')->middleware('permission:users.manage');
        Route::post('users/{user}/cancel-subscription', [UserController::class, 'cancelSubscription'])->name('users.cancel-subscription')->middleware('permission:users.manage');

        // Subscriptions (global list)
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index')->middleware('permission:plans.manage');
        Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show')->middleware('permission:plans.manage');
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel')->middleware('permission:plans.manage');

        // CMS — Pages
        Route::get('cms/pages', [PageController::class, 'index'])->name('cms.pages.index')->middleware('permission:cms.manage');
        Route::get('cms/pages/create', [PageController::class, 'create'])->name('cms.pages.create')->middleware('permission:cms.manage');
        Route::post('cms/pages', [PageController::class, 'store'])->name('cms.pages.store')->middleware('permission:cms.manage');
        Route::get('cms/pages/{page}/edit', [PageController::class, 'edit'])->name('cms.pages.edit')->middleware('permission:cms.manage');
        Route::put('cms/pages/{page}', [PageController::class, 'update'])->name('cms.pages.update')->middleware('permission:cms.manage');
        Route::delete('cms/pages/{page}', [PageController::class, 'destroy'])->name('cms.pages.destroy')->middleware('permission:cms.manage');
        Route::get('cms/pages/{page}/preview', [PageController::class, 'preview'])->name('cms.pages.preview');

        // CMS — Page Sections
        Route::post('cms/pages/{page}/sections', [PageSectionController::class, 'store'])->name('cms.sections.store')->middleware('permission:cms.manage');
        Route::get('cms/pages/{page}/sections/{section}/edit', [PageSectionController::class, 'edit'])->name('cms.sections.edit')->middleware('permission:cms.manage');
        Route::put('cms/pages/{page}/sections/{section}', [PageSectionController::class, 'update'])->name('cms.sections.update')->middleware('permission:cms.manage');
        Route::delete('cms/pages/{page}/sections/{section}', [PageSectionController::class, 'destroy'])->name('cms.sections.destroy')->middleware('permission:cms.manage');
        Route::post('cms/pages/{page}/sections/reorder', [PageSectionController::class, 'reorder'])->name('cms.sections.reorder')->middleware('permission:cms.manage');
        Route::post('cms/pages/{page}/sections/{section}/toggle', [PageSectionController::class, 'toggle'])->name('cms.sections.toggle')->middleware('permission:cms.manage');

        // CMS — Menus
        Route::get('cms/menus', [MenuController::class, 'index'])->name('cms.menus.index')->middleware('permission:cms.manage');
        Route::get('cms/menus/{menu}/edit', [MenuController::class, 'edit'])->name('cms.menus.edit')->middleware('permission:cms.manage');
        Route::post('cms/menus/{menu}/items', [MenuController::class, 'addItem'])->name('cms.menus.items.store')->middleware('permission:cms.manage');
        Route::put('cms/menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('cms.menus.items.update')->middleware('permission:cms.manage');
        Route::delete('cms/menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('cms.menus.items.destroy')->middleware('permission:cms.manage');
        Route::post('cms/menus/{menu}/reorder', [MenuController::class, 'reorder'])->name('cms.menus.reorder')->middleware('permission:cms.manage');

        // CMS — Subscribers & Newsletter
        Route::get('cms/subscribers', [SubscriberController::class, 'index'])->name('cms.subscribers.index')->middleware('permission:cms.manage');
        Route::delete('cms/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('cms.subscribers.destroy')->middleware('permission:cms.manage');
        Route::get('cms/subscribers/broadcast', [SubscriberController::class, 'broadcastForm'])->name('cms.subscribers.broadcast')->middleware('permission:cms.manage');
        Route::post('cms/subscribers/broadcast/send', [SubscriberController::class, 'broadcast'])->name('cms.subscribers.broadcast.send')->middleware('permission:cms.manage');

        // Blog — Posts
        Route::get('blog/posts', [BlogPostController::class, 'index'])->name('blog.posts.index')->middleware('permission:blog.view');
        Route::get('blog/posts/create', [BlogPostController::class, 'create'])->name('blog.posts.create')->middleware('permission:blog.manage');
        Route::post('blog/posts', [BlogPostController::class, 'store'])->name('blog.posts.store')->middleware('permission:blog.manage');
        Route::get('blog/posts/{post}/edit', [BlogPostController::class, 'edit'])->name('blog.posts.edit')->middleware('permission:blog.manage');
        Route::put('blog/posts/{post}', [BlogPostController::class, 'update'])->name('blog.posts.update')->middleware('permission:blog.manage');
        Route::delete('blog/posts/{post}', [BlogPostController::class, 'destroy'])->name('blog.posts.destroy')->middleware('permission:blog.manage');

        // Blog — Categories
        Route::get('blog/categories', [BlogCategoryController::class, 'index'])->name('blog.categories.index')->middleware('permission:blog.view');
        Route::get('blog/categories/create', [BlogCategoryController::class, 'create'])->name('blog.categories.create')->middleware('permission:blog.manage');
        Route::post('blog/categories', [BlogCategoryController::class, 'store'])->name('blog.categories.store')->middleware('permission:blog.manage');
        Route::get('blog/categories/{category}/edit', [BlogCategoryController::class, 'edit'])->name('blog.categories.edit')->middleware('permission:blog.manage');
        Route::put('blog/categories/{category}', [BlogCategoryController::class, 'update'])->name('blog.categories.update')->middleware('permission:blog.manage');
        Route::delete('blog/categories/{category}', [BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy')->middleware('permission:blog.manage');

        // Blog — Tags
        Route::get('blog/tags', [BlogTagController::class, 'index'])->name('blog.tags.index')->middleware('permission:blog.view');
        Route::post('blog/tags', [BlogTagController::class, 'store'])->name('blog.tags.store')->middleware('permission:blog.manage');
        Route::delete('blog/tags/{tag}', [BlogTagController::class, 'destroy'])->name('blog.tags.destroy')->middleware('permission:blog.manage');

        // Staff
        Route::resource('staff', StaffController::class)->middleware('permission:staff.manage');

        // Roles
        Route::resource('roles', RoleController::class)->middleware('permission:roles.manage');

        // Activity log
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index')->middleware('permission:system.view');
        Route::get('activity-log/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-log.show')->middleware('permission:system.view');

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('permission:settings.manage');
        Route::put('settings/{tab?}', [SettingsController::class, 'update'])->name('settings.update')->middleware('permission:settings.manage');

        // Languages
        Route::get('languages', [LanguageController::class, 'index'])->name('languages.index')->middleware('permission:languages.manage');
        Route::get('languages/create', [LanguageController::class, 'create'])->name('languages.create')->middleware('permission:languages.manage');
        Route::post('languages', [LanguageController::class, 'store'])->name('languages.store')->middleware('permission:languages.manage');
        Route::get('languages/{language}/edit', [LanguageController::class, 'edit'])->name('languages.edit')->middleware('permission:languages.manage');
        Route::put('languages/{language}', [LanguageController::class, 'update'])->name('languages.update')->middleware('permission:languages.manage');
        Route::post('languages/{language}/default', [LanguageController::class, 'setDefault'])->name('languages.default')->middleware('permission:languages.manage');
        Route::post('languages/{language}/toggle', [LanguageController::class, 'toggle'])->name('languages.toggle')->middleware('permission:languages.manage');
        Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy')->middleware('permission:languages.manage');

        // Translations
        Route::get('translations', [TranslationController::class, 'index'])->name('translations.index')->middleware('permission:languages.manage');
        Route::post('translations', [TranslationController::class, 'store'])->name('translations.store')->middleware('permission:languages.manage');
        Route::post('translations/bulk', [TranslationController::class, 'bulkUpdate'])->name('translations.bulk-update')->middleware('permission:languages.manage');
        Route::post('translations/sync-keywords', [TranslationController::class, 'syncKeywords'])->name('translations.sync-keywords')->middleware('permission:languages.manage');
        Route::post('translations/auto-translate', [TranslationController::class, 'autoTranslate'])->name('translations.auto-translate')->middleware('permission:languages.manage');
        Route::post('translations/auto-translate-batch', [TranslationController::class, 'autoTranslateBatch'])->name('translations.auto-translate-batch')->middleware('permission:languages.manage');
        Route::delete('translations/{key}', [TranslationController::class, 'destroy'])->name('translations.destroy')->middleware('permission:languages.manage');

        // Admin bell notifications (system)
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::get('notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');

        // Notification templates & settings
        Route::get('notification-templates', [TemplateController::class, 'index'])->name('notifications.templates.index')->middleware('permission:notifications.manage');
        Route::get('notification-templates/{template}/edit', [TemplateController::class, 'edit'])->name('notifications.templates.edit')->middleware('permission:notifications.manage');
        Route::put('notification-templates/{template}', [TemplateController::class, 'update'])->name('notifications.templates.update')->middleware('permission:notifications.manage');
        Route::post('notification-templates/{template}/test', [TemplateController::class, 'testSend'])->name('notifications.templates.test')->middleware('permission:notifications.manage');
        Route::post('notification-templates/{template}/rollback/{version}', [TemplateController::class, 'rollback'])->name('notifications.templates.rollback')->middleware('permission:notifications.manage');

        // Notification logs
        Route::get('notification-logs', [NotificationLogController::class, 'index'])->name('notifications.logs.index')->middleware('permission:notifications.manage');

        // Email health
        Route::get('email-health', [EmailHealthController::class, 'index'])->name('notifications.health.index')->middleware('permission:notifications.manage');

        // Support tickets
        Route::get('support/tickets', [SupportTicketController::class, 'index'])->name('support.tickets.index')->middleware('permission:support.view');
        Route::get('support/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('support.tickets.show')->middleware('permission:support.view');
        Route::post('support/tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.tickets.reply')->middleware('permission:support.manage');
        Route::patch('support/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.tickets.status')->middleware('permission:support.manage');
        Route::patch('support/tickets/{ticket}/assign', [SupportTicketController::class, 'assign'])->name('support.tickets.assign')->middleware('permission:support.manage');
        Route::post('support/tickets/{ticket}/close', [SupportTicketController::class, 'close'])->name('support.tickets.close')->middleware('permission:support.manage');
        Route::post('support/tickets/{ticket}/reopen', [SupportTicketController::class, 'reopen'])->name('support.tickets.reopen')->middleware('permission:support.manage');
        Route::get('support/attachments/{attachment}', [SupportTicketController::class, 'attachment'])->name('support.attachment.download')->middleware('permission:support.view');

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports.view');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export')->middleware('permission:reports.view');

        // System Health
        Route::get('system', [SystemController::class, 'index'])->name('system.index')->middleware('permission:system.view');
        Route::post('system/flush', [SystemController::class, 'flush'])->name('system.flush')->middleware('permission:system.view');
        Route::post('system/test-mail', [SystemController::class, 'testMail'])->name('system.test-mail')->middleware('permission:system.view');

        // Scheduler
        Route::get('ops/scheduler', [SchedulerController::class, 'index'])->name('ops.scheduler.index')->middleware('permission:system.view');
        Route::post('ops/scheduler/run', [SchedulerController::class, 'run'])->name('ops.scheduler.run')->middleware('permission:system.view');

        // Bug reports
        Route::get('ops/bug-reports', [BugReportController::class, 'index'])->name('ops.bug-reports.index')->middleware('permission:system.view');
        Route::post('ops/bug-reports', [BugReportController::class, 'store'])->name('ops.bug-reports.store')->middleware('permission:system.view');
        Route::patch('ops/bug-reports/{bugReport}', [BugReportController::class, 'update'])->name('ops.bug-reports.update')->middleware('permission:system.view');
        Route::delete('ops/bug-reports/{bugReport}', [BugReportController::class, 'destroy'])->name('ops.bug-reports.destroy')->middleware('permission:system.view');

        Route::post('cache/clear', function () {
            try {
                \Illuminate\Support\Facades\Artisan::call('cache:clear');
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                \Illuminate\Support\Facades\Artisan::call('config:clear');
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return response()->json(['success' => true, 'message' => 'Cache cleared']);
        })->name('cache.clear')->middleware('permission:system.view');

        Route::post('logout', function () {
            /** @var SessionGuard $guard */
            $guard = auth('admin');
            $guard->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('admin.login');
        })->name('logout');
    });
