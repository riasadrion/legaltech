@extends('theme::layouts.user')

@section('breadcrumb', __('New Contract'))

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">@lang('Start a New Contract')</h1>
        <p class="ud-page-sub">@lang('Pick the category that best matches the agreement you want to generate.')</p>
    </div>

    @include('user.contracts._stepper', ['step' => 1])

    @php $canAi = auth('user')->user()?->canGenerateAiContract(); @endphp
    <a href="{{ $canAi ? route('user.contracts.ai.create') : route('user.subscription.plans') }}"
       class="relative rounded-2xl glass overflow-hidden p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:bg-white/6 transition group block">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-lime/15 blur-3xl opacity-70 pointer-events-none"></div>
        <div class="w-12 h-12 rounded-xl bg-lime/15 grid place-items-center shrink-0 relative">
            <x-heroicon-o-sparkles class="h-6 w-6 text-lime" />
        </div>
        <div class="flex-1 relative min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-display text-lg text-white">@lang('Draft a Custom Contract with AI')</span>
                <span class="text-[10px] font-mono uppercase tracking-widest text-lime bg-lime/10 border border-lime/30 rounded-full px-2 py-0.5">@lang('Pro')</span>
            </div>
            <p class="text-[12px] text-white/55 mt-1">
                @if($canAi)
                    @lang('Describe what you need in plain English and get a ready-to-sign agreement — no template needed.')
                @else
                    @lang('Skip the templates. Describe what you need and let the AI draft the full agreement for you. Upgrade to unlock.')
                @endif
            </p>
        </div>
        <div class="flex items-center gap-1 text-[12px] text-lime/70 group-hover:text-lime transition relative shrink-0">
            @if($canAi)
                @lang('Start drafting')
            @else
                <x-heroicon-o-lock-closed class="h-3.5 w-3.5" />
                @lang('Upgrade to unlock')
            @endif
            <x-heroicon-o-arrow-right class="h-3.5 w-3.5" />
        </div>
    </a>

    @if($categories->isEmpty())
        <div class="rounded-2xl glass p-14 text-center text-white/40">
            <x-heroicon-o-folder-open class="h-10 w-10 mx-auto mb-3 opacity-30" />
            <div class="text-sm">@lang('No contract templates have been published yet.')</div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('user.contracts.templates', $category) }}"
                   class="rounded-2xl glass p-5 flex flex-col gap-3 hover:bg-white/6 transition group">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-lime/10 grid place-items-center">
                            <x-heroicon-o-folder class="h-5 w-5 text-lime" />
                        </div>
                        <span class="text-[11px] font-mono text-white/35">
                            {{ $category->templates_count }} @lang('templates')
                        </span>
                    </div>
                    <div>
                        <div class="font-display text-lg text-white leading-tight">{{ $category->name }}</div>
                        @if($category->description)
                            <div class="text-[12px] text-white/45 mt-1 leading-relaxed">{{ $category->description }}</div>
                        @endif
                    </div>
                    <div class="mt-auto flex items-center gap-1 text-[12px] text-lime/70 group-hover:text-lime transition">
                        @lang('Browse templates')
                        <x-heroicon-o-arrow-right class="h-3.5 w-3.5" />
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
