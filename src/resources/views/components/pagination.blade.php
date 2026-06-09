@props(['paginator', 'showTotal' => true])

@if ($paginator->hasPages())
    <div class="flex items-center justify-between px-1 py-3">
        @if($showTotal)
        <p class="text-sm text-ink-400">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </p>
        @endif
        <div class="flex gap-1 {{ $showTotal ? '' : 'ml-auto' }}">

            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-sm text-ink-300 border border-ink-900/10 bg-transparent">&#8249;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-ink-500 border border-ink-900/15 bg-white hover:border-primary/40 hover:text-primary transition">&#8249;</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded-lg text-sm bg-primary text-white border border-primary">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-sm text-ink-500 border border-ink-900/15 bg-white hover:border-primary/40 hover:text-primary transition">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-ink-500 border border-ink-900/15 bg-white hover:border-primary/40 hover:text-primary transition">&#8250;</a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-sm text-ink-300 border border-ink-900/10 bg-transparent">&#8250;</span>
            @endif
        </div>
    </div>
@endif
