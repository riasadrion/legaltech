@php
    use App\Domain\Blog\Models\BlogPost;

    $d        = $section->data ?? [];
    $eyebrow  = $d['eyebrow']   ?? '';
    $heading  = $d['heading']   ?? '';
    $linkText = $d['link_text'] ?? __('All articles');
    $linkUrl  = $d['link_url']  ?? route('blog.index');

    $posts = BlogPost::published()
        ->with('category')
        ->latest('published_at')
        ->limit(3)
        ->get();

    $featured = $posts[0] ?? null;
    $side     = $posts->slice(1, 2);
@endphp

<section id="blog" class="relative py-28">
    <div class="mx-auto max-w-7xl px-6">


        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <div>
                @if($eyebrow)
                    <div class="text-[11px] font-mono uppercase tracking-[.25em] text-lime mb-3">— {{ __($eyebrow) }}</div>
                @endif
                @if($heading)
                    <h2 class="font-display text-4xl md:text-5xl lg:text-6xl leading-[0.95]"><?php echo __($heading); ?></h2>
                @endif
            </div>
            <a href="{{ $linkUrl }}" class="hidden md:inline-flex items-center gap-2 rounded-full border border-white/15 px-5 py-3 text-sm text-white/70 hover:text-white hover:border-white/30 transition">
                {{ __($linkText) }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
            </a>
        </div>

        @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


            @if($featured)
            <a href="{{ route('blog.show', ['slug' => $featured->slug]) }}"
               class="md:col-span-2 rounded-3xl glass lift relative overflow-hidden flex" data-reveal="right">
                <div class="absolute -left-20 -bottom-20 w-80 h-80 glow-lime opacity-40 pointer-events-none"></div>
                <div class="p-8 relative flex flex-col w-full">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-[10px] font-mono uppercase tracking-widest rounded-full bg-lime text-ink-900 font-semibold px-3 py-1">
                            {{ $featured->category?->getLocalName() ?? __('Article') }}
                        </span>
                        <span class="text-[11px] font-mono text-white/35">
                            {{ $featured->published_at?->format('M j, Y') }}
                        </span>
                    </div>
                    <h3 class="font-display text-4xl md:text-5xl leading-[0.95] line-clamp-2">{{ $featured->getLocalTitle() }}</h3>
                    <p class="text-white/55 text-[15px] mt-5 leading-relaxed flex-1 line-clamp-4">
                        {{ \Illuminate\Support\Str::limit(strip_tags($featured->getLocalExcerpt()), 320) }}
                    </p>
                    <span class="mt-8 inline-flex items-center gap-2 text-sm text-lime font-medium relative z-10">
                        {{ __('Read article') }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                    </span>
                </div>
            </a>
            @endif


            <div class="flex flex-col gap-4">
                @foreach($side as $post)
                <a href="{{ route('blog.show', ['slug' => $post->slug]) }}"
                   class="flex-1 rounded-3xl glass lift relative overflow-hidden block" data-reveal="left" data-reveal-delay="{{ $loop->index * 100 }}">
                    <div class="p-7 relative h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-mono uppercase tracking-widest rounded-full bg-white/8 border border-white/10 px-2.5 py-1 text-white/60">
                                {{ $post->category?->getLocalName() ?? __('Article') }}
                            </span>
                            <span class="text-[10px] font-mono text-white/35">
                                {{ $post->published_at?->format('M j') }}
                            </span>
                        </div>
                        <h3 class="font-display text-2xl leading-snug line-clamp-2">{{ $post->getLocalTitle() }}</h3>
                        <p class="text-white/50 text-sm mt-2 leading-relaxed flex-1 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->getLocalExcerpt()), 110) }}
                        </p>
                        <span class="mt-5 inline-flex items-center gap-2 text-sm text-lime font-medium">
                            {{ __('Read') }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>


        <div class="mt-8 flex justify-center md:hidden">
            <a href="{{ $linkUrl }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 px-5 py-3 text-sm text-white/70">
                {{ __($linkText) }} <x-heroicon-o-arrow-right class="h-4 w-4 inline" />
            </a>
        </div>
        @endif

    </div>
</section>
