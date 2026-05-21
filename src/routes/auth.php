<?php

use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\AdminResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// --- Admin auth ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'show'])->name('login');
        Route::post('login', [AdminLoginController::class, 'store'])->name('login.store');

        Route::get('forgot-password', [AdminForgotPasswordController::class, 'show'])->name('password.request');
        Route::post('forgot-password', [AdminForgotPasswordController::class, 'store'])->name('password.email');

        Route::get('reset-password/{token}', [AdminResetPasswordController::class, 'show'])->name('password.reset');
        Route::post('reset-password', [AdminResetPasswordController::class, 'store'])->name('password.update');
    });
});

// --- User guest routes ---
Route::middleware('guest:user')->group(function () {
    Route::get('register', [RegisterController::class, 'show'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');

    // 2FA challenge (shown after credentials pass but before full login)
    Route::get('two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('two-factor-challenge', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('two-factor-challenge/recovery', [TwoFactorController::class, 'verifyRecovery'])->name('2fa.recovery');

    // Socialite
    Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('social.callback');
});

// --- User authenticated auth routes ---
Route::middleware('auth:user')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // Email verification
    Route::get('email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Phone verification (gated by controls.sms_verification)
    Route::get('phone/verify', [PhoneVerificationController::class, 'show'])->name('phone.verify.show');
    Route::post('phone/verify/send', [PhoneVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('phone.send');
    Route::post('phone/verify', [PhoneVerificationController::class, 'verify'])
        ->middleware('throttle:10,1')
        ->name('phone.verify');
});
