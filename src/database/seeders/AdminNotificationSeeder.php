<?php

namespace Database\Seeders;

use App\Domain\Payment\Models\Payment;
use App\Models\SupportTicket;
use App\Models\Admin;
use App\Models\AdminNotification;
use Illuminate\Database\Seeder;

class AdminNotificationSeeder extends Seeder
{
    public function run(): void
    {
        AdminNotification::truncate();

        $admins = Admin::all();
        if ($admins->isEmpty()) {
            $this->command->warn('No admins found — skipping.');
            return;
        }

        $rows = [];

        Payment::with('user')->latest()->take(5)->get()->each(function ($payment) use ($admins, &$rows) {
            $name = $payment->user?->name ?? 'A user';
            foreach ($admins as $admin) {
                $rows[] = [
                    'admin_id'   => $admin->id,
                    'type'       => 'payment',
                    'title'      => 'New payment request',
                    'body'       => $name.' submitted $'.number_format($payment->amount, 2).' via '.optional($payment->gateway)->name,
                    'url'        => route('admin.payments.show', $payment),
                    'read_at'    => null,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ];
            }
        });

        SupportTicket::with('user')->latest()->take(5)->get()->each(function ($ticket) use ($admins, &$rows) {
            $name = $ticket->user?->name ?? 'A user';
            foreach ($admins as $admin) {
                $rows[] = [
                    'admin_id'   => $admin->id,
                    'type'       => 'support',
                    'title'      => 'New support ticket #'.$ticket->reference,
                    'body'       => $name.': '.$ticket->subject,
                    'url'        => route('admin.support.tickets.show', $ticket),
                    'read_at'    => null,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                ];
            }
        });

        // Sort by created_at desc so newest appear first
        usort($rows, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        AdminNotification::insert($rows);

        $this->command->info('Seeded '.count($rows).' admin notifications.');
    }
}
