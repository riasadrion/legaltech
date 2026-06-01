@extends('theme::layouts.user')

@section('breadcrumb', __('Add Funds'))

@section('content')
<div class="max-w-lg space-y-5">


    <div class="flex items-start gap-3">
        <a href="{{ route('user.payment.create', array_filter(['plan_id' => request('plan_id')])) }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg glass border border-white/10 text-white/60 hover:text-white transition">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="ud-page-title !mb-0">{{ $gateway->name }}</h1>
            <p class="text-[12px] text-white/45">@lang('Enter amount to add to your wallet')</p>
        </div>
    </div>


    @if($gateway->instructions)
    <div class="rounded-xl border border-white/10 bg-white/4 px-5 py-4 text-[13px] text-white/70">
        <?php echo nl2br(e($gateway->instructions)) ?>
    </div>
    @endif


    <div class="rounded-2xl glass overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-white/8">
            @php $logo = $gateway->logoUrl(); @endphp
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $gateway->name }}" class="w-8 h-8 rounded-lg object-contain shrink-0">
            @else
                <div class="w-8 h-8 rounded-lg bg-white/6 border border-white/10 grid place-items-center shrink-0">
                    <x-heroicon-o-credit-card class="h-4 w-4 text-white/50" />
                </div>
            @endif
            <div>
                <div class="text-[13px] font-medium text-white">{{ $gateway->name }}</div>
                <div class="text-[12px] text-white/60">
                    {{ __('Min: :min · Max: :max', ['min' => $gateway->formatAmount($gateway->min_amount), 'max' => $gateway->formatAmount($gateway->max_amount)]) }}
                    @if($gateway->fee_flat > 0 || $gateway->fee_percent > 0)
                        · @lang('Fee:')
                        @if($gateway->fee_flat > 0){{ $gateway->formatAmount($gateway->fee_flat) }}@endif
                        @if($gateway->fee_flat > 0 && $gateway->fee_percent > 0) + @endif
                        @if($gateway->fee_percent > 0){{ $gateway->fee_percent }}%@endif
                    @else
                        · <span class="text-lime/80">@lang('No fee')</span>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.payment.store') }}" enctype="multipart/form-data" class="px-5 py-5 space-y-4">
            @csrf
            <input type="hidden" name="gateway_id" value="{{ $gateway->id }}">
            <input type="hidden" name="currency" value="{{ $gateway->currency_code }}">
            @if(request('plan_id'))
            <input type="hidden" name="plan_id" value="{{ (int) request('plan_id') }}">
            @endif

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('Amount') ({{ $gateway->currency_code }})
                </label>
                <div class="relative">
                    @if($gateway->symbol_position === 'before')
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-white/70 text-[13px] pointer-events-none select-none leading-none pb-px">{{ $gateway->currency_symbol }}</span>
                        <input type="number" name="amount"
                               step="{{ pow(10, -$gateway->decimal_places) }}"
                               min="{{ $gateway->min_amount }}"
                               max="{{ $gateway->max_amount }}"
                               placeholder="{{ $gateway->min_amount }}"
                               value="{{ old('amount') }}"
                               class="ud-input ud-input-symbol-before"
                               required />
                    @else
                        <input type="number" name="amount"
                               step="{{ pow(10, -$gateway->decimal_places) }}"
                               min="{{ $gateway->min_amount }}"
                               max="{{ $gateway->max_amount }}"
                               placeholder="{{ $gateway->min_amount }}"
                               value="{{ old('amount') }}"
                               class="ud-input pr-10"
                               required />
                        <span class="absolute inset-y-0 right-3.5 flex items-center text-white/70 text-[13px] pointer-events-none select-none">{{ $gateway->currency_symbol }}</span>
                    @endif
                </div>
                @error('amount')<p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>@enderror
            </div>

            @if($gateway->driver === 'manual')
            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">@lang('Upload payment receipt')</label>
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-[13px] text-white/50
                              file:mr-3 file:rounded-lg file:border-0 file:bg-lime/10 file:px-3 file:py-2
                              file:text-[12px] file:font-medium file:text-lime hover:file:bg-lime/20" />
                <p class="mt-1 text-[11px] text-white/35">@lang('JPG, PNG, WebP or PDF, max 5MB')</p>
                @error('receipt')<p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>@enderror
            </div>
            @endif

            @error('error')<p class="text-[12px] text-red-400">{{ $message }}</p>@enderror

            <button type="submit" class="btn-lime rounded-xl w-full py-2.5 text-[13px] font-semibold mt-1">
                @if($gateway->driver === 'manual')
                    @lang('Submit payment request')
                @else
                    {{ __('Continue to :gateway', ['gateway' => $gateway->name]) }}
                @endif
            </button>
        </form>
    </div>

</div>
@endsection
