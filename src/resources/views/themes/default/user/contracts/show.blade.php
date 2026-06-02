@extends('theme::layouts.user')

@section('breadcrumb', $contract->title)

@section('content')
<div class="space-y-5">


    @if(in_array($contract->status, ['editing', 'ready']))
        @include('theme::user.contracts._stepper', ['step' => 5])
    @endif


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


        <div class="flex flex-wrap items-center gap-2 lg:shrink-0">
            @if($contract->isEditable())
                @if(is_null($contract->template_id) && !empty($contract->field_data['brief']))
                <form method="POST" action="{{ route('user.contracts.ai.regenerate', $contract) }}" id="regenerate-form" data-no-page-loader>
                    @csrf
                    <button type="button"
                            onclick="confirmModal.show('@lang('Regenerate Contract')', '@lang('1 AI draft will be deducted. This will overwrite the current content.')', function(){ showRegenLoader(); document.getElementById('regenerate-form').submit(); })"
                            class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5 text-lime/80 hover:text-lime">
                        <x-heroicon-o-sparkles class="h-4 w-4" />
                        @lang('Regenerate')
                    </button>
                </form>
                @endif
                <a href="{{ route('user.contracts.pdf', $contract) }}" target="_blank"
                   class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    @lang('Download PDF')
                </a>
                <button type="button" id="save-contract-btn"
                        class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                    <x-heroicon-o-check class="h-4 w-4" />
                    @lang('Save')
                </button>
                <form method="POST" action="{{ route('user.contracts.finalize', $contract) }}">
                    @csrf
                    <button class="btn-lime rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                        <x-heroicon-o-lock-closed class="h-4 w-4" />
                        @lang('Finalize')
                    </button>
                </form>
            @endif

            @if($contract->isReady())
                <a href="{{ route('user.contracts.pdf', $contract) }}" target="_blank"
                   class="btn-ghost rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    @lang('Download PDF')
                </a>
                <form method="POST" action="{{ route('user.contracts.send-sign', $contract) }}">
                    @csrf
                    <button class="btn-lime rounded-lg px-3 py-2 text-[12px] flex items-center gap-1.5">
                        <x-heroicon-o-paper-airplane class="h-4 w-4" />
                        @lang('Send for Signature')
                    </button>
                </form>
                <form method="POST" action="{{ route('user.contracts.revert', $contract) }}">
                    @csrf
                    <button class="btn-ghost rounded-lg px-3 py-2 text-[12px] text-white/50 hover:text-white flex items-center gap-1.5">
                        <x-heroicon-o-pencil class="h-4 w-4" />
                        @lang('Edit')
                    </button>
                </form>
            @endif

            @if(in_array($contract->status, ['ready', 'editing', 'draft']))
                <form method="POST" action="{{ route('user.contracts.destroy', $contract) }}"
                      onsubmit="return confirm('@lang('Delete this contract? This cannot be undone.')')">
                    @csrf @method('DELETE')
                    <button class="btn-ghost rounded-lg px-3 py-2 text-[12px] text-red-400 hover:text-red-300 flex items-center gap-1.5">
                        <x-heroicon-o-trash class="h-4 w-4" />
                        @lang('Delete')
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



    @if($contract->status === 'generating')
    <div class="rounded-2xl glass p-12 text-center">
        <div class="inline-flex items-center gap-3 text-white/60">
            <svg class="animate-spin h-5 w-5 text-lime" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-sm">@lang('AI is generating your contract…')</span>
        </div>
    </div>
    @else


    <div class="grid lg:grid-cols-12 gap-5">


        <div class="lg:col-span-8 space-y-4">
            @if($contract->isEditable())
            <form id="contract-save-form" method="POST" action="{{ route('user.contracts.update', $contract) }}">
                @csrf @method('PUT')
                <div class="rounded-2xl glass overflow-hidden">
                    <div class="px-5 py-3 border-b border-white/8 flex items-center gap-2">
                        <x-heroicon-o-pencil-square class="h-4 w-4 text-white/40" />
                        <span class="text-[11px] font-mono uppercase tracking-widest text-white/40">@lang('Editor')</span>
                    </div>


                    <div class="px-5 pt-4">
                        <input type="text" name="title" value="{{ $contract->title }}"
                               class="ud-input font-display text-lg" :placeholder="__('Contract title…')" required />
                    </div>


                    <div class="px-5 py-4">
                        <input type="hidden" name="body_html" id="contract_body_input" value="{{ $contract->body_html }}">
                        <div class="prose-editor-init" data-input="contract_body_input"></div>
                    </div>
                </div>
            </form>
            @else

            <div class="rounded-2xl glass overflow-hidden">
                <div class="px-5 py-3 border-b border-white/8 flex items-center gap-2">
                    <x-heroicon-o-document-text class="h-4 w-4 text-white/40" />
                    <span class="text-[11px] font-mono uppercase tracking-widest text-white/40">@lang('Contract')</span>
                </div>
                <div class="px-6 py-6 contract-editor-body text-white/85 text-[14px] leading-relaxed">
                    <?php echo $contract->body_html ?>
                </div>
            </div>
            @endif
        </div>


        <div class="lg:col-span-4 space-y-4">


            <div class="rounded-2xl glass overflow-hidden">
                <div class="px-5 py-3 border-b border-white/8">
                    <h3 class="text-[11px] font-mono uppercase tracking-widest text-white/40">@lang('Parties')</h3>
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
                                <div class="text-[10px] text-lime mt-0.5">✓ @lang('Signed')</div>
                            @elseif($party->role === 'signer')
                                <div class="text-[10px] text-white/30 mt-0.5">@lang('Pending')</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>


            <div class="rounded-2xl glass p-5 space-y-3">
                <h3 class="text-[11px] font-mono uppercase tracking-widest text-white/40">@lang('Details')</h3>
                <div class="space-y-2 text-[12px]">
                    <div class="flex justify-between">
                        <span class="text-white/45">@lang('Template')</span>
                        <span class="text-white/80">{{ $contract->template->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/45">@lang('Credits used')</span>
                        <span class="text-white/80">{{ $contract->credits_used }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/45">@lang('Created')</span>
                        <span class="text-white/80">{{ $contract->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>


<div id="ai-regen-loader" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="max-w-md w-full mx-4 rounded-2xl glass p-8 text-center">
        <div class="relative mx-auto mb-6 h-20 w-20">
            <div class="absolute inset-0 rounded-full border-4 border-white/10"></div>
            <div class="absolute inset-0 rounded-full border-4 border-lime border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <x-heroicon-o-sparkles class="h-9 w-9 text-lime animate-pulse" />
            </div>
        </div>
        <h2 class="text-lg font-bold text-white">@lang('Regenerating your contract…')</h2>
        <p id="ai-regen-step" class="mt-2 text-sm text-white/55">@lang('Reviewing your brief')</p>
        <div class="mt-5 flex items-center justify-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-lime animate-bounce" class="delay-0"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-lime animate-bounce" class="delay-150"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-lime animate-bounce" class="delay-300"></span>
        </div>
        <p class="mt-5 text-xs text-white/40">{{ __('This may take 15–60 seconds. Please don\'t close this window.') }}</p>
    </div>
</div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/editor.css') }}">
<script src="{{ asset('assets/static/js/editor.js') }}"></script>
@endpush

@push('scripts')
<script>
$(function () {
    'use strict';
    document.querySelectorAll('.prose-editor-init').forEach(function (el) {
        ProseEditor.init(el);
    });

    $('#save-contract-btn').on('click', function () {
        $('#contract-save-form').submit();
    });
});

function showRegenLoader() {
    var loader = document.getElementById('ai-regen-loader');
    var step   = document.getElementById('ai-regen-step');
    if (!loader) return;
    loader.classList.remove('hidden');
    loader.classList.add('flex');
    document.body.style.overflow = 'hidden';
    var steps = [
        @json(__('Reviewing your brief')),
        @json(__('Drafting clauses')),
        @json(__('Adding protective terms')),
        @json(__('Finalizing the agreement'))
    ];
    var i = 0;
    setInterval(function () {
        i = (i + 1) % steps.length;
        if (step) step.textContent = steps[i];
    }, 3500);
}
</script>
@endpush
