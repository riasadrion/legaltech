@extends('theme::layouts.frontend')

@section('title', __('Blog'))

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

        <div class="relative mx-auto max-w-4xl px-6 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-primary/10 border border-primary/20 px-4 py-1.5 text-xs font-medium text-primary mb-6">
                <x-heroicon-o-document-text class="h-4 w-4" />@lang('Articles & insights')
            </div>
            <h1 class="font-display text-4xl sm:text-5xl md:text-6xl text-ink-900 leading-tight mb-4">@lang('The LegalDocs Blog')</h1>
            <p class="text-lg text-ink-400 max-w-xl mx-auto">
                @lang('Contract law, AI automation, and the future of legal work — written for teams who ship.')
            </p>
        </div>
    </div>

</div>


<div class="bg-white border-b border-ink-900/8">
    <div class="mx-auto max-w-6xl px-6">

        @if($categories->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 py-5">
            <a href="{{ route('blog.index') }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition
                      {{ !request()->routeIs('blog.category') && !request()->routeIs('blog.tag')
                         ? 'bg-primary text-white' : 'bg-ink-100 text-ink-500 hover:text-ink-900 hover:bg-ink-200' }}">
                @lang('All')
            </a>
            @foreach($categories as $cat)
            @if($cat->posts_count > 0)
            <a href="{{ route('blog.category', $cat->slug) }}"
               class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-medium transition bg-ink-100 text-ink-500 hover:text-ink-900 hover:bg-ink-200">
                {{ $cat->getLocalName() }}
                <span class="text-xs text-ink-400 font-normal">{{ $cat->posts_count }}</span>
            </a>
            @endif
            @endforeach

            <form action="{{ route('blog.index') }}" method="GET" class="ml-auto">
                <div class="relative">
                    <button type="submit" aria-label="@lang('Search')"
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-300 hover:text-primary transition">
                        <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                    </button>
                    <input type="search" name="q" value="{{ $search }}"
                           placeholder="@lang('Search articles…')"
                           class="w-48 sm:w-64 rounded-full border border-ink-900/8 bg-cream pl-9 pr-4 py-1.5 text-sm text-ink-900 placeholder:text-ink-300 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30 transition">
                </div>
            </form>

            <a href="{{ route('blog.rss') }}"
               class="flex items-center gap-1.5 text-sm text-ink-400 hover:text-ink-700 transition">
                <x-heroicon-o-rss class="h-4 w-4" />RSS
            </a>
        </div>
        @endif

    </div>
</div>


<div class="bg-cream py-16">
    <div class="mx-auto max-w-6xl px-6">

        @if($search !== '')
        <div class="flex items-center gap-2 text-sm text-ink-500 mb-8">
            <span>@lang('Search results for') <strong class="text-ink-900">"{{ $search }}"</strong></span>
            <a href="{{ route('blog.index') }}" class="text-primary hover:text-primary-deep transition">@lang('Clear')</a>
        </div>
        @endif

        @if($posts->isEmpty())
        <div class="py-24 text-center text-ink-400">
            <x-heroicon-o-document-text class="h-12 w-12 mb-4 mx-auto block text-ink-200" />
            @if($search !== '')
                @lang('No posts found.')
            @else
                @lang('No posts yet.')
            @endif
        </div>
        @else

        @php
            $heroPost  = ($search === '' && $posts->onFirstPage()) ? $posts->first() : null;
            $gridPosts = $heroPost ? $posts->slice(1) : $posts;
        @endphp

        @if($heroPost)
        <article class="group relative bg-ink-950 rounded-3xl overflow-hidden mb-10 shadow-lg">
            <a href="{{ route('blog.show', $heroPost->slug) }}" class="flex flex-col md:flex-row">
                @if($heroPost->cover_image)
                <div class="md:w-1/2 aspect-video md:aspect-auto overflow-hidden">
                    <img src="{{ upload_url($heroPost->cover_image) }}" alt="{{ $heroPost->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-8 md:p-10 {{ $heroPost->cover_image ? 'md:w-1/2' : 'w-full' }} flex flex-col justify-center">
                    @if($heroPost->category)
                    <span class="text-[11px] font-semibold text-primary uppercase tracking-widest mb-3">
                        {{ $heroPost->category->getLocalName() }}
                    </span>
                    @endif
                    <h2 class="font-display text-3xl text-white leading-tight mb-4 group-hover:text-primary transition">
                        {{ $heroPost->getLocalTitle() }}
                    </h2>
                    @if($heroPost->getLocalExcerpt())
                    <p class="text-white/55 leading-relaxed mb-6 line-clamp-3">{{ $heroPost->getLocalExcerpt() }}</p>
                    @endif
                    <div class="flex items-center gap-3 text-sm text-white/35">
                        <time>{{ $heroPost->published_at->format('M j, Y') }}</time>
                    </div>
                    <div class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-primary">
                        @lang('Read article') <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                    </div>
                </div>
            </a>
        </article>
        @endif

        @if($gridPosts->isNotEmpty())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($gridPosts as $post)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-sm ring-1 ring-ink-900/6 hover:shadow-md transition-shadow flex flex-col">
                @if($post->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-6 flex flex-col flex-1">
                    @if($post->category)
                    <a href="{{ route('blog.category', $post->category->slug) }}"
                       class="text-[11px] font-semibold text-primary uppercase tracking-widest mb-2 block">
                        {{ $post->category->getLocalName() }}
                    </a>
                    @endif
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
        @endif


        @if($posts->hasPages())
        <div class="mt-12">
            <x-pagination :paginator="$posts" />
        </div>
        @endif

        @endif
    </div>
</div>

@endsection
