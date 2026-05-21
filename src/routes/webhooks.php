<?php

use App\Http\Controllers\Webhook\PaymentWebhookController;
use App\Http\Controllers\Webhooks\MailgunWebhookController;
use App\Http\Controllers\Webhooks\PostmarkWebhookController;
use App\Http\Controllers\Webhooks\ResendWebhookController;
use App\Http\Controllers\Webhooks\SesWebhookController;
use Illuminate\Support\Facades\Route;

// Payment gateway webhooks — CSRF-exempt, signature-verified inside the controller
Route::post('webhooks/payment/{driver}', [PaymentWebhookController::class, 'handle'])
    ->name('webhooks.payment');

// Email provider webhooks — CSRF-exempt, signature-verified inside each controller
Route::post('webhooks/email/mailgun', [MailgunWebhookController::class, 'handle'])->name('webhooks.email.mailgun');
Route::post('webhooks/email/ses', [SesWebhookController::class, 'handle'])->name('webhooks.email.ses');
Route::post('webhooks/email/postmark', [PostmarkWebhookController::class, 'handle'])->name('webhooks.email.postmark');
Route::post('webhooks/email/resend', [ResendWebhookController::class, 'handle'])->name('webhooks.email.resend');
