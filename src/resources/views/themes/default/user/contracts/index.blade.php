@extends('theme::layouts.user')

@section('breadcrumb', __('Contracts'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="ud-page-title">@lang('Contracts')</h1>
            <p class="ud-page-sub">@lang('Your generated and signed contracts.')</p>
        </div>
        <a href="{{ route('user.contracts.create') }}" class="btn-primary rounded-xl px-4 py-2.5 text-[13px] flex items-center gap-1.5">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('New Contract')
        </a>
    </div>

    @if($contracts->isEmpty())
    <div class="rounded-2xl glass p-14 text-center">
        <x-heroicon-o-document-text class="h-10 w-10 mx-auto mb-3 text-white/20" />
        <div class="text-white/50 text-sm">@lang('No contracts yet — generate your first one.')</div>
        <a href="{{ route('user.contracts.create') }}" class="btn-primary rounded-xl px-4 py-2 text-[13px] inline-flex items-center gap-1.5 mt-5">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('New Contract')
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($contracts as $contract)
        @php
            $pillMap = [
                'draft'   => 'pill-draft',
                'editing' => 'pill-review',
                'ready'   => 'pill-done',
                'sent'    => 'pill-review',
                'signed'  => 'pill-sign',
                'expired' => 'pill-draft',
            ];
            $pill = $pillMap[$contract->status] ?? 'pill-done';
        @endphp
        <a href="{{ route('user.contracts.show', $contract) }}"
           class="rounded-2xl glass p-5 flex flex-col gap-3 hover:bg-white/6 transition group">
            <div class="flex items-start justify-between gap-2">
                <div class="w-9 h-9 rounded-xl bg-white/6 border border-white/10 grid place-items-center shrink-0">
                    <x-heroicon-o-document-text class="h-4 w-4 text-white/50" />
                </div>
                <span class="pill {{ $pill }}">{{ __(ucfirst($contract->status)) }}</span>
            </div>
            <div>
                <div class="font-display text-lg text-white leading-tight">{{ $contract->title }}</div>
                <div class="text-[11px] text-white/40 mt-1">{{ $contract->template->name ?? '—' }}</div>
            </div>
            <div class="mt-auto flex items-center justify-between text-[11px] text-white/35">
                <span>{{ $contract->created_at->format('d M Y') }}</span>
                <x-heroicon-o-arrow-right class="h-3.5 w-3.5 group-hover:text-primary transition" />
            </div>
        </a>
        @endforeach
    </div>

    @if($contracts->hasPages())
    <div class="mt-2">{{ $contracts->links() }}</div>
    @endif
    @endif

</div>
@endsection
