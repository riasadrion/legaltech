@extends('theme::layouts.frontend')

@section('title', $post->getLocalMetaTitle())
@section('meta_description', $post->getLocalMetaDescription())

@section('content')
<article class="mx-auto max-w-3xl px-4 py-12">

    {{-- Category + meta --}}
    <div class="mb-4 flex flex-wrap items-center gap-3 text-sm text-gray-500">
        @if($post->category)
        <a href="{{ route('blog.category', $post->category->slug) }}"
           class="font-semibold text-brand-600">{{ $post->category->getLocalName() }}</a>
        <span>·</span>
        @endif
        <time datetime="{{ $post->published_at->toIso8601String() }}">
            {{ $post->published_at->format('M j, Y') }}
        </time>
        @if($post->author)
        <span>· by {{ $post->author->name }}</span>
        @endif
    </div>

    <h1 class="text-4xl font-bold text-gray-900 mb-6 leading-tight">{{ $post->getLocalTitle() }}</h1>

    @if($post->cover_image)
    <div class="aspect-video rounded-xl overflow-hidden bg-gray-100 mb-8">
        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
             class="w-full h-full object-cover">
    </div>
    @endif

    <div class="prose prose-gray max-w-none">
        <x-safe-html :html="$post->getLocalBody()" />
    </div>

    {{-- Tags --}}
    @if($post->tags->isNotEmpty())
    <div class="mt-8 flex flex-wrap gap-2">
        @foreach($post->tags as $tag)
        <a href="{{ route('blog.tag', $tag->slug) }}"
           class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-200">
            #{{ $tag->getLocalName() }}
        </a>
        @endforeach
    </div>
    @endif
</article>

{{-- Related posts --}}
@if($related->isNotEmpty())
<div class="border-t border-gray-100 mt-8">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Related posts</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach($related as $rPost)
            <article>
                <h3 class="font-semibold text-gray-900 hover:text-brand-600">
                    <a href="{{ route('blog.show', $rPost->slug) }}">{{ $rPost->getLocalTitle() }}</a>
                </h3>
                <p class="text-xs text-gray-400 mt-1">{{ $rPost->published_at->format('M j, Y') }}</p>
            </article>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
