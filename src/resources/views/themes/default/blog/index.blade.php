@extends('theme::layouts.frontend')

@section('title', 'Blog')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Blog</h1>
        <a href="{{ route('blog.rss') }}" class="text-sm text-gray-400 hover:text-brand-600 flex items-center gap-1">
            <x-heroicon-o-rss class="h-4 w-4" />
            RSS
        </a>
    </div>

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('blog.index') }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">All</a>
        @foreach($categories as $category)
        <a href="{{ route('blog.category', $category->slug) }}"
           class="rounded-full px-4 py-1.5 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200">
            {{ $category->getLocalName() }}
        </a>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
        <article class="group">
            @if($post->cover_image)
            <div class="aspect-video rounded-xl overflow-hidden bg-gray-100 mb-4">
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
            @endif
            <div class="space-y-2">
                @if($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}"
                   class="text-xs font-semibold text-brand-600 uppercase tracking-wide">
                    {{ $post->category->getLocalName() }}
                </a>
                @endif
                <h2 class="font-bold text-gray-900 text-lg leading-snug group-hover:text-brand-600">
                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->getLocalTitle() }}</a>
                </h2>
                @if($post->getLocalExcerpt())
                <p class="text-gray-500 text-sm line-clamp-2">{{ $post->getLocalExcerpt() }}</p>
                @endif
                <p class="text-xs text-gray-400">{{ $post->published_at->format('M j, Y') }}</p>
            </div>
        </article>
        @empty
        <div class="col-span-3 py-16 text-center text-gray-400">No posts yet.</div>
        @endforelse
    </div>

    <div class="mt-10">{{ $posts->links() }}</div>
</div>
@endsection
