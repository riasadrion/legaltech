@extends('theme::layouts.frontend')

@section('title', $category->getLocalName() . ' — ' . __('Blog'))

@section('content')

{{-- Hero --}}
<div class="relative bg-ink-950 pt-36 pb-20 overflow-hidden">
    <div class="absolute inset-0 bg-grid opacity-[0.03] pointer-events-none"></div>
    <div class="radial-fade pointer-events-none"></div>
    <div class="relative mx-auto max-w-6xl px-6">
        <a href="{{ route('blog.index') }}"
           class="inline-flex items-center gap-2 text-sm text-white/40 hover:text-white transition mb-6">
            <i class="ri-arrow-left-line"></i>{{ __('All articles') }}
        </a>
        <div class="inline-flex items-center gap-2 rounded-full glass px-4 py-1.5 text-xs font-medium text-lime mb-4">
            <i class="ri-folder-line"></i>{{ __('Category') }}
        </div>
        <h1 class="font-display text-5xl text-white mb-4">{{ $category->getLocalName() }}</h1>
        @if($category->getLocalDescription())
        <p class="text-lg text-white/40">{{ $category->getLocalDescription() }}</p>
        @endif
    </div>
</div>

<div class="bg-ink-950 py-16">
    <div class="mx-auto max-w-6xl px-6">
        @if($posts->isEmpty())
        <div class="py-24 text-center text-white/30">
            <i class="ri-article-line text-5xl mb-4 block text-white/10"></i>
            {{ __('No posts in this category yet.') }}
        </div>
        @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
            <article class="group glass rounded-2xl overflow-hidden lift flex flex-col">
                @if($post->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="font-semibold text-white leading-snug group-hover:text-lime transition mb-3 flex-1">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->getLocalTitle() }}</a>
                    </h2>
                    @if($post->getLocalExcerpt())
                    <p class="text-sm text-white/40 line-clamp-2 mb-4">{{ $post->getLocalExcerpt() }}</p>
                    @endif
                    <div class="text-xs text-white/30 mt-auto pt-4 border-t border-white/8">
                        <time>{{ $post->published_at->format('M j, Y') }}</time>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @if($posts->hasPages())
        <div class="mt-12 flex justify-center">{{ $posts->links() }}</div>
        @endif
        @endif
    </div>
</div>

@endsection
