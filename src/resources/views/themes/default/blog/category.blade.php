@extends('theme::layouts.frontend')

@section('title', $category->getLocalName() . ' — ' . __('Blog'))

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

        <div class="relative mx-auto max-w-6xl px-6">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-2 text-sm text-ink-400 hover:text-ink-700 transition mb-6">
                <x-heroicon-o-arrow-left class="h-4 w-4" />@lang('All articles')
            </a>
            <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 border border-primary/20 px-4 py-1.5 text-xs font-medium text-primary mb-4">
                <x-heroicon-o-folder class="h-4 w-4" />@lang('Category')
            </div>
            <h1 class="font-display text-5xl text-ink-900 mb-4">{{ $category->getLocalName() }}</h1>
            @if($category->getLocalDescription())
            <p class="text-lg text-ink-400">{{ $category->getLocalDescription() }}</p>
            @endif
        </div>
    </div>

</div>

<div class="bg-cream pt-4 pb-16">
    <div class="mx-auto max-w-6xl px-6">
        @if($posts->isEmpty())
        <div class="py-24 text-center text-ink-400">
            <x-heroicon-o-document-text class="h-12 w-12 mb-4 mx-auto block text-ink-200" />
            @lang('No posts in this category yet.')
        </div>
        @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-sm ring-1 ring-ink-900/6 hover:shadow-md transition-shadow flex flex-col">
                @if($post->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="font-semibold text-ink-900 leading-snug group-hover:text-primary transition mb-3 flex-1">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->getLocalTitle() }}</a>
                    </h2>
                    @if($post->getLocalExcerpt())
                    <p class="text-sm text-ink-400 line-clamp-2 mb-4">{{ $post->getLocalExcerpt() }}</p>
                    @endif
                    <div class="text-xs text-ink-400 mt-auto pt-4 border-t border-ink-900/8">
                        <time>{{ $post->published_at->format('M j, Y') }}</time>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @if($posts->hasPages())
        <div class="mt-12"><x-pagination :paginator="$posts" /></div>
        @endif
        @endif
    </div>
</div>

@endsection
