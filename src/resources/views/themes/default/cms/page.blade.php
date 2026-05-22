@extends('theme::layouts.frontend')

@section('title', $page->getLocalMetaTitle())
@section('meta_description', $page->getLocalMetaDescription())

@section('content')

@php $sections = $page->enabledSections(); @endphp

@php
    $isContentPage = $sections->count() === 1 && $sections->first()->type === 'custom_html';
    $isSectionPage = !$isContentPage && $sections->isNotEmpty();
@endphp

@if($isContentPage || $sections->isEmpty())
<div class="relative" style="background:#08090C;">

    {{-- Glows --}}
    <div class="absolute bottom-0 left-0 w-[700px] h-[700px] pointer-events-none"
         style="background:radial-gradient(circle at 0% 100%, rgba(212,255,58,0.10) 0%, transparent 55%);"></div>
    <div class="absolute top-1/3 right-0 w-[600px] h-[600px] pointer-events-none"
         style="background:radial-gradient(circle at 100% 50%, rgba(124,58,237,0.11) 0%, transparent 55%);"></div>

{{-- Hero header for content-only pages (privacy, terms, refund, etc.) --}}
<div class="relative pb-28 overflow-hidden" style="padding-top: calc(72px + 80px);">

    {{-- Grid --}}
    <div class="absolute inset-0 bg-grid opacity-[0.035] pointer-events-none"></div>

    {{-- Lime orb — top center --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] pointer-events-none"
         style="background:radial-gradient(ellipse at 50% 0%, rgba(212,255,58,0.13) 0%, transparent 70%);"></div>

    {{-- Violet orb — bottom left --}}
    <div class="absolute bottom-0 left-0 w-[500px] h-[400px] pointer-events-none"
         style="background:radial-gradient(ellipse at 0% 100%, rgba(124,58,237,0.10) 0%, transparent 65%);"></div>

    {{-- Violet orb — top right --}}
    <div class="absolute top-0 right-0 w-[400px] h-[350px] pointer-events-none"
         style="background:radial-gradient(ellipse at 100% 0%, rgba(124,58,237,0.08) 0%, transparent 65%);"></div>

    {{-- Shimmer line --}}
    <div class="absolute bottom-0 inset-x-0 h-px pointer-events-none"
         style="background:linear-gradient(90deg, transparent, rgba(212,255,58,0.2) 40%, rgba(212,255,58,0.2) 60%, transparent);"></div>

    <div class="relative mx-auto max-w-4xl px-6 text-center">
        {{-- Breadcrumb --}}
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

{{-- Content section --}}
@foreach($sections as $section)
    <?php echo $section->renderFrontend(); ?>
@endforeach

</div>{{-- end outer glow wrapper --}}

@else
{{-- Section-based landing pages (home, etc.) — no header, sections handle their own layout --}}
@foreach($sections as $section)
    <?php echo $section->renderFrontend(); ?>
@endforeach
@endif

@endsection
