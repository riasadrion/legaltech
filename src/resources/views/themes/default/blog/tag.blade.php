@extends('theme::layouts.frontend')

@section('title', '#' . $tag->getLocalName() . ' — Blog')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">#{{ $tag->getLocalName() }}</h1>

    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
        <article>
            <h2 class="font-bold text-gray-900 hover:text-brand-600">
                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->getLocalTitle() }}</a>
            </h2>
            <p class="text-xs text-gray-400 mt-1">{{ $post->published_at->format('M j, Y') }}</p>
        </article>
        @empty
        <div class="col-span-3 py-16 text-center text-gray-400">No posts with this tag.</div>
        @endforelse
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
</div>
@endsection
