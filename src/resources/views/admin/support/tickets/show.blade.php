@extends('admin.layouts.app')

@section('title', 'Ticket #' . $ticket->reference)

@section('content')
<x-page-header :title="'Ticket #' . $ticket->reference . ' — ' . $ticket->subject" back="{{ route('admin.support.tickets.index') }}" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Thread --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($ticket->messages as $msg)
        @php
            if ($msg->is_internal) {
                $cardClass = 'border-yellow-300 bg-yellow-50 dark:border-yellow-700/50 dark:bg-yellow-900/20';
                $textClass  = 'text-gray-800 dark:text-yellow-100';
                $borderClass = 'border-yellow-200 dark:border-yellow-700/40';
            } elseif ($msg->isFromAdmin()) {
                $cardClass = 'border-blue-200 bg-blue-50 dark:border-blue-700/50 dark:bg-blue-900/20';
                $textClass  = 'text-gray-800 dark:text-blue-100';
                $borderClass = 'border-blue-200 dark:border-blue-700/40';
            } else {
                $cardClass = 'border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-800';
                $textClass  = 'text-gray-800 dark:text-slate-200';
                $borderClass = 'border-gray-200 dark:border-slate-700';
            }
        @endphp
        <div class="rounded-xl border {{ $cardClass }} p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full {{ $msg->isFromAdmin() ? 'bg-blue-600' : 'bg-gray-400 dark:bg-slate-600' }} flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($msg->getAuthorName(), 0, 1)) }}
                    </span>
                    <span class="text-sm font-medium text-gray-900 dark:text-slate-100">{{ $msg->getAuthorName() }}</span>
                    @if($msg->is_internal)
                    <span class="text-xs bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 px-2 py-0.5 rounded-full">Internal Note</span>
                    @endif
                </div>
                <span class="text-xs text-gray-400 dark:text-slate-500">{{ formatDate($msg->created_at) }}</span>
            </div>
            <div class="prose prose-sm max-w-none {{ $textClass }}">
                <x-safe-html :content="$msg->body" />
            </div>
            @if($msg->attachments->isNotEmpty())
            <div class="mt-3 pt-3 border-t {{ $borderClass }} flex flex-wrap gap-2">
                @foreach($msg->attachments as $att)
                <a href="{{ $att->signedUrl() }}" target="_blank"
                    class="flex items-center gap-1.5 text-xs text-brand-600 hover:text-brand-700 bg-white dark:bg-slate-700 dark:hover:bg-slate-600 border border-gray-200 dark:border-slate-600 rounded px-2.5 py-1.5">
                    <x-heroicon-o-paper-clip class="w-3.5 h-3.5" />
                    {{ $att->original_name }} <span class="text-gray-400 dark:text-slate-400">({{ $att->formattedSize() }})</span>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach

        {{-- Reply form --}}
        @if($ticket->isOpen())
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-100 mb-3">Reply</h3>
            <form method="POST" action="{{ route('admin.support.tickets.reply', $ticket) }}" enctype="multipart/form-data">
                @csrf
                <textarea name="body" rows="6" required placeholder="Type your reply…"
                    class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500 @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
                @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <div class="mt-3">
                    <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.txt,.zip"
                        class="text-sm text-gray-600 dark:text-slate-400">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Up to 5 files, 10 MB each.</p>
                </div>

                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Send Reply</button>
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-slate-400">
                        <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 dark:border-slate-600 text-yellow-500">
                        Internal note (not visible to user)
                    </label>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        {{-- Details --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 space-y-3 text-sm">
            <h3 class="font-semibold text-gray-900 dark:text-slate-100">Details</h3>
            <div><span class="text-gray-500 dark:text-slate-400">From</span><div class="font-medium dark:text-slate-200">{{ $ticket->getAuthorName() }}</div><div class="text-gray-400 dark:text-slate-500 text-xs">{{ $ticket->getAuthorEmail() }}</div></div>
            <div><span class="text-gray-500 dark:text-slate-400">Category</span><div class="font-medium dark:text-slate-200">{{ ucfirst($ticket->category) }}</div></div>
            <div><span class="text-gray-500 dark:text-slate-400">Priority</span><div class="font-medium dark:text-slate-200">{{ ucfirst($ticket->priority) }}</div></div>
            <div><span class="text-gray-500 dark:text-slate-400">Opened</span><div class="font-medium dark:text-slate-200">{{ formatDate($ticket->created_at) }}</div></div>
            @if($ticket->closed_at)
            <div><span class="text-gray-500 dark:text-slate-400">Closed</span><div class="font-medium dark:text-slate-200">{{ formatDate($ticket->closed_at) }}</div></div>
            @endif
        </div>

        {{-- Status --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
            <h3 class="font-semibold text-gray-900 dark:text-slate-100 mb-3 text-sm">Change Status</h3>
            <form method="POST" action="{{ route('admin.support.tickets.status', $ticket) }}">
                @csrf @method('PATCH')
                <select name="status" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm mb-3">
                    @foreach(\App\Domain\Support\Models\SupportTicket::statuses() as $s)
                    <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600">Update Status</button>
            </form>
        </div>

        {{-- Assign --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
            <h3 class="font-semibold text-gray-900 dark:text-slate-100 mb-3 text-sm">Assign To</h3>
            <form method="POST" action="{{ route('admin.support.tickets.assign', $ticket) }}">
                @csrf @method('PATCH')
                <select name="assigned_to" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm mb-3">
                    <option value="">Unassigned</option>
                    @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600">Assign</button>
            </form>
        </div>

        {{-- Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 space-y-2">
            @if($ticket->isOpen())
            <form method="POST" action="{{ route('admin.support.tickets.close', $ticket) }}">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600"
                    data-confirm="Close this ticket?" data-confirm-title="Close ticket">Close Ticket</button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.support.tickets.reopen', $ticket) }}">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600">Reopen Ticket</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
