<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\KycController;
use App\Http\Controllers\User\NotificationPreferenceController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\TwoFactorSetupController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\PayoutController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:user', 'account.active', 'verified.phone'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/dismiss-nudge', [DashboardController::class, 'dismissNudge'])->name('dashboard.dismiss-nudge');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::delete('profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Wallet
        Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');

        // Payments
        Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('payment/create', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');
        Route::get('payment/{payment}/pending', [PaymentController::class, 'pending'])->name('payment.pending');
        Route::get('payment/{payment}/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('payment/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::get('payment/{payment}/return/{gateway}', [PaymentController::class, 'handleReturn'])->name('payment.return');

        // KYC
        Route::get('kyc', [KycController::class, 'index'])->name('kyc.index');
        Route::get('kyc/submit', [KycController::class, 'create'])->name('kyc.create');
        Route::post('kyc', [KycController::class, 'store'])->name('kyc.store');

        // Subscription
        Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
        Route::post('subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');

        // Payouts
        Route::get('payout', [PayoutController::class, 'index'])->name('payout.index');
        Route::get('payout/create', [PayoutController::class, 'create'])->middleware('kyc')->name('payout.create');
        Route::post('payout', [PayoutController::class, 'store'])->middleware('kyc')->name('payout.store');
        Route::get('payout/{payout}/pending', [PayoutController::class, 'pending'])->name('payout.pending');
        Route::post('payout/{payout}/cancel', [PayoutController::class, 'cancel'])->name('payout.cancel');

        // 2FA setup
        Route::get('security/2fa', [TwoFactorSetupController::class, 'index'])->name('2fa.index');
        Route::get('security/2fa/setup', [TwoFactorSetupController::class, 'setup'])->name('2fa.setup');
        Route::post('security/2fa/enable', [TwoFactorSetupController::class, 'enable'])->name('2fa.enable');
        Route::post('security/2fa/disable', [TwoFactorSetupController::class, 'disable'])->name('2fa.disable');
        Route::post('security/2fa/regenerate', [TwoFactorSetupController::class, 'regenerateCodes'])->name('2fa.regenerate');

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
