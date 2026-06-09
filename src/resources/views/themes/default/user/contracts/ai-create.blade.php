@extends('theme::layouts.user')

@section('breadcrumb', __('AI Custom Contract'))

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-start gap-3">
        <a href="{{ route('user.contracts.create') }}"
           class="flex items-center justify-center h-9 w-9 rounded-xl glass border border-white/10 text-white/60 hover:text-white transition shrink-0 mt-1">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="ud-page-title !mb-0 flex items-center gap-2">
                <x-heroicon-o-sparkles class="h-5 w-5 text-primary" />
                @lang('AI Custom Contract')
            </h1>
            <p class="text-[12px] text-white/60 mt-1">
                @lang('Describe the contract you need. The AI drafts the full agreement, ready to review, edit, and sign.')
                · <span class="text-primary/80">@lang('1 AI draft')</span>
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-[13px] text-red-300">
            <ul class="space-y-0.5 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="ai-contract-form" method="POST" action="{{ route('user.contracts.ai.store') }}" class="space-y-5" data-no-page-loader>
        @csrf


        <div class="rounded-2xl glass overflow-hidden">
            <div class="px-5 py-3 border-b border-white/8 flex items-center gap-2">
                <x-heroicon-o-sparkles class="h-4 w-4 text-primary" />
                <span class="text-[12px] font-mono uppercase tracking-widest text-white/50">@lang('Your Brief')</span>
            </div>
            <div class="px-5 py-5 space-y-3">
                <textarea id="brief-input" name="brief" rows="6"
                          required minlength="20"
                          placeholder="{{ __('e.g. Mutual NDA between Acme Inc (Delaware) and BetaCorp (UK), covering trade secrets and product designs, 3-year term, governed by California law.') }}"
                          class="ud-input">{{ old('brief', request('brief')) }}</textarea>

                <div class="flex flex-wrap gap-1.5">
                    @foreach([
                        __('Mutual NDA · UK law · 3-year term'),
                        __('SaaS subscription · EU customer · monthly billing'),
                        __('Reseller agreement · 24 months · 20% commission'),
                        __('SOW for web development · fixed-price · 60-day delivery'),
                        __('Employment offer · remote · 90-day probation'),
                    ] as $sample)
                        <button type="button" data-sample="{{ $sample }}"
                                class="sample-btn text-[11px] rounded-full bg-white/4 border border-white/10 px-2.5 py-1 hover:bg-white/8 text-white/70 hover:text-white transition">
                            {{ $sample }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>


        <div class="rounded-2xl glass overflow-hidden">
            <div class="px-5 py-3 border-b border-white/8 flex items-center justify-between">
                <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">@lang('Parties')</h2>
                <button type="button" id="add-party-btn"
                        class="text-[12px] text-primary hover:underline flex items-center gap-1">
                    <x-heroicon-o-plus class="h-3.5 w-3.5" />
                    @lang('Add party')
                </button>
            </div>
            <div class="px-5 py-5 space-y-4" id="parties-list">
                @php $existingParties = old('parties', [[]]); if (empty($existingParties)) $existingParties = [[]]; @endphp
                @foreach($existingParties as $i => $p)
                    <div class="party-row space-y-3 {{ $i > 0 ? 'pt-4 border-t border-white/8' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="text-[11px] font-mono text-white/35 uppercase tracking-widest">@lang('Party') {{ $i + 1 }}</div>
                            @if($i > 0)
                                <button type="button" class="remove-party text-red-400 hover:text-red-300 text-[11px]">@lang('Remove')</button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="parties[{{ $i }}][name]" placeholder="{{ __('Full name') }}" required class="ud-input" value="{{ $p['name'] ?? '' }}" />
                            <input type="email" name="parties[{{ $i }}][email]" placeholder="{{ __('Email address') }}" required class="ud-input" value="{{ $p['email'] ?? '' }}" />
                        </div>
                        <select name="parties[{{ $i }}][role]" class="ud-input">
                            <option value="signer" {{ ($p['role'] ?? 'signer') !== 'viewer' ? 'selected' : '' }}>@lang('Signer')</option>
                            <option value="viewer" {{ ($p['role'] ?? '') === 'viewer' ? 'selected' : '' }}>@lang('Viewer only')</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('user.contracts.create') }}" class="text-[13px] text-white/50 hover:text-white">@lang('Cancel')</a>
            <button type="submit" class="btn-primary rounded-full px-5 py-3 text-[13px] font-semibold flex items-center gap-2">
                <x-heroicon-o-sparkles class="h-4 w-4" />
                @lang('Generate Contract')
            </button>
        </div>
    </form>

</div>


<div id="ai-loader" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="max-w-md w-full mx-4 rounded-2xl glass p-8 text-center">
        <div class="relative mx-auto mb-6 h-20 w-20">
            <div class="absolute inset-0 rounded-full border-4 border-white/10"></div>
            <div class="absolute inset-0 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <x-heroicon-o-sparkles class="h-9 w-9 text-primary animate-pulse" />
            </div>
        </div>
        <h2 class="text-lg font-bold text-white">@lang('Drafting your contract…')</h2>
        <p id="ai-loader-step" class="mt-2 text-sm text-white/55">@lang('Reviewing your brief')</p>
        <div class="mt-5 flex items-center justify-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-primary animate-bounce" class="delay-0"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-primary animate-bounce" class="delay-150"></span>
            <span class="h-1.5 w-1.5 rounded-full bg-primary animate-bounce" class="delay-300"></span>
        </div>
        <p class="mt-5 text-xs text-white/40">{{ __('This may take 15–60 seconds. Please don\'t close this window.') }}</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var idx = {{ count($existingParties) }};
    var briefInput = document.getElementById('brief-input');

    document.querySelectorAll('.sample-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!briefInput) return;
            briefInput.value = btn.getAttribute('data-sample');
            briefInput.focus();
        });
    });

    document.getElementById('add-party-btn').addEventListener('click', function () {
        var list = document.getElementById('parties-list');
        var html = '<div class="party-row space-y-3 pt-4 border-t border-white/8">'
            + '<div class="flex items-center justify-between">'
            + '<div class="text-[11px] font-mono text-white/35 uppercase tracking-widest">{{ __("Party") }} ' + (idx + 1) + '</div>'
            + '<button type="button" class="remove-party text-red-400 hover:text-red-300 text-[11px]">{{ __("Remove") }}</button>'
            + '</div>'
            + '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">'
            + '<input type="text" name="parties[' + idx + '][name]" placeholder="{{ __("Full name") }}" required class="ud-input" />'
            + '<input type="email" name="parties[' + idx + '][email]" placeholder="{{ __("Email address") }}" required class="ud-input" />'
            + '</div>'
            + '<select name="parties[' + idx + '][role]" class="ud-input">'
            + '<option value="signer">{{ __("Signer") }}</option>'
            + '<option value="viewer">{{ __("Viewer only") }}</option>'
            + '</select>'
            + '</div>';
        list.insertAdjacentHTML('beforeend', html);
        idx++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-party')) {
            e.target.closest('.party-row').remove();
        }
    });

    // Loader on submit
    var form   = document.getElementById('ai-contract-form');
    var loader = document.getElementById('ai-loader');
    var step   = document.getElementById('ai-loader-step');
    var steps  = [
        @json(__('Reviewing your brief')),
        @json(__('Drafting clauses')),
        @json(__('Adding protective terms')),
        @json(__('Finalizing the agreement'))
    ];
    form.addEventListener('submit', function () {
        loader.classList.remove('hidden');
        loader.classList.add('flex');
        document.body.style.overflow = 'hidden';
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
        var i = 0;
        setInterval(function () {
    'use strict';
            i = (i + 1) % steps.length;
            step.textContent = steps[i];
        }, 3500);
    });
})();
</script>
@endpush
