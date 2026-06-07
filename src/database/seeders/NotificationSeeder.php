<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'welcome',
                'name' => 'Welcome',
                'description' => 'Sent when a new user registers.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name',  'description' => 'User\'s name'],
                    ['key' => 'app.name',   'description' => 'Application name'],
                    ['key' => 'app.url',    'description' => 'Application URL'],
                ],
                'subject' => ['en' => 'Welcome to {{app.name}}!'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Welcome to {{app.name}}! We\'re glad to have you on board.</p><p>If you have any questions, feel free to reach out to our support team.</p><p>Best regards,<br>The {{app.name}} Team</p>'],
                'sms_body' => ['en' => 'Welcome to {{app.name}}, {{user.name}}! Your account is ready.'],
            ],
            [
                'key' => 'email_verification',
                'name' => 'Email Verification',
                'description' => 'Sent to verify the user\'s email address.',
                'channels' => ['email'],
                'variables' => [
                    ['key' => 'user.name',        'description' => 'User\'s name'],
                    ['key' => 'verification.url', 'description' => 'Email verification link'],
                ],
                'subject' => ['en' => 'Verify your email address'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Please click the button below to verify your email address.</p><p><a href="{{verification.url}}" class="email-cta">Verify Email</a></p><p>If you did not create an account, no further action is required.</p>'],
                'sms_body' => ['en' => ''],
            ],
            [
                'key' => 'password_reset',
                'name' => 'Password Reset',
                'description' => 'Sent when a user requests a password reset.',
                'channels' => ['email'],
                'variables' => [
                    ['key' => 'user.name',  'description' => 'User\'s name'],
                    ['key' => 'reset.url',  'description' => 'Password reset link'],
                    ['key' => 'reset.expires', 'description' => 'Minutes until link expires'],
                ],
                'subject' => ['en' => 'Reset your password'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>You requested a password reset. Click below to set a new password.</p><p><a href="{{reset.url}}" class="email-cta">Reset Password</a></p><p>This link expires in {{reset.expires}} minutes. If you did not request a reset, ignore this email.</p>'],
                'sms_body' => ['en' => ''],
            ],
            [
                'key' => 'payment_received',
                'name' => 'Payment Received',
                'description' => 'Sent when a payment is pending review.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name',    'description' => 'User\'s name'],
                    ['key' => 'amount',       'description' => 'Payment amount'],
                    ['key' => 'currency',     'description' => 'Currency code'],
                    ['key' => 'gateway',      'description' => 'Payment gateway name'],
                ],
                'subject' => ['en' => 'Payment received — {{amount}} {{currency}}'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>We received your payment of <strong>{{amount}} {{currency}}</strong> via {{gateway}}.</p><p>Your payment is currently being reviewed. You will be notified once it is approved.</p>'],
                'sms_body' => ['en' => 'Payment of {{amount}} {{currency}} received and is under review.'],
            ],
            [
                'key' => 'payment_approved',
                'name' => 'Payment Approved',
                'description' => 'Sent when a payment is approved and wallet is credited.',
                'channels' => ['email', 'sms', 'in_app'],
                'variables' => [
                    ['key' => 'user.name',    'description' => 'User\'s name'],
                    ['key' => 'amount',       'description' => 'Payment amount'],
                    ['key' => 'currency',     'description' => 'Currency code'],
                    ['key' => 'balance',      'description' => 'New wallet balance'],
                ],
                'subject' => ['en' => 'Payment approved — {{amount}} {{currency}} added to your wallet'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Your payment of <strong>{{amount}} {{currency}}</strong> has been approved and added to your wallet.</p><p>Your new balance is <strong>{{balance}}</strong>.</p>'],
                'sms_body' => ['en' => 'Your payment of {{amount}} {{currency}} was approved. New balance: {{balance}}.'],
            ],
            [
                'key' => 'payment_rejected',
                'name' => 'Payment Rejected',
                'description' => 'Sent when a payment is rejected.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name', 'description' => 'User\'s name'],
                    ['key' => 'amount',    'description' => 'Payment amount'],
                    ['key' => 'currency',  'description' => 'Currency code'],
                    ['key' => 'reason',    'description' => 'Rejection reason'],
                ],
                'subject' => ['en' => 'Payment rejected'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Unfortunately your payment of <strong>{{amount}} {{currency}}</strong> was rejected.</p><p>Reason: {{reason}}</p><p>If you have questions, please contact support.</p>'],
                'sms_body' => ['en' => 'Your payment of {{amount}} {{currency}} was rejected. Reason: {{reason}}'],
            ],
            [
                'key' => 'kyc_submitted',
                'name' => 'KYC Submitted',
                'description' => 'Sent when a user submits KYC documents.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name', 'description' => 'User\'s name'],
                ],
                'subject' => ['en' => 'KYC documents received'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>We have received your KYC documents and they are currently under review. This usually takes 1–3 business days.</p>'],
                'sms_body' => ['en' => 'Your KYC documents have been received and are under review.'],
            ],
            [
                'key' => 'kyc_approved',
                'name' => 'KYC Approved',
                'description' => 'Sent when a KYC submission is approved.',
                'channels' => ['email', 'sms', 'in_app'],
                'variables' => [
                    ['key' => 'user.name', 'description' => 'User\'s name'],
                ],
                'subject' => ['en' => 'KYC approved — your account is verified'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Great news! Your identity has been verified. You now have full access to all platform features.</p>'],
                'sms_body' => ['en' => 'Your KYC verification was approved. You now have full access.'],
            ],
            [
                'key' => 'kyc_rejected',
                'name' => 'KYC Rejected',
                'description' => 'Sent when a KYC submission is rejected.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name', 'description' => 'User\'s name'],
                    ['key' => 'reason',    'description' => 'Rejection reason'],
                ],
                'subject' => ['en' => 'KYC verification not approved'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Unfortunately your KYC documents were not approved.</p><p>Reason: {{reason}}</p><p>Please resubmit with the correct documents.</p>'],
                'sms_body' => ['en' => 'Your KYC was not approved. Reason: {{reason}}. Please resubmit.'],
            ],
            [
                'key' => 'ticket_created',
                'name' => 'Support Ticket Created',
                'description' => 'Sent when a user opens a support ticket.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name',         'description' => 'User\'s name'],
                    ['key' => 'ticket.reference',  'description' => 'Ticket reference number'],
                    ['key' => 'ticket.subject',    'description' => 'Ticket subject'],
                    ['key' => 'ticket.url',        'description' => 'Link to view the ticket'],
                ],
                'subject' => ['en' => 'Support ticket #{{ticket.reference}} received'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>Thank you for contacting support. Your ticket has been received.</p><p><strong>Reference:</strong> #{{ticket.reference}}<br><strong>Subject:</strong> {{ticket.subject}}</p><p>We\'ll respond as soon as possible. <a href="{{ticket.url}}">View your ticket →</a></p>'],
                'sms_body' => ['en' => 'Support ticket #{{ticket.reference}} received. We will be in touch shortly.'],
            ],
            [
                'key' => 'ticket_update',
                'name' => 'Support Ticket Update',
                'description' => 'Sent when an admin replies to or updates a ticket.',
                'channels' => ['email', 'in_app'],
                'variables' => [
                    ['key' => 'user.name',         'description' => 'User\'s name'],
                    ['key' => 'ticket.reference',  'description' => 'Ticket reference number'],
                    ['key' => 'ticket.subject',    'description' => 'Ticket subject'],
                    ['key' => 'ticket.url',        'description' => 'Link to view the ticket'],
                    ['key' => 'update',            'description' => 'Brief description of the update'],
                ],
                'subject' => ['en' => 'Update on your ticket #{{ticket.reference}}'],
                'email_body' => ['en' => '<p>Hi {{user.name}},</p><p>There\'s an update on your support ticket.</p><p><strong>Reference:</strong> #{{ticket.reference}}<br><strong>Subject:</strong> {{ticket.subject}}</p><p><a href="{{ticket.url}}">View ticket and reply →</a></p>'],
                'sms_body' => ['en' => 'Update on ticket #{{ticket.reference}}. Visit {{ticket.url}} to view.'],
            ],
            [
                'key' => 'contract_sign_request',
                'name' => 'Contract Sign Request',
                'description' => 'Sent to a party when their signature is requested on a contract.',
                'channels' => ['email'],
                'variables' => [
                    ['key' => 'party.name',    'description' => 'Signer\'s name'],
                    ['key' => 'contract.title','description' => 'Contract title'],
                    ['key' => 'sign.url',      'description' => 'Link to sign the contract'],
                    ['key' => 'app.name',      'description' => 'Application name'],
                    ['key' => 'app.url',       'description' => 'Application URL'],
                ],
                'subject' => ['en' => 'Signature required: {{contract.title}}'],
                'email_body' => ['en' => '<p>Hi {{party.name}},</p><p>You have been requested to sign a contract: <strong>{{contract.title}}</strong>.</p><p>Please click the button below to review and sign the document.</p><p><a href="{{sign.url}}" class="email-cta-accent">Review &amp; Sign</a></p><p>If you cannot click the button, copy and paste this link into your browser:<br>{{sign.url}}</p><p>This link is unique to you. Do not share it.</p>'],
                'sms_body' => ['en' => ''],
            ],
            [
                'key' => 'email_layout',
                'name' => 'Email Layout',
                'description' => 'Global wrapper used to render every outgoing notification email (header, content area, footer with unsubscribe link).',
                'channels' => ['email'],
                'variables' => [
                    ['key' => 'app.name',          'description' => 'Application name'],
                    ['key' => 'app.url',           'description' => 'Application URL'],
                    ['key' => 'content',           'description' => 'Rendered notification body'],
                    ['key' => 'unsubscribe.url',   'description' => 'Unsubscribe link'],
                ],
                'subject' => ['en' => ''],
                'email_body' => ['en' => '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{app.name}}</title><link rel="stylesheet" href="{{app.url}}/assets/static/global/css/email.css"></head><body class="email-body"><table width="100%" cellpadding="0" cellspacing="0" class="email-wrapper"><tr><td align="center"><table width="600" cellpadding="0" cellspacing="0" class="email-card"><tr><td class="email-header"><a href="{{app.url}}" class="email-brand">{{app.name}}</a></td></tr><tr><td class="email-content">{{content}}</td></tr><tr><td class="email-footer"><p class="email-copyright">&copy; {{app.name}} &mdash; <a href="{{app.url}}">{{app.url}}</a></p><p><a href="{{unsubscribe.url}}">Unsubscribe</a></p></td></tr></table></td></tr></table></body></html>'],
                'sms_body' => ['en' => ''],
            ],
        ];

        foreach ($templates as $data) {
            $template = NotificationTemplate::firstOrNew(['key' => $data['key']]);
            $template->name = $data['name'];
            $template->description = $data['description'];
            $template->channels = $data['channels'];
            $template->variables = $data['variables'];
            $template->is_active = true;
            $template->is_system = true;

            if ($template->subject === null) {
                $template->subject = $data['subject'];
                $template->email_body = $data['email_body'];
                $template->sms_body = $data['sms_body'];
            }

            $template->save();
        }
    }
}
