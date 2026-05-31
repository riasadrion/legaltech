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
        <div class="absolute inset-0 bg-grid opacity-[0.035] pointer-events-none"></div>
        <div class="page-hero-orb-top"></div>
        <div class="page-hero-orb-bl"></div>
        <div class="page-hero-orb-tr"></div>
        <div class="page-hero-shimmer"></div>

        <div class="relative mx-auto max-w-4xl px-6 text-center">
            <div class="flex items-center justify-center gap-2 text-sm text-white/30 mb-8">
                <a href="{{ route('home') }}" class="hover:text-white/60 transition">{{ __('Home') }}</a>
                <span>/</span>
                <span class="text-white/50">{{ $page->getLocalTitle() }}</span>
            </div>
            <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl text-white leading-tight">
                {{ $page->getLocalTitle() }}
            </h1>
        </div>
    </div>

</div>
@endif

@foreach($sections as $section)
    <?php echo $section->renderFrontend(); ?>
@endforeach

@endsection
