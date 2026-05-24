<div class="grid grid-cols-3 gap-6">
    {{-- Left panel --}}
    <div class="col-span-2 space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <x-form.input name="name" label="{{ __('Plan name') }}" value="{{ old('name', $plan?->name) }}" required />

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('description', $plan?->description) }}</textarea>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Extra perks') }}</label>
                <p class="text-xs text-gray-500 mb-3">{{ __('Qualitative perks only — e.g. "Priority support", "SSO & SAML", "Dedicated account manager". Contract counts and AI drafts auto-display from the fields above; do not list them here.') }}</p>
                <div id="features-list" class="space-y-2">
                    @foreach(old('features', $plan?->features ?? []) as $feat)
                    <div class="feature-row flex gap-2">
                        <input type="text" name="features[]" value="{{ $feat }}" placeholder="{{ __('Feature text…') }}"
                               class="flex-1 h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <button type="button" class="remove-feature inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-white text-gray-400 hover:bg-red-50 hover:text-red-600 text-lg">×</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-feature" class="mt-3 btn-secondary">
                    {{ __('+ Add feature') }}
                </button>
            </div>
        </div>

        {{-- CTA --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5">
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Call to action') }}</h3>
            <x-form.input name="cta_text" label="{{ __('Button text') }}"
                          value="{{ old('cta_text', $plan?->cta_text) }}"
                          hint="{{ __('e.g. Start free trial') }}" />
            <x-form.input name="cta_url" label="{{ __('Button URL') }}"
                          value="{{ old('cta_url', $plan?->cta_url) }}"
                          hint="{{ __('e.g. /register') }}" />
        </div>
    </div>

    {{-- Right sidebar --}}
    <div class="col-span-1 space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5">
            <x-form.select name="billing_cycle" label="{{ __('Billing cycle') }}"
                           :options="[
                               'per_contract' => __('Per contract'),
                               'monthly'      => __('Monthly'),
                               'yearly'       => __('Yearly'),
                               'lifetime'     => __('Lifetime'),
                           ]"
                           :selected="old('billing_cycle', $plan?->billing_cycle)" required />

            <x-form.input name="price" type="number" step="0.01" label="{{ __('Price') }}"
                         value="{{ old('price', $plan?->price ?? 0) }}"
                         hint="{{ __('0 = custom / contact sales') }}" required />

            <x-form.input name="credits" type="number" label="{{ __('Credits per period') }}"
                         value="{{ old('credits', $plan?->credits ?? 0) }}"
                         hint="{{ __('0 = unlimited') }}" required />

            <x-form.input name="trial_days" type="number" label="{{ __('Trial days') }}"
                         value="{{ old('trial_days', $plan?->trial_days ?? 0) }}"
                         hint="{{ __('0 = no trial') }}" required />

            <x-form.input name="sort" type="number" label="{{ __('Sort order') }}"
                         value="{{ old('sort', $plan?->sort ?? 0) }}" required />

            <x-form.checkbox name="is_featured" label="{{ __('Featured (highlighted card)') }}"
                            :checked="old('is_featured', $plan?->is_featured ?? false)" />

            <x-form.checkbox name="is_active" label="{{ __('Active') }}"
                            :checked="old('is_active', $plan?->is_active ?? true)" />

            <div class="pt-3 mt-3 border-t border-gray-200 space-y-3">
                <x-form.checkbox name="ai_contract_generation" label="{{ __('Allow free-form AI contract drafting') }}"
                                :checked="old('ai_contract_generation', $plan?->ai_contract_generation ?? false)" />

                <x-form.input name="ai_drafts" type="number" label="{{ __('AI drafts per period') }}"
                             value="{{ old('ai_drafts', $plan?->ai_drafts ?? 0) }}"
                             hint="{{ __('Number of AI contract drafts included in this plan per billing cycle (or one-time for per-contract / lifetime). Set 0 if AI is disabled.') }}" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    onAdminReady(function () {
        'use strict';

        var template = '<div class="feature-row flex gap-2">'
            + '<input type="text" name="features[]" placeholder="{{ __('Feature text…') }}" class="flex-1 h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">'
            + '<button type="button" class="remove-feature inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-white text-gray-400 hover:bg-red-50 hover:text-red-600 text-lg">×</button>'
            + '</div>';

        $('#add-feature').on('click', function () {
            $('#features-list').append(template);
        });

        $(document).on('click', '.remove-feature', function () {
            $(this).closest('.feature-row').remove();
        });
    });
</script>
@endpush
