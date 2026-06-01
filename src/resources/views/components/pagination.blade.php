@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex items-center justify-between px-1 py-3">
        <p class="text-sm text-white/40">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </p>
        <div class="flex gap-1">

            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-sm text-white/20 border border-white/10 bg-transparent">&#8249;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-white/60 border border-white/15 bg-transparent hover:bg-white/8 hover:text-white transition">&#8249;</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded-lg text-sm bg-brand-600 text-white border border-brand-600">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-sm text-white/60 border border-white/15 bg-transparent hover:bg-white/8 hover:text-white transition">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-white/60 border border-white/15 bg-transparent hover:bg-white/8 hover:text-white transition">&#8250;</a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-sm text-white/20 border border-white/10 bg-transparent">&#8250;</span>
            @endif
        </div>
    </div>
@endif
