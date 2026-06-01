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


<div class="relative bg-ink-950 pt-36 pb-16 overflow-hidden">
    <div class="absolute inset-0 bg-grid opacity-[0.03] pointer-events-none"></div>
    <div class="radial-fade pointer-events-none"></div>

    <div class="relative mx-auto max-w-3xl px-6">


        <div class="flex flex-wrap items-center gap-2 text-sm text-white/40 mb-6">
            <a href="{{ route('blog.index') }}" class="hover:text-white/70 transition">@lang('Blog')</a>
            @if($post->category)
                <span>/</span>
                <a href="{{ route('blog.category', $post->category->slug) }}"
                   class="text-lime font-medium hover:text-lime-soft transition">
                    {{ $post->category->getLocalName() }}
                </a>
            @endif
        </div>

        <h1 class="font-display text-3xl sm:text-4xl md:text-5xl text-white leading-tight mb-6">
            {{ $post->getLocalTitle() }}
        </h1>

        @if($post->getLocalExcerpt())
        <p class="text-lg text-white/50 leading-relaxed mb-8">{{ $post->getLocalExcerpt() }}</p>
        @endif


        <div class="flex flex-wrap items-center gap-4 text-sm text-white/40">
            <time datetime="{{ $post->published_at->toIso8601String() }}">
                {{ $post->published_at->format('M j, Y') }}
            </time>
        </div>
    </div>
</div>


@if($post->cover_image)
<div class="mx-auto max-w-4xl px-6 -mt-8 mb-0 relative z-10">
    <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10">
        <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
             class="w-full h-full object-cover">
    </div>
</div>
@endif


<div class="mx-auto max-w-3xl px-6 py-16">
    <div class="blog-body">
        <x-safe-html :content="$post->getLocalBody()" />
    </div>


    @if($post->tags->isNotEmpty())
    <div class="mt-10 pt-8 border-t border-white/8 flex flex-wrap gap-2">
        @foreach($post->tags as $tag)
        <a href="{{ route('blog.tag', $tag->slug) }}"
           class="inline-flex items-center gap-1 rounded-full glass px-4 py-1.5 text-xs font-medium text-white/60 hover:text-white hover:bg-white/10 transition">
            <x-heroicon-o-hashtag class="h-3.5 w-3.5 text-lime/60" />{{ $tag->getLocalName() }}
        </a>
        @endforeach
    </div>
    @endif


    <div class="mt-10 flex items-center justify-between">
        <a href="{{ route('blog.index') }}"
           class="inline-flex items-center gap-2 text-sm text-white/40 hover:text-white transition">
            <x-heroicon-o-arrow-left class="h-4 w-4" />@lang('All articles')
        </a>
    </div>
</div>


@if($related->isNotEmpty())
<section class="relative border-t border-white/8 bg-ink-900 py-20">
    <div class="mx-auto max-w-6xl px-6">
        <h2 class="font-display text-3xl text-white mb-12">@lang('Other Posts')</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($related as $rPost)
            <article class="group glass rounded-2xl overflow-hidden lift">
                @if($rPost->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ upload_url($rPost->cover_image) }}" alt="{{ $rPost->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-5">
                    @if($rPost->category)
                    <a href="{{ route('blog.category', $rPost->category->slug) }}"
                       class="text-[11px] font-semibold text-lime uppercase tracking-widest mb-2 block">
                        {{ $rPost->category->getLocalName() }}
                    </a>
                    @endif
                    <h3 class="font-semibold text-white leading-snug group-hover:text-lime transition mb-2">
                        <a href="{{ route('blog.show', $rPost->slug) }}">{{ $rPost->getLocalTitle() }}</a>
                    </h3>
                    <p class="text-xs text-white/30">{{ $rPost->published_at->format('M j, Y') }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
