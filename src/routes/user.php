<?php

use App\Http\Controllers\User\ContractController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\KycController;
use App\Http\Controllers\User\NotificationPreferenceController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\TwoFactorSetupController;
use App\Http\Controllers\User\UserNotificationController;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:user', 'account.active', 'verified.email', 'verified.phone', 'subscription.expire'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::middleware('demo')->group(function () {
            Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
            Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            Route::post('contracts/ai', [ContractController::class, 'aiStore'])->name('contracts.ai.store');
            Route::post('contracts/{contract}/ai-regenerate', [ContractController::class, 'aiRegenerate'])->name('contracts.ai.regenerate');

            Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');

            Route::post('kyc', [KycController::class, 'store'])->name('kyc.store');

            Route::post('subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');

            Route::post('security/2fa/enable', [TwoFactorSetupController::class, 'enable'])->name('2fa.enable');
            Route::post('security/2fa/disable', [TwoFactorSetupController::class, 'disable'])->name('2fa.disable');
            Route::post('security/2fa/regenerate', [TwoFactorSetupController::class, 'regenerateCodes'])->name('2fa.regenerate');
        });
        // ─────────────────────────────────────────────────────────────────────

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/dismiss-nudge', [DashboardController::class, 'dismissNudge'])->name('dashboard.dismiss-nudge');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Contracts — wizard flow: create → templates → wizard → preview → store
        Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create');
        Route::get('contracts/ai/new', [ContractController::class, 'aiCreate'])->name('contracts.ai.create');
        Route::get('contracts/categories/{category}', [ContractController::class, 'templates'])->name('contracts.templates');
        Route::get('contracts/templates/{template}/wizard', [ContractController::class, 'wizard'])->name('contracts.wizard');
        Route::post('contracts/preview', [ContractController::class, 'preview'])->name('contracts.preview');
        Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
        Route::put('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::post('contracts/{contract}/finalize', [ContractController::class, 'finalize'])->name('contracts.finalize');
        Route::post('contracts/{contract}/revert', [ContractController::class, 'revertToDraft'])->name('contracts.revert');
        Route::get('contracts/{contract}/pdf', [ContractController::class, 'pdf'])->name('contracts.pdf');
        Route::post('contracts/{contract}/send-sign', [ContractController::class, 'sendSign'])->name('contracts.send-sign');
        Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

        // Payments
        Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('payment/create', [PaymentController::class, 'create'])->name('payment.create');
        Route::get('payment/{payment}/pending', [PaymentController::class, 'pending'])->name('payment.pending');
        Route::get('payment/{payment}/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('payment/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::get('payment/{payment}/return/{gateway}', [PaymentController::class, 'handleReturn'])->name('payment.return')->withoutMiddleware(['verified.email', 'verified.phone']);

        // KYC
        Route::get('kyc', [KycController::class, 'index'])->name('kyc.index');
        Route::get('kyc/submit', [KycController::class, 'create'])->name('kyc.create');

        // Subscription
        Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
        Route::get('subscription/{plan}/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');


        // 2FA setup
        Route::get('security/2fa', [TwoFactorSetupController::class, 'index'])->name('2fa.index');
        Route::get('security/2fa/setup', [TwoFactorSetupController::class, 'setup'])->name('2fa.setup');

        // In-app notifications
        Route::get('notifications', [UserNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [UserNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('notifications/read-all', [UserNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::get('notifications/unread-count', [UserNotificationController::class, 'unreadCount'])->name('notifications.unread-count');

        // Notification preferences
        Route::get('notifications/preferences', [NotificationPreferenceController::class, 'index'])->name('notifications.preferences');
        Route::put('notifications/preferences', [NotificationPreferenceController::class, 'update'])->name('notifications.preferences.update');

        // Support tickets
        Route::get('support', [SupportTicketController::class, 'index'])->name('support.index');
        Route::get('support/create', [SupportTicketController::class, 'create'])->name('support.create');
        Route::post('support', [SupportTicketController::class, 'store'])->name('support.store');
        Route::get('support/{reference}', [SupportTicketController::class, 'show'])->name('support.show');
        Route::post('support/{reference}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
        Route::post('support/{reference}/close', [SupportTicketController::class, 'close'])->name('support.close');
    });
