@extends('theme::layouts.user')

@section('content')
<div class="space-y-6">

    <div class="flex items-start gap-3 mb-6">
        <a href="{{ route('user.payment.index') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Payment via :gateway', ['gateway' => $gateway->name]) }}</h1>
    </div>

    @if($gateway->instructions)
    <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
        <?php echo nl2br(e($gateway->instructions)); ?>
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('user.payment.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
            @csrf
            <input type="hidden" name="gateway_id" value="{{ $gateway->id }}">
            <input type="hidden" name="currency" value="{{ $gateway->currency_code }}">
            @if(request('plan_id'))
            <input type="hidden" name="plan_id" value="{{ (int) request('plan_id') }}">
            @endif

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Amount') }} ({{ $gateway->currency_code }})</label>
                <div class="relative">
                    @if($gateway->symbol_position === 'before')
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">{{ $gateway->currency_symbol }}</span>
                        <input type="number" name="amount" step="{{ pow(10, -$gateway->decimal_places) }}"
                               min="{{ $gateway->min_amount }}"
                               max="{{ $gateway->max_amount }}"
                               placeholder="{{ $gateway->formatAmount($gateway->min_amount) }}"
                               value="{{ old('amount') }}"
                               class="h-11 w-full rounded-lg border border-gray-300 pl-7 pr-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                               required />
                    @else
                        <input type="number" name="amount" step="{{ pow(10, -$gateway->decimal_places) }}"
                               min="{{ $gateway->min_amount }}"
                               max="{{ $gateway->max_amount }}"
                               placeholder="{{ $gateway->formatAmount($gateway->min_amount) }}"
                               value="{{ old('amount') }}"
                               class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                               required />
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 text-sm">{{ $gateway->currency_symbol }}</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-400">
                    {{ __('Min: :min', ['min' => $gateway->formatAmount($gateway->min_amount)]) }} 
                    &middot; {{ __('Max: :max', ['max' => $gateway->formatAmount($gateway->max_amount)]) }}
                    @if($gateway->fee_flat > 0 || $gateway->fee_percent > 0)
                        &middot; {{ __('Fee:') }}
                        @if($gateway->fee_flat > 0) {{ $gateway->formatAmount($gateway->fee_flat) }} @endif
                        @if($gateway->fee_flat > 0 && $gateway->fee_percent > 0) + @endif
                        @if($gateway->fee_percent > 0) {{ $gateway->fee_percent }}% @endif
                    @endif
                </p>
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            @if($gateway->driver === 'manual')
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Upload payment receipt') }}</label>
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100" />
                <p class="mt-1 text-xs text-gray-400">{{ __('JPG, PNG, WebP or PDF, max 5MB') }}</p>
                @error('receipt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            @error('error')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

            <button type="submit"
                    class="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                @if($gateway->driver === 'manual')
                    {{ __('Submit payment request') }}
                @else
                    {{ __('Continue to :gateway', ['gateway' => $gateway->name]) }}
                @endif
            </button>
        </form>
    </div>

</div>
@endsection