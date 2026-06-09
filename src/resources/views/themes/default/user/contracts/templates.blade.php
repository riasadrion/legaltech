@extends('theme::layouts.user')

@section('breadcrumb', $category->name)

@section('content')
<div class="space-y-6">

    <div class="flex items-start gap-3">
        <a href="{{ route('user.contracts.create') }}"
           class="flex items-center justify-center h-9 w-9 rounded-xl glass border border-white/10 text-white/60 hover:text-white transition shrink-0 mt-1">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="ud-page-title !mb-0">{{ $category->name }}</h1>
            <p class="text-[12px] text-white/60 mt-1">{{ $category->description ?: __('Choose a template to start your contract.') }}</p>
        </div>
    </div>

    @include('theme::user.contracts._stepper', ['step' => 2])

    @if($templates->isEmpty())
        <div class="rounded-2xl glass p-14 text-center text-white/40">
            <x-heroicon-o-document-text class="h-10 w-10 mx-auto mb-3 opacity-30" />
            <div class="text-sm">@lang('No templates in this category yet.')</div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
                <a href="{{ route('user.contracts.wizard', $template) }}"
                   class="rounded-2xl glass p-5 flex flex-col gap-3 hover:bg-white/6 transition group">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 grid place-items-center">
                            <x-heroicon-o-document-text class="h-5 w-5 text-primary" />
                        </div>
                        <div class="text-[11px] font-mono text-white/35">
                            {{ $template->credits_cost }} {{ __('credit', [], null, $template->credits_cost) }}
                        </div>
                    </div>
                    <div>
                        <div class="font-display text-lg text-white">{{ $template->name }}</div>
                        @if($template->description)
                            <div class="text-[12px] text-white/60 mt-1 leading-relaxed line-clamp-3">{{ $template->description }}</div>
                        @endif
                    </div>
                    <div class="mt-auto flex items-center gap-1 text-[12px] text-primary/70 group-hover:text-primary transition">
                        @lang('Use this template')
                        <x-heroicon-o-arrow-right class="h-3.5 w-3.5" />
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
