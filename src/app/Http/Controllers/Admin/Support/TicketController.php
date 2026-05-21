<?php

namespace App\Http\Controllers\Admin\Support;

use App\Domain\Notifications\Actions\Notify;
use App\Domain\Support\Models\SupportAttachment;
use App\Domain\Support\Models\SupportMessage;
use App\Domain\Support\Models\SupportTicket;
use App\Domain\Upload\FileUploader;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TicketController
{
    public function index(Request $request): View
    {
        $tickets = SupportTicket::with(['user', 'assignee'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('search'), fn ($q) => $q
                ->where('reference', 'like', '%'.$request->search.'%')
                ->orWhere('subject', 'like', '%'.$request->search.'%')
            )
            ->orderByDesc('updated_at')
            ->paginate(25)
            ->withQueryString();

        $counts = SupportTicket::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        $statusPills = [
            ['value' => '',            'label' => 'All',          'count' => $counts->sum()],
            ['value' => 'open',        'label' => 'Open',         'count' => $counts['open'] ?? 0],
            ['value' => 'in_progress', 'label' => 'In Progress',  'count' => $counts['in_progress'] ?? 0],
            ['value' => 'waiting',     'label' => 'Waiting',      'count' => $counts['waiting'] ?? 0],
            ['value' => 'closed',      'label' => 'Closed',       'count' => $counts['closed'] ?? 0],
        ];

        return view('admin.support.tickets.index', compact('tickets', 'statusPills'));
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['user', 'assignee', 'messages.author', 'messages.attachments']);
        $admins = Admin::orderBy('name')->get();

        return view('admin.support.tickets.show', compact('ticket', 'admins'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'body' => 'required|string|max:10000',
            'is_internal' => 'sometimes|boolean',
            'attachments' => 'sometimes|array|max:5',
            'attachments.*' => 'file|max:10240',
        ]);

        $admin = $request->user('admin');

        $message = new SupportMessage;
        $message->support_ticket_id = $ticket->id;
        $message->author_type = Admin::class;
        $message->author_id = $admin->id;
        $message->body = $request->input('body');
        $message->is_internal = $request->boolean('is_internal');
        $message->save();

        $this->handleAttachments($request, $message);

        if (! $message->is_internal) {
            if ($ticket->status === SupportTicket::STATUS_OPEN) {
                $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
                $ticket->save();
            }
            $this->notifyUser($ticket, 'admin replied to your ticket');
        }

        return redirect()->route('admin.support.tickets.show', $ticket)->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate(['status' => 'required|in:'.implode(',', SupportTicket::statuses())]);

        $ticket->status = $request->input('status');

        if ($ticket->status === SupportTicket::STATUS_CLOSED) {
            $ticket->closed_at = now();
        } else {
            $ticket->closed_at = null;
        }

        $ticket->save();

        $this->notifyUser($ticket, 'ticket status changed to '.$request->input('status'));

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function assign(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate(['assigned_to' => 'nullable|exists:admins,id']);
        $ticket->assigned_to = $request->input('assigned_to') ?: null;
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket assigned.');
    }

    public function close(SupportTicket $ticket): RedirectResponse
    {
        $ticket->status = SupportTicket::STATUS_CLOSED;
        $ticket->closed_at = now();
        $ticket->save();
        $this->notifyUser($ticket, 'ticket closed');

        return redirect()->route('admin.support.tickets.index')->with('success', 'Ticket closed.');
    }

    public function reopen(SupportTicket $ticket): RedirectResponse
    {
        $ticket->status = SupportTicket::STATUS_OPEN;
        $ticket->closed_at = null;
        $ticket->save();

        return redirect()->route('admin.support.tickets.show', $ticket)->with('success', 'Ticket reopened.');
    }

    public function attachment(Request $request, SupportAttachment $attachment): Response
    {
        $expires = (int) $request->query('expires', 0);
        $sig = $request->query('signature', '');

        if ($expires < now()->timestamp) {
            abort(410, 'Link expired.');
        }

        $expected = hash_hmac('sha256', (string) $attachment->id, config('app.key'));
        if (! hash_equals($expected, $sig)) {
            abort(403, 'Invalid signature.');
        }

        $path = storage_path('app/private/'.$attachment->path);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'attachment; filename="'.$attachment->original_name.'"',
        ]);
    }

    private function handleAttachments(Request $request, SupportMessage $message): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        $uploader = app(FileUploader::class);

        foreach ($request->file('attachments') as $file) {
            $meta = $uploader->store($file, 'tickets');

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

    private function notifyUser(SupportTicket $ticket, string $note): void
    {
        if ($ticket->isGuest()) {
            return;
        }

        $user = $ticket->user;
        if (! $user) {
            return;
        }

        Notify::send($user, 'ticket_update', [
            'ticket.reference' => $ticket->reference,
            'ticket.subject' => $ticket->subject,
            'ticket.url' => route('user.support.show', $ticket->reference),
            'update' => $note,
        ]);
    }
}
