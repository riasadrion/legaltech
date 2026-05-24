@extends('theme::layouts.user')

@section('breadcrumb', __('Preview'))

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-start gap-3">
        <a href="{{ route('user.contracts.wizard', $template) }}"
           class="flex items-center justify-center h-9 w-9 rounded-xl glass border border-white/10 text-white/60 hover:text-white transition shrink-0 mt-1">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="ud-page-title !mb-0">{{ __('Review your details') }}</h1>
            <p class="text-[12px] text-white/45 mt-1">{{ __('Confirm everything is correct before generating the contract. Generation deducts') }} <span class="text-lime/80">{{ $template->credits_cost }} {{ __('credit(s)') }}</span>.</p>
        </div>
    </div>

    @include('user.contracts._stepper', ['step' => 4])

    {{-- Filled fields --}}
    <div class="rounded-2xl glass overflow-hidden">
        <div class="px-5 py-3 border-b border-white/8 flex items-center justify-between">
            <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">{{ __('Contract Details') }}</h2>
            <a href="{{ route('user.contracts.wizard', $template) }}" class="text-[12px] text-lime hover:underline">{{ __('Edit') }}</a>
        </div>
        <div class="divide-y divide-white/6">
            @foreach($template->fields as $field)
                @php $value = $fields[$field->key] ?? null; @endphp
                <div class="px-5 py-3 grid grid-cols-3 gap-3 items-start">
                    <div class="text-[12px] text-white/45">{{ $field->label }}</div>
                    <div class="col-span-2 text-[13px] text-white/85 break-words">
                        @if($value)
                            @if($field->type === 'textarea')
                                <div class="whitespace-pre-wrap">{{ $value }}</div>
                            @elseif($field->type === 'date')
                                {{ \Illuminate\Support\Carbon::parse($value)->format('d M Y') }}
                            @else
                                {{ $value }}
                            @endif
                        @else
                            <span class="text-white/30 italic">{{ __('Not provided') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Parties --}}
    <div class="rounded-2xl glass overflow-hidden">
        <div class="px-5 py-3 border-b border-white/8 flex items-center justify-between">
            <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">{{ __('Parties') }}</h2>
            <a href="{{ route('user.contracts.wizard', $template) }}" class="text-[12px] text-lime hover:underline">{{ __('Edit') }}</a>
        </div>
        <div class="divide-y divide-white/6">
            @foreach($parties as $i => $party)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-[13px] text-white">{{ $party['name'] }}</div>
                        <div class="text-[11px] text-white/40">{{ $party['email'] }}</div>
                    </div>
                    <span class="text-[10px] font-mono uppercase text-white/40">{{ $party['role'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Generate button --}}
    <form method="POST" action="{{ route('user.contracts.store') }}" class="flex items-center justify-between gap-3">
        @csrf
        <input type="hidden" name="template_id" value="{{ $template->id }}">
        <a href="{{ route('user.contracts.wizard', $template) }}"
           class="text-[13px] text-white/50 hover:text-white">{{ __('Go back & edit') }}</a>
        <button type="submit" class="btn-lime rounded-xl px-5 py-3 text-[13px] font-semibold flex items-center gap-2">
            <x-heroicon-o-sparkles class="h-4 w-4" />
            {{ __('Generate Contract') }}
        </button>
    </form>

</div>
@endsection
