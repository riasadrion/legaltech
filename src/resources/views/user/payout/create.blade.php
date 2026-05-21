@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-lg px-4 sm:px-6 py-10">

    <div class="flex items-start gap-3 mb-6">
        <a href="{{ route('user.payout.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900">Request Payout</h1>
    </div>

    <p class="mb-5 text-sm text-gray-500">Available balance: <strong class="text-gray-900">{{ $balance->format() }}</strong></p>

    @if($methods->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-10 text-center shadow-sm">
        <p class="text-sm text-gray-500">No payout methods are currently available.</p>
    </div>
    @else

    <form method="POST" action="{{ route('user.payout.store') }}"
          x-data="payoutForm()"
          class="space-y-6">
        @csrf

        {{-- Method picker --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-gray-900">Choose method</h2>
            </div>
            <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2">
                @foreach($methods as $method)
                <label class="cursor-pointer">
                    <input type="radio" name="method_id" value="{{ $method->id }}"
                           x-model="selectedMethod"
                           @change="selectMethod({{ $method->id }})"
                           class="sr-only"
                           @if(old('method_id') == $method->id) checked @endif />
                    <div :class="selectedMethod == {{ $method->id }} ? 'border-brand-500 ring-1 ring-brand-400' : 'border-gray-200'"
                         class="rounded-xl border bg-white p-4 transition-all hover:border-brand-300">
                        <p class="font-semibold text-gray-900 text-sm">{{ $method->getLocaleName() }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">
                            Min {{ formatAmount($method->min_amount) }}
                            @if($method->getLocaleDelayText()) · {{ $method->getLocaleDelayText() }} @endif
                        </p>
                    </div>
                </label>
                @endforeach
            </div>
            @error('method_id')<p class="px-5 pb-3 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Amount --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-gray-900">Amount</h2>
            </div>
            <div class="px-5 py-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">$</span>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           value="{{ old('amount') }}"
                           placeholder="0.00"
                           class="h-11 w-full rounded-lg border border-gray-300 pl-7 pr-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                </div>
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Dynamic payout fields per method --}}
        @foreach($methods as $method)
        @if($method->form && $method->form->fields->isNotEmpty())
        <div x-show="selectedMethod == {{ $method->id }}"
             class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-gray-900">Payout details</h2>
            </div>
            <div class="px-5 py-5 space-y-4">
                <x-form-renderer :form="$method->form" />
            </div>
        </div>
        @endif
        @endforeach

        @error('error')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

        <button type="submit"
                class="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            Submit payout request
        </button>
    </form>

    @endif
</div>
@endsection

@push('scripts')
<script>
'use strict';
function payoutForm() {
    return {
        selectedMethod: {{ old('method_id', $methods->first()?->id ?? 'null') }},
        selectMethod(id) {
            'use strict';
            this.selectedMethod = id;
        },
    };
}
</script>
@endpush
