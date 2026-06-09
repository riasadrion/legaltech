@php
    /** $step int 1..5 */
    $steps = [
        1 => __('Category'),
        2 => __('Template'),
        3 => __('Fields'),
        4 => __('Preview'),
        5 => __('Generated'),
    ];
@endphp
<div class="rounded-2xl glass px-4 py-4">
    <ol class="flex items-center w-full overflow-x-auto">
        @foreach($steps as $i => $label)
            @php
                $state = $i < $step ? 'done' : ($i === $step ? 'active' : 'pending');
            @endphp
            <li class="flex items-center {{ $loop->last ? '' : 'flex-1' }} shrink-0">
                <div class="flex items-center gap-2">
                    <span class="grid place-items-center h-7 w-7 rounded-full text-[11px] font-semibold border transition
                        @if($state === 'done') bg-primary/15 border-primary/40 text-primary
                        @elseif($state === 'active') bg-primary border-primary text-black
                        @else bg-white/4 border-white/10 text-white/40 @endif">
                        @if($state === 'done')
                            <x-heroicon-o-check class="h-3.5 w-3.5" />
                        @else
                            {{ $i }}
                        @endif
                    </span>
                    <span class="text-[12px]
                        @if($state === 'active') inline text-white font-medium
                        @elseif($state === 'done') hidden sm:inline text-white/70
                        @else hidden sm:inline text-white/35 @endif">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <span class="flex-1 mx-3 h-px bg-white/10"></span>
                @endif
            </li>
        @endforeach
    </ol>
</div>
