@extends('theme::layouts.frontend')

@section('title', $page->getLocalMetaTitle())
@section('meta_description', $page->getLocalMetaDescription())

@section('content')

@php $sections = $page->enabledSections(); @endphp

@php
    $firstType = $sections->first()?->type;
    $needsHero = $firstType !== 'hero';
@endphp

@if($needsHero)
<div class="page-hero-wrap">

    <div class="page-hero-glow-bl"></div>
    <div class="page-hero-glow-tr"></div>

    <div class="page-hero">
        <div class="absolute inset-0 bg-grid-cream radial-fade pointer-events-none"></div>
        <div class="page-hero-orb-top"></div>
        <div class="page-hero-orb-bl"></div>
        <div class="page-hero-orb-tr"></div>
        <div class="page-hero-shimmer"></div>

        <div class="relative mx-auto max-w-4xl px-6 text-center">
            <nav class="inline-flex items-center gap-1.5 text-[13px] text-ink-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-ink-700 transition">@lang('Home')</a>
                <span class="text-ink-900/20">/</span>
                <span class="text-ink-600">{{ $page->getLocalTitle() }}</span>
            </nav>
            <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl text-ink-900 leading-tight">
                {{ $page->getLocalTitle() }}
            </h1>
        </div>
    </div>

</div>
@endif

@foreach($sections as $index => $section)
    <?php echo $section->renderFrontend($needsHero ? $index + 1 : $index); ?>
@endforeach

@endsection
