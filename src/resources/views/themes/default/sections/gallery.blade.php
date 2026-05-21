@php $d = $section->getLocaleData(); @endphp
{{-- Section: gallery --}}
<section class="py-16">
    <div class="mx-auto max-w-6xl px-4">
        <p class="text-xs text-gray-400 font-mono">[gallery section — customize this view]</p>
        @if(isset($d['heading']) && $d['heading'])
            <h2 class="text-3xl font-bold text-gray-900 mt-2">{{ $d['heading'] }}</h2>
        @endif
    </div>
</section>
