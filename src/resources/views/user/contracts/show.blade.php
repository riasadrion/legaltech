@extends('theme::layouts.user')

@section('breadcrumb', $contract->title)

@section('content')
<div class="space-y-5">

    {{-- Stepper (only for fresh contracts in editing state) --}}
    @if(in_array($contract->status, ['editing', 'ready']))
        @include('user.contracts._stepper', ['step' => 5])
    @endif

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div class="flex items-start gap-3 min-w-0">
            <a href="{{ route('user.contracts.index') }}"
               class="flex items-center justify-center h-8 w-8 rounded-lg glass border border-white/10 text-white/60 hover:text-white transition shrink-0 mt-1">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div class="min-w-0">
                <h1 class="ud-page-title !mb-0 break-words">{{ $contract->title }}</h1>
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1 text-[12px] text-white/45">
                    <span>{{ $contract->template->name ?? '—' }}</span>
                    <span class="hidden sm:inline">·</span>
                    <span>{{ $contract->created_at->format('d M Y') }}</span>
                    @php
                        $pillMap = ['draft'=>'pill-draft','generating'=>'pill-review','editing'=>'pill-review','ready'=>'pill-done','sent'=>'pill-review','signed'=>'pill-sign','expired'=>'pill-draft'];
                    @endphp
                    <span class="pill {{ $pillMap[$contract->status] ?? 'pill-done' }}">{{ __(ucfirst($contract->status)) }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-2 lg:shrink-0">
            @if($contract->isEditable())
                <a href="{{ route('user.contracts.pdf', $contract) }}" target="_blank"
                   class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    {{ __('Download PDF') }}
                </a>
                <button type="button" id="save-contract-btn"
                        class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                    <x-heroicon-o-check class="h-4 w-4" />
                    {{ __('Save') }}
                </button>
                <form method="POST" action="{{ route('user.contracts.finalize', $contract) }}">
                    @csrf
                    <button class="btn-lime rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                        <x-heroicon-o-lock-closed class="h-4 w-4" />
                        {{ __('Finalize') }}
                    </button>
                </form>
            @endif

            @if($contract->isReady())
                <a href="{{ route('user.contracts.pdf', $contract) }}" target="_blank"
                   class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    {{ __('Download PDF') }}
                </a>
                <form method="POST" action="{{ route('user.contracts.send-sign', $contract) }}">
                    @csrf
                    <button class="btn-lime rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                        <x-heroicon-o-paper-airplane class="h-4 w-4" />
                        {{ __('Send for Signature') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('user.contracts.revert', $contract) }}">
                    @csrf
                    <button class="btn-ghost rounded-lg px-3 py-2 text-[12px] text-white/50 hover:text-white flex items-center gap-1.5">
                        <x-heroicon-o-pencil class="h-4 w-4" />
                        {{ __('Edit') }}
                    </button>
                </form>
            @endif

            @if(in_array($contract->status, ['ready', 'editing', 'draft']))
                <form method="POST" action="{{ route('user.contracts.destroy', $contract) }}"
                      onsubmit="return confirm('{{ __('Delete this contract? This cannot be undone.') }}')">
                    @csrf @method('DELETE')
                    <button class="btn-ghost rounded-lg px-3 py-2 text-[12px] text-red-400 hover:text-red-300 flex items-center gap-1.5">
                        <x-heroicon-o-trash class="h-4 w-4" />
                        {{ __('Delete') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-[13px] text-red-300">
        @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
    </div>
    @endif

    @if(session('success'))
    <div class="rounded-xl border border-lime/30 bg-lime/10 px-4 py-3 text-[13px] text-lime">{{ session('success') }}</div>
    @endif

    {{-- Generating state --}}
    @if($contract->status === 'generating')
    <div class="rounded-2xl glass p-12 text-center">
        <div class="inline-flex items-center gap-3 text-white/60">
            <svg class="animate-spin h-5 w-5 text-lime" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-sm">{{ __('AI is generating your contract…') }}</span>
        </div>
    </div>
    @else

    {{-- Two-column layout: editor + sidebar --}}
    <div class="grid lg:grid-cols-12 gap-5">

        {{-- Editor / Preview --}}
        <div class="lg:col-span-8 space-y-4">
            @if($contract->isEditable())
            <form id="contract-save-form" method="POST" action="{{ route('user.contracts.update', $contract) }}">
                @csrf @method('PUT')
                <div class="rounded-2xl glass overflow-hidden">
                    <div class="px-5 py-3 border-b border-white/8 flex items-center gap-2">
                        <x-heroicon-o-pencil-square class="h-4 w-4 text-white/40" />
                        <span class="text-[11px] font-mono uppercase tracking-widest text-white/40">{{ __('Editor') }}</span>
                    </div>

                    {{-- Title --}}
                    <div class="px-5 pt-4">
                        <input type="text" name="title" value="{{ $contract->title }}"
                               class="ud-input font-display text-lg" placeholder="{{ __('Contract title…') }}" required />
                    </div>

                    {{-- Rich text body (ProseEditor) --}}
                    <div class="px-5 py-4">
                        <input type="hidden" name="body_html" id="contract_body_input" value="{{ $contract->body_html }}">
                        <div class="prose-editor-init" data-input="contract_body_input"></div>
                    </div>
                </div>
            </form>
            @else
            {{-- Read-only view --}}
            <div class="rounded-2xl glass overflow-hidden">
                <div class="px-5 py-3 border-b border-white/8 flex items-center gap-2">
                    <x-heroicon-o-document-text class="h-4 w-4 text-white/40" />
                    <span class="text-[11px] font-mono uppercase tracking-widest text-white/40">{{ __('Contract') }}</span>
                </div>
                <div class="px-6 py-6 contract-editor-body text-white/85 text-[14px] leading-relaxed">
                    {!! $contract->body_html !!}
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-4">

            {{-- Parties --}}
            <div class="rounded-2xl glass overflow-hidden">
                <div class="px-5 py-3 border-b border-white/8">
                    <h3 class="text-[11px] font-mono uppercase tracking-widest text-white/40">{{ __('Parties') }}</h3>
                </div>
                <div class="divide-y divide-white/6">
                    @foreach($contract->parties as $party)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-[13px] text-white">{{ $party->name }}</div>
                            <div class="text-[11px] text-white/40">{{ $party->email }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-[10px] font-mono uppercase text-white/30">{{ ucfirst($party->role) }}</div>
                            @if($party->hasSigned())
                                <div class="text-[10px] text-lime mt-0.5">✓ {{ __('Signed') }}</div>
                            @elseif($party->role === 'signer')
                                <div class="text-[10px] text-white/30 mt-0.5">{{ __('Pending') }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Details --}}
            <div class="rounded-2xl glass p-5 space-y-3">
                <h3 class="text-[11px] font-mono uppercase tracking-widest text-white/40">{{ __('Details') }}</h3>
                <div class="space-y-2 text-[12px]">
                    <div class="flex justify-between">
                        <span class="text-white/45">{{ __('Template') }}</span>
                        <span class="text-white/80">{{ $contract->template->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/45">{{ __('Credits used') }}</span>
                        <span class="text-white/80">{{ $contract->credits_used }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/45">{{ __('Created') }}</span>
                        <span class="text-white/80">{{ $contract->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/editor.css') }}">
<script src="{{ asset('assets/static/js/editor.js') }}"></script>
@endpush

@push('scripts')
<script>
$(function () {
    document.querySelectorAll('.prose-editor-init').forEach(function (el) {
        ProseEditor.init(el);
    });

    $('#save-contract-btn').on('click', function () {
        $('#contract-save-form').submit();
    });
});
</script>
@endpush
