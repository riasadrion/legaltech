<?php

namespace App\Http\Controllers\User;

use App\Actions\Notify;
use App\Domain\Support\Models\SupportAttachment;
use App\Domain\Support\Models\SupportMessage;
use App\Domain\Support\Models\SupportTicket;
use App\Services\FileUploader;
use App\Models\User;
use App\Support\AdminNotify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController
{
    public function index(): View
    {
        $user = auth('user')->user();
        $tickets = SupportTicket::forUser($user->id)
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('theme::user.support.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('theme::user.support.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'category' => 'required|in:'.implode(',', SupportTicket::categories()),
            'body' => 'required|string|max:10000',
            'attachments' => 'sometimes|array|max:5',
            'attachments.*' => 'file|max:10240',
        ]);

        $user = auth('user')->user();

        $ticket = new SupportTicket;
        $ticket->reference = SupportTicket::generateReference();
        $ticket->user_id = $user->id;
        $ticket->subject = $request->input('subject');
        $ticket->category = $request->input('category');
        $ticket->priority = SupportTicket::PRIORITY_NORMAL;
        $ticket->status = SupportTicket::STATUS_OPEN;
        $ticket->save();

        $message = new SupportMessage;
        $message->support_ticket_id = $ticket->id;
        $message->author_type = User::class;
        $message->author_id = $user->id;
        $message->body = $request->input('body');
        $message->is_internal = false;
        $message->save();

        $this->handleAttachments($request, $message);

        Notify::send($user, 'ticket_created', [
            'ticket.reference' => $ticket->reference,
            'ticket.subject' => $ticket->subject,
            'ticket.url' => route('user.support.show', $ticket->reference),
        ]);

        AdminNotify::all(
            'New support ticket #'.$ticket->reference,
            $user->name.': '.$ticket->subject,
            route('admin.support.tickets.show', $ticket)
        );

        return redirect()->route('user.support.show', $ticket->reference)
            ->with('success', 'Ticket #'.$ticket->reference.' created successfully.');
    }

    public function show(string $reference): View
    {
        $user = auth('user')->user();
        $ticket = SupportTicket::where('reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $ticket->load(['messages' => fn ($q) => $q->where('is_internal', false)->with(['author', 'attachments'])]);

        return view('theme::user.support.show', compact('ticket'));
    }

    public function reply(Request $request, string $reference): RedirectResponse
    {
        $user = auth('user')->user();
        $ticket = SupportTicket::where('reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($ticket->isClosed()) {
            return redirect()->back()->with('error', 'This ticket is closed. Please open a new ticket.');
        }

        $request->validate([
            'body' => 'required|string|max:10000',
            'attachments' => 'sometimes|array|max:5',
            'attachments.*' => 'file|max:10240',
        ]);

        $message = new SupportMessage;
        $message->support_ticket_id = $ticket->id;
        $message->author_type = User::class;
        $message->author_id = $user->id;
        $message->body = $request->input('body');
        $message->is_internal = false;
        $message->save();

        $this->handleAttachments($request, $message);

        $ticket->status = SupportTicket::STATUS_WAITING;
        $ticket->save();

        return redirect()->route('user.support.show', $reference)->with('success', 'Reply sent.');
    }

    public function close(string $reference): RedirectResponse
    {
        $user = auth('user')->user();
        $ticket = SupportTicket::where('reference', $reference)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $ticket->status = SupportTicket::STATUS_CLOSED;
        $ticket->closed_at = now();
        $ticket->save();

        return redirect()->route('user.support.show', $reference)->with('success', 'Ticket closed.');
    }

    private function handleAttachments(Request $request, SupportMessage $message): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        $uploader = app(FileUploader::class);

        foreach ($request->file('attachments') as $file) {
            $meta = $uploader->store($file, 'support');

            $attachment = new SupportAttachment;
            $attachment->support_message_id = $message->id;
            $attachment->original_name = $meta->originalName;
            $attachment->disk = $meta->disk;
            $attachment->path = $meta->path;
            $attachment->mime_type = $meta->mime;
            $attachment->size = $meta->size;
            $attachment->save();
        }
    }
}
