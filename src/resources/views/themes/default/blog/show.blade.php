@extends('theme::layouts.frontend')

@section('title', $post->getLocalMetaTitle())
@section('meta_description', $post->getLocalMetaDescription())

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->getLocalMetaTitle() }}">
@if($post->cover_image)
<meta property="og:image" content="{{ upload_url($post->cover_image) }}">
@endif
@endpush

@section('content')


<div class="page-hero-wrap">

    <div class="page-hero-glow-bl"></div>
    <div class="page-hero-glow-tr"></div>

    <div class="page-hero">
        <div class="absolute inset-0 bg-grid-cream radial-fade pointer-events-none"></div>
        <div class="page-hero-orb-top"></div>
        <div class="page-hero-orb-bl"></div>
        <div class="page-hero-orb-tr"></div>
        <div class="page-hero-shimmer"></div>

        <div class="relative mx-auto max-w-3xl px-6">

            <div class="flex flex-wrap items-center gap-2 text-sm text-ink-400 mb-6">
                <a href="{{ route('blog.index') }}" class="hover:text-ink-700 transition">@lang('Blog')</a>
                @if($post->category)
                    <span class="text-ink-900/20">/</span>
                    <a href="{{ route('blog.category', $post->category->slug) }}"
                       class="text-primary font-medium hover:text-primary-deep transition">
                        {{ $post->category->getLocalName() }}
                    </a>
                @endif
            </div>

            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl text-ink-900 leading-tight mb-6">
                {{ $post->getLocalTitle() }}
            </h1>

            @if($post->getLocalExcerpt())
            <p class="text-lg text-ink-500 leading-relaxed mb-8">{{ $post->getLocalExcerpt() }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-4 text-sm text-ink-400">
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->format('M j, Y') }}
                </time>
            </div>
        </div>
    </div>

</div>


@if($post->cover_image)
<div class="mx-auto max-w-4xl px-6 -mt-8 mb-0 relative z-10">
    <div class="aspect-video rounded-2xl overflow-hidden shadow-lg ring-1 ring-ink-900/8">
        <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
             class="w-full h-full object-cover">
    </div>
</div>
@endif


<div class="bg-white">
    <div class="mx-auto max-w-3xl px-6 py-16">
        <div class="blog-body">
            <x-safe-html :content="$post->getLocalBody()" />
        </div>


        <div class="mt-10 pt-8 border-t border-ink-900/8 flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-ink-500">@lang('Share')</span>

            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->getLocalTitle()) }}"
               target="_blank" rel="noopener noreferrer"
               aria-label="@lang('Share on X')"
               class="grid place-items-center w-9 h-9 rounded-full bg-ink-100 hover:bg-primary/10 border border-ink-900/8 hover:border-primary/20 text-ink-500 hover:text-primary transition">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.66l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>
                </svg>
            </a>

            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"
               target="_blank" rel="noopener noreferrer"
               aria-label="@lang('Share on LinkedIn')"
               class="grid place-items-center w-9 h-9 rounded-full bg-ink-100 hover:bg-primary/10 border border-ink-900/8 hover:border-primary/20 text-ink-500 hover:text-primary transition">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
            </a>

            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
               target="_blank" rel="noopener noreferrer"
               aria-label="@lang('Share on Facebook')"
               class="grid place-items-center w-9 h-9 rounded-full bg-ink-100 hover:bg-primary/10 border border-ink-900/8 hover:border-primary/20 text-ink-500 hover:text-primary transition">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>

            <button type="button" data-copy-link="{{ url()->current() }}"
                    aria-label="@lang('Copy link')"
                    class="grid place-items-center w-9 h-9 rounded-full bg-ink-100 hover:bg-primary/10 border border-ink-900/8 hover:border-primary/20 text-ink-500 hover:text-primary transition">
                <x-heroicon-o-link class="h-4 w-4" />
            </button>
        </div>

        @if($post->tags->isNotEmpty())
        <div class="mt-10 pt-8 border-t border-ink-900/8 flex flex-wrap gap-2">
            @foreach($post->tags as $tag)
            <a href="{{ route('blog.tag', $tag->slug) }}"
               class="inline-flex items-center gap-1 rounded-full bg-ink-100 hover:bg-primary/10 border border-ink-900/8 hover:border-primary/20 px-4 py-1.5 text-xs font-medium text-ink-500 hover:text-primary transition">
                <x-heroicon-o-hashtag class="h-3.5 w-3.5" />{{ $tag->getLocalName() }}
            </a>
            @endforeach
        </div>
        @endif


        <div class="mt-10 flex items-center justify-between">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-2 text-sm text-ink-400 hover:text-ink-900 transition">
                <x-heroicon-o-arrow-left class="h-4 w-4" />@lang('All articles')
            </a>
        </div>
    </div>
</div>


@if($related->isNotEmpty())
<section class="bg-cream border-t border-ink-900/8 py-20">
    <div class="mx-auto max-w-6xl px-6">
        <h2 class="font-display text-3xl text-ink-900 mb-12">@lang('Other Posts')</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($related as $rPost)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-sm ring-1 ring-ink-900/6 hover:shadow-md transition-shadow">
                @if($rPost->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ upload_url($rPost->cover_image) }}" alt="{{ $rPost->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-5">
                    @if($rPost->category)
                    <a href="{{ route('blog.category', $rPost->category->slug) }}"
                       class="text-[11px] font-semibold text-primary uppercase tracking-widest mb-2 block">
                        {{ $rPost->category->getLocalName() }}
                    </a>
                    @endif
                    <h3 class="font-semibold text-ink-900 leading-snug group-hover:text-primary transition mb-2">
                        <a href="{{ route('blog.show', $rPost->slug) }}">{{ $rPost->getLocalTitle() }}</a>
                    </h3>
                    <p class="text-xs text-ink-400">{{ $rPost->published_at->format('M j, Y') }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('scripts')
<script>
$(function () {
    'use strict';
    $(document).on('click', '[data-copy-link]', function () {
        var $btn = $(this);
        var url = $btn.data('copy-link');
        navigator.clipboard.writeText(url).then(function () {
            var original = $btn.html();
            $btn.html('<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>');
            setTimeout(function () { $btn.html(original); }, 1500);
        });
    });
});
</script>
@endpush

@endsection
