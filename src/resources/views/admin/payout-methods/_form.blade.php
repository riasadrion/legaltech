@php $m = $method ?? null; @endphp

{{-- Basic info --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-3.5">
        <h2 class="text-sm font-semibold text-gray-900">Basic info</h2>
    </div>
    <div class="grid grid-cols-1 gap-5 px-5 py-5 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name[en]" value="{{ old('name.en', $m?->name['en'] ?? '') }}"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('name.en') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" required />
            @error('name.en')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Delay label</label>
            <input type="text" name="delay_text[en]" value="{{ old('delay_text.en', $m?->delay_text['en'] ?? '') }}"
                   placeholder="e.g. 1–3 business days"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('delay_text.en') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
            @error('delay_text.en')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Min amount (cents) <span class="text-red-500">*</span></label>
            <input type="number" name="min_amount" value="{{ old('min_amount', $m?->min_amount ?? 1000) }}" min="0"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('min_amount') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" required />
            @error('min_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Max amount (cents) <span class="text-red-500">*</span></label>
            <input type="number" name="max_amount" value="{{ old('max_amount', $m?->max_amount ?? 1000000) }}" min="1"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('max_amount') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" required />
            @error('max_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Flat fee (cents)</label>
            <input type="number" name="fee_flat" value="{{ old('fee_flat', $m?->fee_flat ?? 0) }}" min="0"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('fee_flat') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
            @error('fee_flat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Percent fee (%)</label>
            <input type="number" name="fee_percent" value="{{ old('fee_percent', $m?->fee_percent ?? 0) }}" min="0" max="100" step="0.01"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('fee_percent') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
            @error('fee_percent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">Sort order</label>
            <input type="number" name="sort" value="{{ old('sort', $m?->sort ?? 0) }}" min="0"
                   class="h-9 w-full rounded-lg border px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 {{ $errors->has('sort') ? 'border-red-400 focus:border-red-400' : 'border-gray-300 focus:border-brand-500' }}" />
            @error('sort')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-end gap-6 pb-1">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="kyc_required" value="1" @checked(old('kyc_required', $m?->kyc_required ?? true))
                       class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                KYC required
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $m?->enabled ?? true))
                       class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                Enabled
            </label>
        </div>
    </div>
</div>

{{-- Form fields (dynamic) --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-3.5">
        <h2 class="text-sm font-semibold text-gray-900">Payout detail fields</h2>
        <p class="text-xs text-gray-400 mt-0.5">Fields the user must fill in when requesting a payout (e.g. bank account, wallet address).</p>
    </div>
    <div class="px-5 py-5">
        <x-form-builder :form="$m?->form" :locales="['en']" />
    </div>
</div>
