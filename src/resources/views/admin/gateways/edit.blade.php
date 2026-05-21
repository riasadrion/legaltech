@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl">
    <form method="POST" action="{{ route('admin.gateways.update', $gatewayDriver->driver()) }}" class="space-y-5">
        @csrf @method('PUT')

    <div class="flex items-start gap-4">
        <a href="{{ route('admin.gateways.index') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $gatewayDriver->name() }}</h1>
            <p class="text-sm text-gray-400 font-mono">driver: {{ $gatewayDriver->driver() }}</p>
        </div>
    </div>

        {{-- Cards: side by side on lg+, stacked on mobile --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 lg:items-start">

        {{-- General settings --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('General') }}</h3>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-medium text-gray-900">{{ __('Enabled') }}</label>
                        <p class="text-xs text-gray-500">{{ __('Show this gateway to users during payment.') }}</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="enabled" value="0">
                        <input type="checkbox" name="enabled" value="1" class="peer sr-only"
                               {{ $gateway->enabled ? 'checked' : '' }}>
                        <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:bg-brand-600 peer-checked:after:translate-x-5"></div>
                    </label>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Display name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $gateway->name ?? $gatewayDriver->name()) }}"
                           class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('name') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Currency Settings --}}
                <div class="border-t border-gray-100 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('Currency Settings') }}</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Currency Code') }}</label>
                            <input type="text" name="currency_code" maxlength="3" value="{{ old('currency_code', $gateway->currency_code ?? 'USD') }}"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 uppercase {{ $errors->has('currency_code') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('currency_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Currency Symbol') }}</label>
                            <input type="text" name="currency_symbol" maxlength="10" value="{{ old('currency_symbol', $gateway->currency_symbol ?? '$') }}"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('currency_symbol') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('currency_symbol')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Exchange Rate') }}</label>
                            <input type="number" name="exchange_rate" step="0.00000001" min="0.00000001" value="{{ old('exchange_rate', $gateway->exchange_rate ?? 1.0) }}"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('exchange_rate') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('exchange_rate')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-400">{{ __('Base = 1.0') }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Decimal Places') }}</label>
                            <input type="number" name="decimal_places" min="0" max="8" value="{{ old('decimal_places', $gateway->decimal_places ?? 2) }}"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('decimal_places') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('decimal_places')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Symbol Position') }}</label>
                            <select name="symbol_position"
                                    class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('symbol_position') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}">
                                <option value="before" {{ old('symbol_position', $gateway->symbol_position ?? 'before') === 'before' ? 'selected' : '' }}>{{ __('Before ($100)') }}</option>
                                <option value="after" {{ old('symbol_position', $gateway->symbol_position ?? 'before') === 'after' ? 'selected' : '' }}>{{ __('After (100$)') }}</option>
                            </select>
                            @error('symbol_position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('Amount Limits & Fees') }}</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Min Amount') }}</label>
                            <input type="number" name="min_amount" value="{{ old('min_amount', $gateway->min_amount ?? 1.00) }}" min="0.01" step="0.01"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('min_amount') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('min_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Max Amount') }}</label>
                            <input type="number" name="max_amount" value="{{ old('max_amount', $gateway->max_amount ?? 10000) }}" min="0.01" step="0.01"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('max_amount') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('max_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Flat Fee') }}</label>
                            <input type="number" name="fee_flat" value="{{ old('fee_flat', $gateway->fee_flat ?? 0) }}" min="0" step="0.01"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('fee_flat') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('fee_flat')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-400">{{ __('Fixed fee amount') }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Percentage fee (%)') }}</label>
                            <input type="number" name="fee_percent" value="{{ old('fee_percent', $gateway->fee_percent ?? 0) }}" min="0" max="100" step="0.01"
                                   class="h-9 w-full rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('fee_percent') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                            @error('fee_percent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Sort order') }}</label>
                    <input type="number" name="sort" value="{{ old('sort', $gateway->sort ?? 0) }}" min="0"
                           class="h-9 w-32 rounded-lg border px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('sort') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
                    @error('sort')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($gatewayDriver->driver() === 'manual')
                <div class="border-t border-gray-100 pt-4">
                    <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Instructions (shown to user)') }}</label>
                    <textarea name="instructions" rows="4"
                              class="w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('instructions') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}">{{ old('instructions', $gateway->instructions) }}</textarea>
                    @error('instructions')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>
        </div>

        {{-- Credentials --}}
        @if($gatewayDriver->driver() !== 'manual')
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('API Credentials') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Stored encrypted. Leave blank to keep existing value.') }}</p>
            </div>
            <div class="px-5 py-5 space-y-4">

                @php $d = $gatewayDriver->driver(); @endphp

                @if($d === 'stripe')
                    <x-gateway-credential label="{{ __('Secret key') }}"        name="secret_key"      type="password" placeholder="sk_live_… or sk_test_…" />
                    <x-gateway-credential label="{{ __('Publishable key') }}"   name="publishable_key" type="text"     placeholder="pk_live_… or pk_test_…" />
                    <x-gateway-credential label="{{ __('Webhook secret') }}"    name="webhook_secret"  type="password" placeholder="whsec_…" />

                @elseif($d === 'paypal')
                    <x-gateway-credential label="{{ __('Business Email') }}"    name="business_email"  type="email"    placeholder="your@business.com" />
                    <x-gateway-sandbox-toggle :gateway="$gateway" />

                @elseif($d === 'razorpay')
                    <x-gateway-credential label="{{ __('Key ID') }}"            name="key_id"          type="text"     placeholder="rzp_live_… or rzp_test_…" />
                    <x-gateway-credential label="{{ __('Key Secret') }}"        name="key_secret"      type="password" placeholder="{{ __('Razorpay Key Secret') }}" />
                    <x-gateway-credential label="{{ __('Webhook Secret') }}"    name="webhook_secret"  type="password" placeholder="{{ __('Set in Razorpay dashboard') }}" />

                @elseif($d === 'paystack')
                    <x-gateway-credential label="{{ __('Secret Key') }}"        name="secret_key"      type="password" placeholder="sk_live_… or sk_test_…" />
                    <x-gateway-credential label="{{ __('Public Key') }}"        name="public_key"      type="text"     placeholder="pk_live_… or pk_test_…" />
                    <x-gateway-credential label="{{ __('Webhook Secret') }}"    name="webhook_secret"  type="password" placeholder="{{ __('Set in Paystack dashboard') }}" />

                @elseif($d === 'flutterwave')
                    <x-gateway-credential label="{{ __('Secret Key') }}"        name="secret_key"      type="password" placeholder="FLWSECK_TEST-… or FLWSECK-…" />
                    <x-gateway-credential label="{{ __('Public Key') }}"        name="public_key"      type="text"     placeholder="FLWPUBK_TEST-… or FLWPUBK-…" />
                    <x-gateway-credential label="{{ __('Webhook Hash (verif-hash)') }}" name="webhook_secret" type="password" placeholder="{{ __('Custom hash set in Flutterwave dashboard') }}" />

                @elseif($d === 'mollie')
                    <x-gateway-credential label="{{ __('API Key') }}"           name="api_key"         type="password" placeholder="live_… or test_…" />
                    <p class="text-xs text-gray-400">{{ __('Mollie does not sign webhook bodies — verification is done by fetching payment status from the API on each call.') }}</p>

                @elseif($d === 'mercadopago')
                    <x-gateway-credential label="{{ __('Access Token') }}"      name="access_token"    type="password" placeholder="APP_USR-… or TEST-…" />
                    <x-gateway-credential label="{{ __('Webhook Secret (x-signature)') }}" name="webhook_secret" type="password" placeholder="{{ __('Set in Mercado Pago developer panel') }}" />
                    <x-gateway-sandbox-toggle :gateway="$gateway" />

                @elseif($d === 'coinbase_commerce')
                    <x-gateway-credential label="{{ __('API Key') }}"           name="api_key"         type="password" placeholder="{{ __('Coinbase Commerce API key') }}" />
                    <x-gateway-credential label="{{ __('Webhook Secret') }}"    name="webhook_secret"  type="password" placeholder="{{ __('Shared secret from Coinbase Commerce') }}" />

                @elseif($d === 'coingate')
                    <x-gateway-credential label="{{ __('API Token') }}"         name="api_token"       type="password" placeholder="{{ __('CoinGate API token') }}" />
                    <x-gateway-credential label="{{ __('Receive Currency') }}"  name="receive_currency" type="text"    placeholder="e.g. USD, EUR, BTC" />
                    <x-gateway-sandbox-toggle :gateway="$gateway" />
                    <p class="text-xs text-gray-400">{{ __('CoinGate does not sign IPN bodies. Verification relies on HTTPS-only delivery.') }}</p>

                @elseif($d === 'nowpayments')
                    <x-gateway-credential label="{{ __('API Key') }}"           name="api_key"         type="password" placeholder="{{ __('NOWPayments API key') }}" />
                    <x-gateway-credential label="{{ __('IPN Secret') }}"        name="ipn_secret"      type="password" placeholder="{{ __('IPN secret from NOWPayments dashboard') }}" />
                    <x-gateway-credential label="{{ __('Pay Currency') }}"      name="pay_currency"    type="text"     placeholder="e.g. btc, eth, usdt" />
                    <x-gateway-sandbox-toggle :gateway="$gateway" />

                @elseif($d === 'izipay')
                    <x-gateway-credential label="{{ __('Shop ID') }}"           name="shop_id"         type="text"     placeholder="{{ __('Izipay Shop ID') }}" />
                    <x-gateway-credential label="{{ __('API Key (password)') }}" name="api_key"        type="password" placeholder="{{ __('Izipay API key / password') }}" />
                    <x-gateway-credential label="{{ __('Public Key') }}"        name="public_key"      type="text"     placeholder="{{ __('Izipay public key (for JS form)') }}" />
                    <x-gateway-sandbox-toggle :gateway="$gateway" />

                @endif

            </div>
        </div>
        @endif

        </div>{{-- end grid --}}

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                {{ __('Save changes') }}
            </button>
            <a href="{{ route('admin.gateways.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                {{ __('Cancel') }}
            </a>
        </div>

    </form>

</div>
@endsection
