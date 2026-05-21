@php
    $d     = $section->getLocaleData();
    $count = (int) ($section->data['count'] ?? 3);
    $posts = \App\Domain\Blog\Models\BlogPost::published()
        ->with('category')
        ->latest('published_at')
        ->limit($count)
        ->get();
@endphp

<section class="py-16 bg-gray-50">
    <div class="mx-auto max-w-6xl px-4">
        @if(!empty($d['heading']))
        <h2 class="text-3xl font-bold text-gray-900 mb-8">{{ $d['heading'] }}</h2>
        @endif

        @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
            <article>
                <p class="text-xs font-semibold text-brand-600 uppercase tracking-wide mb-1">
                    {{ $post->category?->getLocalName() }}
                </p>
                <h3 class="font-bold text-gray-900 hover:text-brand-600 leading-snug">
                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->getLocalTitle() }}</a>
                </h3>
                <p class="text-xs text-gray-400 mt-1">{{ $post->published_at->format('M j, Y') }}</p>
            </article>
            @endforeach
        </div>
        <div class="mt-8">
            <a href="{{ route('blog.index') }}"
               class="text-sm font-medium text-brand-600 hover:text-brand-700">View all posts →</a>
        </div>
        @else
        <p class="text-gray-400">No posts published yet.</p>
        @endif
    </div>
</section>
