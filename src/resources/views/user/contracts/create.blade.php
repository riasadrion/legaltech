@extends('theme::layouts.user')

@section('breadcrumb', __('New Contract'))

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">{{ __('Choose a Template') }}</h1>
        <p class="ud-page-sub">{{ __('Select the type of contract you want to generate.') }}</p>
    </div>

    @foreach($categories as $category)
    <div class="space-y-3">
        <h2 class="text-[11px] font-mono uppercase tracking-widest text-white/40">{{ $category->name }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($category->templates as $template)
            <a href="{{ route('user.contracts.wizard', $template) }}"
               class="rounded-2xl glass p-5 flex flex-col gap-3 hover:bg-white/6 transition group">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 rounded-xl bg-lime/10 grid place-items-center">
                        <x-heroicon-o-document-text class="h-4 w-4 text-lime" />
                    </div>
                    <div class="text-[11px] font-mono text-white/35">
                        {{ $template->credits_cost }} {{ __('credit', [], null, $template->credits_cost) }}
                    </div>
                </div>
                <div>
                    <div class="font-display text-lg text-white">{{ $template->name }}</div>
                    @if($template->description)
                    <div class="text-[12px] text-white/45 mt-1 leading-relaxed">{{ $template->description }}</div>
                    @endif
                </div>
                <div class="mt-auto flex items-center gap-1 text-[12px] text-lime/70 group-hover:text-lime transition">
                    {{ __('Use this template') }}
                    <x-heroicon-o-arrow-right class="h-3.5 w-3.5" />
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($categories->isEmpty())
    <div class="rounded-2xl glass p-14 text-center text-white/40">
        <x-heroicon-o-document-text class="h-10 w-10 mx-auto mb-3 opacity-30" />
        <div class="text-sm">{{ __('No templates available yet.') }}</div>
    </div>
    @endif

</div>
@endsection
