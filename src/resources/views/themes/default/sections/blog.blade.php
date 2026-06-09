@php
    use App\Models\BlogPost;

    $d        = $section->data ?? [];
    $eyebrow  = $d['eyebrow']   ?? '';
    $heading  = $d['heading']   ?? '';
    $linkText = $d['link_text'] ?? __('All articles');
    $linkUrl  = $d['link_url']  ?? route('blog.index');

    $posts = BlogPost::published()
        ->select(['id', 'slug', 'title', 'excerpt', 'published_at', 'blog_category_id'])
        ->with('category:id,name,slug')
        ->latest('published_at')
        ->limit(3)
        ->get();

    $featured = $posts[0] ?? null;
    $side     = $posts->slice(1, 2);
@endphp

<section id="blog" class="relative py-28 {{ $sectionBg ?? 'bg-cream-100' }} text-ink-900">
    @if(($sectionBg ?? '') === 'bg-cream-50')<div class="absolute inset-0 bg-grid-cream pointer-events-none"></div>@endif
    <div class="relative mx-auto max-w-7xl px-6">


        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <x-theme::section-header :eyebrow="$eyebrow" :heading="$heading" />
            <a href="{{ $linkUrl }}" class="hidden md:inline-flex items-center gap-2 rounded-full border border-ink-900/12 px-5 py-3 text-sm text-ink-700 hover:text-ink-950 hover:border-ink-900/25 transition">
                {{ __($linkText) }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
            </a>
        </div>

        @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


            @if($featured)
            <a href="{{ route('blog.show', ['slug' => $featured->slug]) }}"
               class="md:col-span-2 rounded-3xl bg-ink-900 text-white lift relative overflow-hidden flex flex-col" data-reveal="right">
                <div class="absolute -left-20 -bottom-20 w-80 h-80 glow-primary opacity-60 pointer-events-none"></div>
                <div class="p-10 relative flex flex-col justify-between flex-1 min-h-[320px] w-full">
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-6 w-full">
                            <span class="text-[10px] font-mono uppercase tracking-widest rounded-full bg-primary text-white font-semibold px-3 py-1">
                                {{ $featured->category?->getLocalName() ?? __('Article') }}
                            </span>
                            <span class="text-[11px] font-mono text-white/40">
                                {{ $featured->published_at?->format('M j, Y') }}
                            </span>
                        </div>
                        <h3 class="font-display text-5xl md:text-6xl leading-[0.93] w-full text-white">{{ $featured->getLocalTitle() }}</h3>
                        <p class="text-white/55 text-[16px] mt-6 leading-relaxed line-clamp-4 w-full">
                            {{ \Illuminate\Support\Str::limit(strip_tags($featured->getLocalExcerpt()), 400) }}
                        </p>
                    </div>
                    <span class="mt-8 inline-flex items-center gap-2 text-sm text-primary font-medium relative z-10">
                        @lang('Read article') <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                    </span>
                </div>
            </a>
            @endif


            <div class="flex flex-col gap-4">
                @foreach($side as $post)
                <a href="{{ route('blog.show', ['slug' => $post->slug]) }}"
                   class="flex-1 rounded-3xl glass-cream lift relative overflow-hidden block" data-reveal="left" data-reveal-delay="{{ $loop->index * 100 }}">
                    <div class="p-7 relative h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-mono uppercase tracking-widest rounded-full bg-ink-900/5 border border-ink-900/10 px-2.5 py-1 text-ink-600">
                                {{ $post->category?->getLocalName() ?? __('Article') }}
                            </span>
                            <span class="text-[10px] font-mono text-ink-400">
                                {{ $post->published_at?->format('M j') }}
                            </span>
                        </div>
                        <h3 class="font-display text-2xl leading-snug line-clamp-2">{{ $post->getLocalTitle() }}</h3>
                        <p class="text-ink-500 text-sm mt-2 leading-relaxed flex-1 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->getLocalExcerpt()), 110) }}
                        </p>
                        <span class="mt-5 inline-flex items-center gap-2 text-sm text-primary font-medium">
                            @lang('Read') <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>


        <div class="mt-8 flex justify-center md:hidden">
            <a href="{{ $linkUrl }}" class="inline-flex items-center gap-2 rounded-full border border-ink-900/12 px-5 py-3 text-sm text-ink-700">
                {{ __($linkText) }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
            </a>
        </div>
        @endif

    </div>
</section>
