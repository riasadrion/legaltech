<?php

namespace Database\Seeders;

use App\Domain\Support\Models\SupportTicket;
use App\Domain\Support\Models\SupportMessage;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(12)->get();
        $admin = Admin::first();

        if ($users->isEmpty()) {
            return;
        }

        $ticketData = [
            [
                'user_id' => 1,
                'subject' => 'How to upgrade my plan?',
                'category' => SupportTicket::CATEGORY_BILLING,
                'priority' => SupportTicket::PRIORITY_NORMAL,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(45),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'Hi, I\'d like to upgrade from the Starter plan to Professional. Can you guide me through the process?', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'You can upgrade directly from your account settings under "Billing > Plans". Click the upgrade button on the Professional plan.', 'is_internal' => false],
                    ['author_type' => 'user', 'body' => 'Great! It worked. Thank you for the help!', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 2,
                'subject' => 'Payment failed - need assistance',
                'category' => SupportTicket::CATEGORY_BILLING,
                'priority' => SupportTicket::PRIORITY_HIGH,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(30),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'My payment keeps failing with error code 402. I\'ve tried multiple payment methods.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Error 402 usually means insufficient funds. Let\'s try a different gateway. Can you try PayPal instead of Stripe?', 'is_internal' => false],
                    ['author_type' => 'user', 'body' => 'PayPal worked! Thanks for the suggestion.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 3,
                'subject' => 'API integration not working',
                'category' => SupportTicket::CATEGORY_TECHNICAL,
                'priority' => SupportTicket::PRIORITY_HIGH,
                'status' => SupportTicket::STATUS_IN_PROGRESS,
                'assigned_to' => $admin?->id,
                'closed_at' => null,
                'messages' => [
                    ['author_type' => 'user', 'body' => 'I\'m getting a 401 Unauthorized error when trying to use the API. My API key should be valid.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Let me check your API key status. Can you provide the last 4 characters of your API key?', 'is_internal' => false],
                    ['author_type' => 'user', 'body' => 'The key ends with ...abcd', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'I found the issue. Your API key was rotated yesterday. Check your dashboard for the new key.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 4,
                'subject' => 'KYC verification stuck',
                'category' => SupportTicket::CATEGORY_KYC,
                'priority' => SupportTicket::PRIORITY_URGENT,
                'status' => SupportTicket::STATUS_WAITING,
                'assigned_to' => $admin?->id,
                'closed_at' => null,
                'messages' => [
                    ['author_type' => 'user', 'body' => 'My KYC verification has been pending for 5 days. I need to withdraw funds urgently.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'I\'ll expedite your verification. We need a clearer photo of your ID document.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 5,
                'subject' => 'Refund request for erroneous payment',
                'category' => SupportTicket::CATEGORY_BILLING,
                'priority' => SupportTicket::PRIORITY_NORMAL,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(15),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'I accidentally made a double payment. Can I get a refund?', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Yes, I\'ve processed a refund of $50 to your original payment method. It should appear within 3-5 business days.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 6,
                'subject' => 'Feature request: Dark mode',
                'category' => SupportTicket::CATEGORY_GENERAL,
                'priority' => SupportTicket::PRIORITY_LOW,
                'status' => SupportTicket::STATUS_OPEN,
                'assigned_to' => null,
                'closed_at' => null,
                'messages' => [
                    ['author_type' => 'user', 'body' => 'Would love to see a dark mode option for the dashboard. Long hours of work at night are eye-straining.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Thank you for the feature request! We\'ve added it to our roadmap.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'FYI: This is already in development for Q3.', 'is_internal' => true],
                ],
            ],
            [
                'user_id' => 7,
                'subject' => 'Login issues after password reset',
                'category' => SupportTicket::CATEGORY_TECHNICAL,
                'priority' => SupportTicket::PRIORITY_HIGH,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(5),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'I reset my password but now I can\'t log in. Says "Invalid credentials".', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Try clearing your browser cache and cookies, then try again with your new password.', 'is_internal' => false],
                    ['author_type' => 'user', 'body' => 'It works now! Thank you!', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 8,
                'subject' => 'Can\'t find my invoice',
                'category' => SupportTicket::CATEGORY_BILLING,
                'priority' => SupportTicket::PRIORITY_NORMAL,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(20),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'I need an invoice for my payment from May 1st. I don\'t see it in my account.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'I\'ve resent the invoice to your registered email. Please check your inbox and spam folder.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 9,
                'subject' => 'Webhook delivery failures',
                'category' => SupportTicket::CATEGORY_TECHNICAL,
                'priority' => SupportTicket::PRIORITY_HIGH,
                'status' => SupportTicket::STATUS_IN_PROGRESS,
                'assigned_to' => $admin?->id,
                'closed_at' => null,
                'messages' => [
                    ['author_type' => 'user', 'body' => 'My webhooks are failing to deliver. Getting 500 errors from my endpoint. All other APIs work fine.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'I\'ve checked our logs. Your endpoint responded with HTTP 500 three times in a row. Can you check your server logs?', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 10,
                'subject' => 'Two-factor authentication issues',
                'category' => SupportTicket::CATEGORY_TECHNICAL,
                'priority' => SupportTicket::PRIORITY_URGENT,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(2),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'I\'m locked out. Lost access to my 2FA device. Help!', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Don\'t worry. I\'ve temporarily disabled 2FA on your account. Please set it up again with a new device.', 'is_internal' => false],
                    ['author_type' => 'user', 'body' => 'All set! Thank you for the quick help.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 11,
                'subject' => 'Export data request',
                'category' => SupportTicket::CATEGORY_GENERAL,
                'priority' => SupportTicket::PRIORITY_NORMAL,
                'status' => SupportTicket::STATUS_OPEN,
                'assigned_to' => null,
                'closed_at' => null,
                'messages' => [
                    ['author_type' => 'user', 'body' => 'I need all my transaction data exported in CSV format for tax purposes.', 'is_internal' => false],
                ],
            ],
            [
                'user_id' => 12,
                'subject' => 'Subscription auto-renewal question',
                'category' => SupportTicket::CATEGORY_BILLING,
                'priority' => SupportTicket::PRIORITY_NORMAL,
                'status' => SupportTicket::STATUS_CLOSED,
                'assigned_to' => $admin?->id,
                'closed_at' => now()->subDays(35),
                'messages' => [
                    ['author_type' => 'user', 'body' => 'When does my Professional plan renew? I see a renewal date but want to make sure it\'s correct.', 'is_internal' => false],
                    ['author_type' => 'admin', 'body' => 'Your plan renews on June 12th. You can manage auto-renewal in your billing settings.', 'is_internal' => false],
                ],
            ],
        ];

        foreach ($ticketData as $data) {
            $messages = $data['messages'];
            unset($data['messages']);

            $ticket = SupportTicket::create([
                ...$data,
                'reference' => SupportTicket::generateReference(),
            ]);

            foreach ($messages as $messageData) {
                $authorType = $messageData['author_type'];
                $messageData = collect($messageData)->except('author_type')->toArray();

                if ($authorType === 'user') {
                    $message = $ticket->messages()->create([
                        ...$messageData,
                        'author_type' => User::class,
                        'author_id' => $ticket->user_id,
                    ]);
                } else {
                    $message = $ticket->messages()->create([
                        ...$messageData,
                        'author_type' => Admin::class,
                        'author_id' => $admin?->id,
                    ]);
                }

                // Stagger message timestamps
                $message->update([
                    'created_at' => now()->subDays(rand(1, 50))->addHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(1, 50))->addHours(rand(0, 23)),
                ]);
            }
        }
    }
}
