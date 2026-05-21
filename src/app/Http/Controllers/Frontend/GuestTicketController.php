<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Support\Models\SupportMessage;
use App\Domain\Support\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuestTicketController
{
    public function create(): View
    {
        return view('theme::support.guest-create');
    }

    public function store(Request $request): View|RedirectResponse
    {
        $request->validate([
            'guest_name' => 'required|string|max:100',
            'guest_email' => 'required|email|max:200',
            'subject' => 'required|string|max:200',
            'category' => 'required|in:'.implode(',', SupportTicket::categories()),
            'body' => 'required|string|max:10000',
        ]);

        $token = Str::random(64);

        $ticket = new SupportTicket;
        $ticket->reference = SupportTicket::generateReference();
        $ticket->guest_name = $request->input('guest_name');
        $ticket->guest_email = $request->input('guest_email');
        $ticket->guest_verify_token = $token;
        $ticket->guest_verified = false;
        $ticket->subject = $request->input('subject');
        $ticket->category = $request->input('category');
        $ticket->priority = SupportTicket::PRIORITY_NORMAL;
        $ticket->status = SupportTicket::STATUS_OPEN;
        $ticket->save();

        $message = new SupportMessage;
        $message->support_ticket_id = $ticket->id;
        $message->body = $request->input('body');
        $message->is_internal = false;
        $message->save();

        $this->sendVerificationEmail($ticket, $token);

        return view('theme::support.guest-pending', compact('ticket'));
    }

    public function verify(Request $request, string $token): View|RedirectResponse
    {
        $ticket = SupportTicket::where('guest_verify_token', $token)
            ->where('guest_verified', false)
            ->first();

        if (! $ticket) {
            return redirect()->route('support.guest.create')
                ->with('error', 'Invalid or expired verification link.');
        }

        $ticket->guest_verified = true;
        $ticket->save();

        return view('theme::support.guest-verified', compact('ticket'));
    }

    public function show(Request $request, string $reference): View
    {
        $email = $request->query('email');
        $ticket = SupportTicket::where('reference', $reference)
            ->where('guest_email', $email)
            ->where('guest_verified', true)
            ->firstOrFail();

        $ticket->load(['messages' => fn ($q) => $q->where('is_internal', false)->with(['author', 'attachments'])]);

        return view('theme::support.guest-show', compact('ticket'));
    }

    public function reply(Request $request, string $reference): RedirectResponse
    {
        $email = $request->input('email');
        $ticket = SupportTicket::where('reference', $reference)
            ->where('guest_email', $email)
            ->where('guest_verified', true)
            ->firstOrFail();

        if ($ticket->isClosed()) {
            return redirect()->back()->with('error', 'This ticket is closed.');
        }

        $request->validate(['body' => 'required|string|max:10000']);

        $message = new SupportMessage;
        $message->support_ticket_id = $ticket->id;
        $message->body = $request->input('body');
        $message->is_internal = false;
        $message->save();

        $ticket->status = SupportTicket::STATUS_WAITING;
        $ticket->save();

        return redirect()->route('support.guest.show', ['reference' => $reference, 'email' => $email])
            ->with('success', 'Reply sent.');
    }

    private function sendVerificationEmail(SupportTicket $ticket, string $token): void
    {
        $verifyUrl = route('support.guest.verify', $token);
        $appName = config('app.name');

        Mail::send([], [], function (Message $msg) use ($ticket, $verifyUrl, $appName) {
            $msg->to($ticket->guest_email, $ticket->guest_name)
                ->subject("Verify your support ticket #{$ticket->reference} — {$appName}")
                ->html(
                    "<p>Hi {$ticket->guest_name},</p>".
                    '<p>Thank you for contacting support. Click the link below to verify your email and activate your ticket:</p>'.
                    "<p><a href=\"{$verifyUrl}\" style=\"display:inline-block;background:#1e40af;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;\">Verify Email &amp; View Ticket</a></p>".
                    "<p>Ticket reference: <strong>#{$ticket->reference}</strong></p>".
                    "<p>Subject: {$ticket->subject}</p>"
                );
        });
    }
}
