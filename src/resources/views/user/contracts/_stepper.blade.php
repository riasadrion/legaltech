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
    <ol class="flex items-center gap-2 sm:gap-3 overflow-x-auto">
        @foreach($steps as $i => $label)
            @php
                $state = $i < $step ? 'done' : ($i === $step ? 'active' : 'pending');
            @endphp
            <li class="flex items-center gap-2 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="grid place-items-center h-7 w-7 rounded-full text-[11px] font-semibold border transition
                        @if($state === 'done') bg-lime/15 border-lime/40 text-lime
                        @elseif($state === 'active') bg-lime border-lime text-black
                        @else bg-white/4 border-white/10 text-white/40 @endif">
                        @if($state === 'done')
                            <x-heroicon-o-check class="h-3.5 w-3.5" />
                        @else
                            {{ $i }}
                        @endif
                    </span>
                    <span class="text-[12px] hidden sm:inline
                        @if($state === 'active') text-white font-medium
                        @elseif($state === 'done') text-white/70
                        @else text-white/35 @endif">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <span class="h-px w-6 sm:w-10 bg-white/10"></span>
                @endif
            </li>
        @endforeach
    </ol>
</div>
