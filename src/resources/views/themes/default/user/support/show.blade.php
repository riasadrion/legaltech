@extends('theme::layouts.user')

@section('breadcrumb', '#' . $ticket->reference)

@push('scripts')
<script>
$(function () {
    'use strict';

    $(document).on('change', '[data-file-list]', function () {
        var $list = $('#' + $(this).data('file-list'));
        $list.empty();
        Array.from(this.files).forEach(function (f) {
            var $li = $('<li>').html('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>'
                + f.name + ' <span class="file-size-label">(' + (f.size / 1024).toFixed(0) + ' KB)</span>');
            $list.append($li);
        });
    });
});
</script>
@endpush

@section('content')
<div class="space-y-6">


    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="ud-page-title">{{ $ticket->subject }}</h1>
            <p class="ud-page-sub">
                @lang('Ticket') #{{ $ticket->reference }}
                · {{ __(ucfirst(str_replace('_', ' ', $ticket->category))) }}
                · {{ $ticket->created_at->format('d M Y') }}
            </p>
        </div>
        @php
            $sc = match($ticket->status) {
                'open'        => 'ud-badge-green',
                'in_progress' => 'ud-badge-primary',
                'waiting'     => 'ud-badge-amber',
                'closed'      => 'ud-badge-gray',
                default       => 'ud-badge-gray',
            };
        @endphp
        <span class="ud-badge {{ $sc }} shrink-0">{{ __(ucfirst(str_replace('_', ' ', $ticket->status))) }}</span>
    </div>


    <div class="ud-card divide-y divide-white/6">
        @foreach($ticket->messages as $msg)
        @php $isUser = $msg->author_type === \App\Models\User::class; @endphp
        <div class="px-4 py-3 space-y-1.5 {{ $isUser ? '' : 'bg-white/2' }}">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $isUser ? 'bg-primary/15' : 'bg-violet/15' }} text-xs font-bold {{ $isUser ? 'text-primary' : 'text-violet-400' }}">
                        {{ strtoupper(substr($msg->author?->name ?? 'S', 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium text-white">
                        {{ $isUser ? ($msg->author?->name ?? __('You')) : __('Support Team') }}
                    </span>
                    @if(!$isUser)
                        <span class="ud-badge ud-badge-violet text-xs">@lang('Staff')</span>
                    @endif
                </div>
                <span class="text-xs text-white/35">{{ $msg->created_at->diffForHumans() }}</span>
            </div>
            <div class="text-sm text-white/75 leading-relaxed pl-9 whitespace-pre-wrap">{{ $msg->body }}</div>
            @if($msg->attachments->isNotEmpty())
            <div class="pl-9 flex flex-wrap gap-2 pt-1">
                @foreach($msg->attachments as $att)
                <a href="{{ upload_url($att->path) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-white/6 border border-white/10 px-3 py-1.5 text-xs text-white/70 hover:bg-white/10 transition-colors">
                    <x-heroicon-o-paper-clip class="h-3.5 w-3.5" />
                    {{ $att->original_name }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>


    @if($ticket->isOpen())
    <div class="ud-card">
        <form method="POST" action="{{ route('user.support.reply', $ticket->reference) }}"
              enctype="multipart/form-data" class="px-4 py-4 space-y-3">
            <h2 class="ud-card-title">@lang('Reply')</h2>
            @csrf
            <div>
                <textarea name="body" rows="5" class="ud-input resize-none"
                          placeholder="{{ __('Write your reply...') }}" required>{{ old('body') }}</textarea>
                @error('body') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ud-dropzone" id="dropzone-reply">
                    <input type="file" name="attachments[]" multiple class="sr-only" id="file-reply"
                           accept=".jpg,.jpeg,.png,.webp,.pdf,.txt" data-file-list="file-list-reply" />
                    <x-heroicon-o-paper-clip class="h-5 w-5 text-white/30" />
                    <span class="ud-dropzone-text">@lang('Attach files')</span>
                    <span class="ud-dropzone-hint">@lang('JPG, PNG, PDF, TXT — max 10 MB each')</span>
                </label>
                <ul id="file-list-reply" class="ud-file-list"></ul>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="ud-btn-primary inline-flex">
                    <x-heroicon-o-paper-airplane class="h-4 w-4" />
                    @lang('Send Reply')
                </button>
                <form method="POST" action="{{ route('user.support.close', $ticket->reference) }}" class="inline">
                    @csrf
                    <button type="submit" class="ud-btn-ghost inline-flex text-red-400 hover:text-red-300"
                            :data-confirm="__('Close this ticket?')">
                        <x-heroicon-o-x-circle class="h-4 w-4" />
                        @lang('Close Ticket')
                    </button>
                </form>
            </div>
        </form>
    </div>
    @else
    <div class="ud-card p-5 flex items-center gap-3 text-white/50 text-sm">
        <x-heroicon-o-lock-closed class="h-4 w-4 shrink-0" />
        @lang('This ticket is closed. Open a new ticket if you need further help.')
        <a href="{{ route('user.support.create') }}" class="ml-auto ud-btn-ghost inline-flex text-xs">@lang('New Ticket')</a>
    </div>
    @endif

</div>
@endsection
