@extends('theme::layouts.frontend')

@section('title', __('Blog'))

@section('content')


<div class="relative bg-ink-950 pt-36 pb-20 overflow-hidden">
    <div class="absolute inset-0 bg-grid opacity-[0.03] pointer-events-none"></div>
    <div class="radial-fade pointer-events-none"></div>
    <div class="relative mx-auto max-w-4xl px-6 text-center">
        <div class="inline-flex items-center gap-2 rounded-full glass px-4 py-1.5 text-xs font-medium text-white/50 mb-6">
            <x-heroicon-o-document-text class="h-4 w-4 text-lime" />@lang('Articles & insights')
        </div>
        <h1 class="font-display text-4xl sm:text-5xl md:text-6xl text-white mb-4">@lang('The LegalTech Blog')</h1>
        <p class="text-lg text-white/40 max-w-xl mx-auto">
            @lang('Contract law, AI automation, and the future of legal work — written for teams who ship.')
        </p>
    </div>
</div>

<div class="bg-ink-950 pb-4">
    <div class="mx-auto max-w-6xl px-6">


        @if($categories->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 py-6 border-b border-white/8">
            <a href="{{ route('blog.index') }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition
                      {{ !request()->routeIs('blog.category') && !request()->routeIs('blog.tag')
                         ? 'bg-lime text-ink-900' : 'glass text-white/60 hover:text-white' }}">
                @lang('All')
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('blog.category', $cat->slug) }}"
               class="rounded-full px-4 py-1.5 text-sm font-medium transition glass text-white/60 hover:text-white">
                {{ $cat->getLocalName() }}
            </a>
            @endforeach

            <a href="{{ route('blog.rss') }}"
               class="ml-auto flex items-center gap-1.5 text-sm text-white/30 hover:text-white/60 transition">
                <x-heroicon-o-rss class="h-4 w-4" />RSS
            </a>
        </div>
        @endif

    </div>
</div>


<div class="bg-ink-950 py-16">
    <div class="mx-auto max-w-6xl px-6">

        @if($posts->isEmpty())
        <div class="py-24 text-center text-white/30">
            <x-heroicon-o-document-text class="h-12 w-12 mb-4 mx-auto block text-white/10" />
            @lang('No posts yet.')
        </div>
        @else

        @foreach($posts as $post)
        @if($loop->first)

        <article class="group relative glass rounded-3xl overflow-hidden mb-8 lift">
            <a href="{{ route('blog.show', $post->slug) }}" class="flex flex-col md:flex-row">
                @if($post->cover_image)
                <div class="md:w-1/2 aspect-video md:aspect-auto overflow-hidden">
                    <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-8 md:p-10 {{ $post->cover_image ? 'md:w-1/2' : 'w-full' }} flex flex-col justify-center">
                    @if($post->category)
                    <span class="text-[11px] font-semibold text-lime uppercase tracking-widest mb-3">
                        {{ $post->category->getLocalName() }}
                    </span>
                    @endif
                    <h2 class="font-display text-3xl text-white leading-tight mb-4 group-hover:text-lime transition">
                        {{ $post->getLocalTitle() }}
                    </h2>
                    @if($post->getLocalExcerpt())
                    <p class="text-white/50 leading-relaxed mb-6 line-clamp-3">{{ $post->getLocalExcerpt() }}</p>
                    @endif
                    <div class="flex items-center gap-3 text-sm text-white/30">
                        <time>{{ $post->published_at->format('M j, Y') }}</time>
                    </div>
                    <div class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-lime">
                        @lang('Read article') <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                    </div>
                </div>
            </a>
        </article>
        @if($posts->count() > 1)
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @endif
        @else

            <article class="group glass rounded-2xl overflow-hidden lift flex flex-col">
                @if($post->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ upload_url($post->cover_image) }}" alt="{{ $post->getLocalTitle() }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-6 flex flex-col flex-1">
                    @if($post->category)
                    <a href="{{ route('blog.category', $post->category->slug) }}"
                       class="text-[11px] font-semibold text-lime uppercase tracking-widest mb-2 block">
                        {{ $post->category->getLocalName() }}
                    </a>
                    @endif
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
        @endif
        @endforeach
        @if($posts->count() > 1)
        </div>
        @endif


        @if($posts->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $posts->links() }}
        </div>
        @endif

        @endif
    </div>
</div>

@endsection
