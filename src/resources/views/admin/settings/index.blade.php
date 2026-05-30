@extends('admin.layouts.app')

@section('content')
<x-page-header title="{{ __('Settings') }}" subtitle="{{ __('General site configuration.') }}" />

<div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
    {{-- Tab Navigation --}}
    <div class="flex border-b border-gray-200 bg-gray-50 px-6 overflow-x-auto">
        <button type="button" class="tab-button active flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-900 border-b-2 border-brand-600 whitespace-nowrap" data-tab="general">
            <x-heroicon-o-cog-6-tooth class="h-4 w-4" />
            {{ __('General') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="ai">
            <x-heroicon-o-sparkles class="h-4 w-4" />
            {{ __('AI Setup') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="controls">
            <x-heroicon-o-shield-check class="h-4 w-4" />
            {{ __('Global Controls') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="brand">
            <x-heroicon-o-swatch class="h-4 w-4" />
            {{ __('Brand & Appearance') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="seo">
            <x-heroicon-o-magnifying-glass class="h-4 w-4" />
            {{ __('SEO & Footer') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="integrations">
            <x-heroicon-o-puzzle-piece class="h-4 w-4" />
            {{ __('Integrations') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="email">
            <x-heroicon-o-envelope class="h-4 w-4" />
            {{ __('Email') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="sms">
            <x-heroicon-o-chat-bubble-left class="h-4 w-4" />
            {{ __('SMS') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="storage">
            <x-heroicon-o-cloud-arrow-up class="h-4 w-4" />
            {{ __('Storage') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="social">
            <x-heroicon-o-user-circle class="h-4 w-4" />
            {{ __('Social Login') }}
        </button>
        <button type="button" class="tab-button flex items-center gap-2 px-4 py-3 text-sm font-semibold text-gray-600 border-b-2 border-transparent hover:text-gray-900 whitespace-nowrap" data-tab="queue">
            <x-heroicon-o-arrow-path class="h-4 w-4" />
            {{ __('Queue') }}
        </button>
    </div>

    {{-- Tab Content --}}
    <div class="p-6">
        {{-- General Tab --}}
        <div id="general" class="tab-content block">
            <form method="POST" action="{{ route('admin.settings.update', 'general') }}" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-form.input name="site_name" label="{{ __('Site Name') }}" :value="old('site_name', setting('site.name', config('app.name')))" required />
                    <x-form.input name="site_tagline" label="{{ __('Tagline') }}" :value="old('site_tagline', setting('site.tagline'))" placeholder="{{ __('Your short slogan') }}" />
                    <x-form.input name="site_email" label="{{ __('Contact Email') }}" type="email" :value="old('site_email', setting('site.email'))" />
                    <x-form.input name="site_phone" label="{{ __('Contact Phone') }}" :value="old('site_phone', setting('site.phone'))" />
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Timezone') }}</label>
                        <select name="site_timezone" class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            @php $current = old('site_timezone', setting('site.timezone', config('app.timezone', 'UTC'))); @endphp
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" @selected($tz === $current)>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-form.input name="site_currency" label="{{ __('Default Currency (ISO 4217)') }}" :value="old('site_currency', setting('site.currency', 'USD'))" placeholder="USD" maxlength="3" />
                    <x-form.input name="site_currency_symbol" label="{{ __('Currency Symbol') }}" :value="old('site_currency_symbol', setting('site.currency_symbol', '$'))" placeholder="$" maxlength="10" />
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        {{ __('Save General') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Global Controls Tab --}}
        <div id="controls" class="tab-content hidden">
            <form method="POST" action="{{ route('admin.settings.update', 'controls') }}" class="space-y-5">
                @csrf @method('PUT')

                @php
                    $controls = [
                        [
                            'name' => 'registration_enabled',
                            'key' => 'controls.registration_enabled',
                            'default' => '1',
                            'icon' => 'heroicon-o-user-plus',
                            'tone' => 'bg-emerald-50 text-emerald-600',
                            'title' => __('User Registration'),
                            'desc' => __('Allow new users to sign up. When disabled, the register page is blocked.'),
                        ],
                        [
                            'name' => 'email_verification',
                            'key' => 'controls.email_verification',
                            'default' => '1',
                            'icon' => 'heroicon-o-envelope-open',
                            'tone' => 'bg-blue-50 text-blue-600',
                            'title' => __('Email Verification'),
                            'desc' => __('Require new users to verify their email address before accessing the dashboard.'),
                        ],
                        [
                            'name' => 'sms_verification',
                            'key' => 'controls.sms_verification',
                            'default' => '0',
                            'icon' => 'heroicon-o-device-phone-mobile',
                            'tone' => 'bg-purple-50 text-purple-600',
                            'title' => __('SMS Verification'),
                            'desc' => __('Require users to verify their phone number via SMS code. Configure SMS driver under the SMS tab.'),
                        ],
                        [
                            'name' => 'kyc_enabled',
                            'key' => 'controls.kyc_enabled',
                            'default' => '1',
                            'icon' => 'heroicon-o-identification',
                            'tone' => 'bg-amber-50 text-amber-600',
                            'title' => __('KYC Verification'),
                            'desc' => __('Require identity verification before accessing platform features. When disabled, users have access immediately.'),
                        ],
                        [
                            'name' => 'policy_agreement',
                            'key' => 'controls.policy_agreement',
                            'default' => '0',
                            'icon' => 'heroicon-o-document-check',
                            'tone' => 'bg-indigo-50 text-indigo-600',
                            'title' => __('Policy Agreement'),
                            'desc' => __('Show a Terms & Privacy checkbox on the registration form. Pages live under CMS → Pages (/terms, /privacy).'),
                        ],
                        [
                            'name' => 'maintenance_mode',
                            'key' => 'site.maintenance_mode',
                            'default' => '0',
                            'icon' => 'heroicon-o-wrench-screwdriver',
                            'tone' => 'bg-rose-50 text-rose-600',
                            'title' => __('Maintenance Mode'),
                            'desc' => __('Show a 503 maintenance page to all public visitors. Admin panel stays accessible.'),
                        ],
                        [
                            'name' => 'force_https',
                            'key' => 'controls.force_https',
                            'default' => '0',
                            'icon' => 'heroicon-o-lock-closed',
                            'tone' => 'bg-slate-100 text-slate-700',
                            'title' => __('Force HTTPS'),
                            'desc' => __('Redirect all HTTP requests to HTTPS and force generated URLs to use the secure scheme.'),
                        ],
                        [
                            'name' => 'cookie_consent',
                            'key' => 'controls.cookie_consent',
                            'default' => '0',
                            'icon' => 'heroicon-o-shield-check',
                            'tone' => 'bg-teal-50 text-teal-600',
                            'title' => __('Cookie Consent Banner'),
                            'desc' => __('Show a GDPR cookie consent floater to visitors. Links to your /privacy page. Dismissed state is saved per-browser.'),
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    @foreach($controls as $c)
                        <div class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 bg-white px-5 py-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $c['tone'] }}">
                                    <x-dynamic-component :component="$c['icon']" class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $c['title'] }}</h3>
                                    <p class="mt-1 text-sm font-normal text-gray-500">{{ $c['desc'] }}</p>
                                </div>
                            </div>
                            <x-form.toggle :name="$c['name']" :checked="setting($c['key'], $c['default']) === '1'" />
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    <x-heroicon-o-check class="h-4 w-4" />
                    {{ __('Save Controls') }}
                </button>
            </form>
        </div>

        {{-- Brand & Appearance Tab --}}
        <div id="brand" class="tab-content hidden">
            <form method="POST" action="{{ route('admin.settings.update', 'brand') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                {{-- Color + Font top row --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    {{-- Primary color picker --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Primary / Accent Color') }}</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" value="{{ old('primary_color', setting('site.primary_color', '#D4FF3A')) }}"
                                class="h-9 w-14 cursor-pointer rounded-lg border border-gray-300 bg-white p-1">
                            <input type="text" id="primary_color_hex" value="{{ old('primary_color', setting('site.primary_color', '#D4FF3A')) }}"
                                class="h-9 w-32 rounded-lg border border-gray-300 px-3 text-sm font-mono focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                placeholder="#D4FF3A" maxlength="7"
                                oninput="document.querySelector('[name=primary_color]').value=this.value">
                        </div>
                        <p class="mt-1 text-xs text-gray-400">{{ __('Buttons, links, highlights across the frontend.') }}</p>
                    </div>
                    <script>
                        document.querySelector('[name=primary_color]').addEventListener('input', function() { document.getElementById('primary_color_hex').value = this.value; });
                    </script>

                    {{-- Font picker --}}
                    <div class="flex flex-col">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Site Font') }}</label>
                        @php $currentFont = old('site_font', setting('site.font', 'system')); @endphp
                        <select id="site_font_select" name="site_font" class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            <option value="system" @selected($currentFont === 'system')>{{ __('Default (Manrope)') }}</option>
                            @foreach(['Inter','Roboto','Lato','Open Sans','Poppins','Nunito','Source Sans 3','Work Sans','DM Sans','Manrope','Montserrat','Plus Jakarta Sans'] as $font)
                                <option value="{{ $font }}" @selected($currentFont === $font)>{{ $font }}</option>
                            @endforeach
                        </select>
                        <div id="font_preview" class="mt-2 flex-1 rounded-lg border border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-800/50 px-4 py-3 flex flex-col justify-center">
                            <p id="font_preview_text" class="text-2xl text-gray-800 dark:text-slate-100 leading-snug">The quick brown fox jumps over the lazy dog</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-slate-500" id="font_preview_label">Manrope</p>
                        </div>
                    </div>

                    {{-- Logo (light bg / default) --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Logo') }} <span class="text-xs text-gray-400 font-normal">{{ __('(for dark backgrounds)') }}</span></label>
                        @php $logo = setting('site.logo'); @endphp
                        <label class="upload-drop relative flex flex-col w-full h-44 rounded-lg border-2 border-dashed border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer overflow-hidden">
                            <div class="upload-preview flex-1 flex items-center justify-center min-h-0 p-3 bg-ink-900 rounded-t-md">
                                @if($logo)
                                    <img src="{{ upload_url($logo) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                                @else
                                    <x-heroicon-o-photo class="h-8 w-8 text-gray-600" />
                                @endif
                            </div>
                            <div class="upload-info shrink-0 flex flex-col items-center gap-0.5 bg-white/70 dark:bg-slate-900/60 border-t border-gray-200 dark:border-slate-700 px-3 py-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-100 truncate max-w-full">{{ __('Click or drop files') }}</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400 text-center">{{ __('PNG, JPG, WebP — max 500px wide, max 2 MB') }}</span>
                            </div>
                            <input type="file" name="site_logo" accept="image/*" class="hidden">
                        </label>
                    </div>

                    {{-- Logo dark (for light backgrounds) --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Logo (Light Mode)') }} <span class="text-xs text-gray-400 font-normal">{{ __('(for light backgrounds)') }}</span></label>
                        @php $darkLogo = setting('site.dark_logo'); @endphp
                        <label class="upload-drop relative flex flex-col w-full h-44 rounded-lg border-2 border-dashed border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer overflow-hidden">
                            <div class="upload-preview upload-preview-light flex-1 flex items-center justify-center min-h-0 p-3 rounded-t-md">
                                @if($darkLogo)
                                    <img src="{{ upload_url($darkLogo) }}" alt="Logo Light Mode" class="max-h-full max-w-full object-contain">
                                @else
                                    <x-heroicon-o-photo class="h-8 w-8 text-gray-300" />
                                @endif
                            </div>
                            <div class="upload-info shrink-0 flex flex-col items-center gap-0.5 bg-white/70 dark:bg-slate-900/60 border-t border-gray-200 dark:border-slate-700 px-3 py-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-100 truncate max-w-full">{{ __('Click or drop files') }}</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400 text-center">{{ __('PNG, JPG, WebP — max 500px wide, max 2 MB') }}</span>
                            </div>
                            <input type="file" name="site_dark_logo" accept="image/*" class="hidden">
                        </label>
                    </div>

                    {{-- Favicon --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Favicon') }}</label>
                        @php $favicon = setting('site.favicon'); @endphp
                        <label class="upload-drop relative flex flex-col w-full h-44 rounded-lg border-2 border-dashed border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer overflow-hidden">
                            <div class="upload-preview flex-1 flex items-center justify-center min-h-0 p-3">
                                @if($favicon)
                                    <img src="{{ upload_url($favicon) }}" alt="Favicon" class="max-h-full max-w-full object-contain">
                                @else
                                    <x-heroicon-o-photo class="h-8 w-8 text-gray-300 dark:text-slate-600" />
                                @endif
                            </div>
                            <div class="upload-info shrink-0 flex flex-col items-center gap-0.5 bg-white/70 dark:bg-slate-900/60 border-t border-gray-200 dark:border-slate-700 px-3 py-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-100 truncate max-w-full">{{ __('Click or drop files') }}</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400 text-center">{{ __('PNG, ICO — max 512 KB') }}</span>
                            </div>
                            <input type="file" name="site_favicon" accept="image/*" class="hidden">
                        </label>
                    </div>

                    {{-- OG Image --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Default OG / Social Share Image') }}</label>
                        @php $og = setting('site.og_image'); @endphp
                        <label class="upload-drop relative flex flex-col w-full h-44 rounded-lg border-2 border-dashed border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer overflow-hidden">
                            <div class="upload-preview flex-1 flex items-center justify-center min-h-0 p-3">
                                @if($og)
                                    <img src="{{ upload_url($og) }}" alt="OG Image" class="max-h-full max-w-full object-contain rounded">
                                @else
                                    <x-heroicon-o-photo class="h-8 w-8 text-gray-300 dark:text-slate-600" />
                                @endif
                            </div>
                            <div class="upload-info shrink-0 flex flex-col items-center gap-0.5 bg-white/70 dark:bg-slate-900/60 border-t border-gray-200 dark:border-slate-700 px-3 py-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-100 truncate max-w-full">{{ __('Click or drop files') }}</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400 text-center">{{ __('1200×630 recommended — max 2 MB') }}</span>
                            </div>
                            <input type="file" name="site_og_image" accept="image/*" class="hidden">
                        </label>
                    </div>
                </div>

                {{-- Custom CSS --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Custom CSS') }}</label>
                    <textarea name="custom_css" rows="8" placeholder="/* Your custom CSS here */" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('custom_css', setting('site.custom_css')) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Injected into every frontend page. Changes take effect immediately on save.') }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        {{ __('Save Brand') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- SEO & Footer Tab --}}
        <div id="seo" class="tab-content hidden">
            <form method="POST" action="{{ route('admin.settings.update', 'seo') }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Default Meta Description') }}</label>
                    <textarea name="meta_description" rows="2"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                        maxlength="300">{{ old('meta_description', setting('site.meta_description')) }}</textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Footer Text') }}</label>
                    <textarea name="footer_text" rows="2"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                        maxlength="500">{{ old('footer_text', setting('site.footer_text')) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Shown in the site footer. Plain text only.') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        {{ __('Save SEO & Footer') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Email Tab --}}
        <div id="email" class="tab-content hidden">
            @php $mailDriver = setting('mail.driver', 'log'); @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left: Config --}}
                <form method="POST" action="{{ route('admin.settings.update', 'email') }}" class="space-y-5">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form.input name="mail_from_address" label="{{ __('From Address') }}" type="email"
                            :value="setting('mail.from_address', config('mail.from.address'))" required />
                        <x-form.input name="mail_from_name" label="{{ __('From Name') }}"
                            :value="setting('mail.from_name', config('mail.from.name', config('app.name')))" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Driver') }}</label>
                        <select name="mail_driver" id="mail_driver"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            @foreach(['log' => 'Log (dev)', 'smtp' => 'SMTP', 'sendmail' => 'PHP Mail (Sendmail)', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'resend' => 'Resend'] as $val => $label)
                            <option value="{{ $val }}" {{ $mailDriver === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="driver-smtp" class="driver-fields {{ $mailDriver !== 'smtp' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input name="mail_smtp_host" label="{{ __('SMTP Host') }}" :value="setting('mail.smtp.host')" />
                        <x-form.input name="mail_smtp_port" label="{{ __('SMTP Port') }}" :value="setting('mail.smtp.port', '587')" />
                        <x-form.input name="mail_smtp_username" label="{{ __('Username') }}" :value="setting('mail.smtp.username')" />
                        <x-form.input name="mail_smtp_password" label="{{ __('Password') }}" type="password" :value="setting('mail.smtp.password')" />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Encryption') }}</label>
                            <select name="mail_smtp_encryption" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                                @foreach(['tls' => 'TLS', 'ssl' => 'SSL', '' => 'None'] as $val => $label)
                                <option value="{{ $val }}" {{ setting('mail.smtp.encryption') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="driver-mailgun" class="driver-fields {{ $mailDriver !== 'mailgun' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input name="mail_mailgun_domain" label="{{ __('Domain') }}" :value="setting('mail.mailgun.domain')" />
                        <x-form.input name="mail_mailgun_secret" label="{{ __('API Key') }}" :value="setting('mail.mailgun.secret')" />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Endpoint') }}</label>
                            <select name="mail_mailgun_endpoint" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                                <option value="api.mailgun.net" {{ setting('mail.mailgun.endpoint') === 'api.mailgun.net' ? 'selected' : '' }}>{{ __('US (api.mailgun.net)') }}</option>
                                <option value="api.eu.mailgun.net" {{ setting('mail.mailgun.endpoint') === 'api.eu.mailgun.net' ? 'selected' : '' }}>{{ __('EU (api.eu.mailgun.net)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div id="driver-ses" class="driver-fields {{ $mailDriver !== 'ses' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input name="mail_ses_key" label="{{ __('Access Key') }}" :value="setting('mail.ses.key')" />
                        <x-form.input name="mail_ses_secret" label="{{ __('Secret Key') }}" type="password" :value="setting('mail.ses.secret')" />
                        <x-form.input name="mail_ses_region" label="{{ __('Region') }}" :value="setting('mail.ses.region', 'us-east-1')" />
                    </div>

                    <div id="driver-postmark" class="driver-fields {{ $mailDriver !== 'postmark' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input name="mail_postmark_token" label="{{ __('Server Token') }}" :value="setting('mail.postmark.token')" />
                    </div>

                    <div id="driver-resend" class="driver-fields {{ $mailDriver !== 'resend' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input name="mail_resend_key" label="{{ __('API Key') }}" :value="setting('mail.resend.key')" />
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                            <x-heroicon-o-check class="h-4 w-4" />
                            {{ __('Save Email') }}
                        </button>
                    </div>
                </form>

                {{-- Right: Test --}}
                <div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                <x-heroicon-o-paper-airplane class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ __('Send Test Email') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('Verify your mail driver is working correctly.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-lg border border-gray-200 bg-white p-3 text-xs text-gray-500 space-y-1 font-mono">
                            <div><span class="text-gray-400">Driver:</span> {{ strtoupper($mailDriver) }}</div>
                            <div><span class="text-gray-400">From:</span> {{ setting('mail.from_address') ?: '—' }}</div>
                            @if($mailDriver === 'smtp')
                            <div><span class="text-gray-400">Host:</span> {{ setting('mail.smtp.host') ?: '—' }}:{{ setting('mail.smtp.port', '587') }}</div>
                            <div><span class="text-gray-400">Encryption:</span> {{ strtoupper(setting('mail.smtp.encryption') ?: 'none') }}</div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.system.test-mail') }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-600">{{ __('Send to') }}</label>
                                <input type="email" name="email"
                                    value="{{ old('email', auth('admin')->user()?->email) }}"
                                    placeholder="you@example.com" required
                                    class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                                <x-heroicon-o-paper-airplane class="h-4 w-4" />
                                {{ __('Send Test Email') }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        {{-- SMS Tab --}}
        <div id="sms" class="tab-content hidden">
            @php
            $smsDriver = setting('sms.driver', 'log');
            @endphp
            <form method="POST" action="{{ route('admin.settings.update', 'sms') }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Driver') }}</label>
                    <select name="sms_driver" id="sms_driver"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @foreach(['log' => 'Log (dev)', 'twilio' => 'Twilio', 'vonage' => 'Vonage', 'messagebird' => 'MessageBird', 'textmagic' => 'Textmagic'] as $val => $label)
                        <option value="{{ $val }}" {{ $smsDriver === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="sms-twilio" class="sms-fields {{ $smsDriver !== 'twilio' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input name="sms_twilio_sid" label="{{ __('Account SID') }}" :value="setting('sms.twilio.sid')" />
                    <x-form.input name="sms_twilio_token" label="{{ __('Auth Token') }}" type="password" :value="setting('sms.twilio.token')" />
                    <x-form.input name="sms_twilio_from" label="{{ __('From Number') }}" :value="setting('sms.twilio.from')" />
                </div>

                <div id="sms-vonage" class="sms-fields {{ $smsDriver !== 'vonage' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input name="sms_vonage_api_key" label="{{ __('API Key') }}" :value="setting('sms.vonage.api_key')" />
                    <x-form.input name="sms_vonage_api_secret" label="{{ __('API Secret') }}" type="password" :value="setting('sms.vonage.api_secret')" />
                    <x-form.input name="sms_vonage_from" label="{{ __('From Name') }}" :value="setting('sms.vonage.from', 'DocsAI')" />
                </div>

                <div id="sms-messagebird" class="sms-fields {{ $smsDriver !== 'messagebird' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input name="sms_messagebird_api_key" label="{{ __('API Key') }}" :value="setting('sms.messagebird.api_key')" />
                    <x-form.input name="sms_messagebird_from" label="{{ __('From') }}" :value="setting('sms.messagebird.from', 'DocsAI')" />
                </div>

                <div id="sms-textmagic" class="sms-fields {{ $smsDriver !== 'textmagic' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input name="sms_textmagic_username" label="{{ __('Username') }}" :value="setting('sms.textmagic.username')" />
                    <x-form.input name="sms_textmagic_api_key" label="{{ __('API Key') }}" :value="setting('sms.textmagic.api_key')" />
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        {{ __('Save SMS') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Storage Tab --}}
        <div id="storage" class="tab-content hidden">
            @php
            $storageDriver = setting('storage.driver', 'local');
            $storageTiles = [
                'local'      => ['label' => 'Local Disk',          'desc' => 'Server filesystem',     'color' => 'bg-slate-600'],
                's3'         => ['label' => 'Amazon S3',           'desc' => 'AWS object storage',    'color' => 'bg-orange-500'],
                'r2'         => ['label' => 'Cloudflare R2',       'desc' => 'Zero egress fees',      'color' => 'bg-amber-500'],
                'wasabi'     => ['label' => 'Wasabi',              'desc' => 'Hot cloud storage',     'color' => 'bg-green-600'],
                'do_spaces'  => ['label' => 'DigitalOcean Spaces', 'desc' => 'Object storage',        'color' => 'bg-blue-500'],
            ];
            @endphp
            <form method="POST" action="{{ route('admin.settings.update', 'storage') }}" class="space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="storage_driver" id="storage_driver_input" value="{{ $storageDriver }}">

                {{-- Driver tiles --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($storageTiles as $key => $tile)
                    @php $isActive = $storageDriver === $key; @endphp
                    <button type="button" data-storage-driver="{{ $key }}" data-active="{{ $isActive ? '1' : '0' }}"
                        class="storage-tile relative flex flex-col items-start gap-3 rounded-xl border-2 p-4 text-left transition-all cursor-pointer border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                        <div class="h-8 w-8 rounded-lg {{ $tile['color'] }} opacity-90"></div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $tile['label'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">{{ $tile['desc'] }}</div>
                        </div>
                    </button>
                    @endforeach
                </div>

                {{-- Driver fields --}}
                <div class="storage-fields-local {{ $storageDriver !== 'local' ? 'hidden' : '' }}">
                    <p class="text-sm text-gray-500">{{ __('Files are stored at') }} <code class="rounded bg-gray-100 dark:bg-slate-700 px-1">/assets/uploads/</code> {{ __('in the project root, served directly by the web server.') }}</p>
                </div>

                <div class="storage-fields-s3 {{ $storageDriver !== 's3' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['key' => 'Access Key', 'secret' => 'Secret Key', 'region' => 'Region', 'bucket' => 'Bucket', 'endpoint' => 'Endpoint (optional)'] as $field => $label)
                    <x-form.input name="storage_s3_{{ $field }}" label="{{ $label }}"
                        type="{{ $field === 'secret' ? 'password' : 'text' }}"
                        :value="setting('storage.s3.' . $field)" />
                    @endforeach
                </div>

                <div class="storage-fields-r2 {{ $storageDriver !== 'r2' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['key' => 'Access Key ID', 'secret' => 'Secret Access Key', 'bucket' => 'Bucket Name', 'endpoint' => 'R2 Endpoint URL'] as $field => $label)
                    <x-form.input name="storage_r2_{{ $field }}" label="{{ $label }}"
                        type="{{ $field === 'secret' ? 'password' : 'text' }}"
                        :value="setting('storage.r2.' . $field)" />
                    @endforeach
                </div>

                <div class="storage-fields-wasabi {{ $storageDriver !== 'wasabi' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['key' => 'Access Key', 'secret' => 'Secret Key', 'region' => 'Region (e.g. us-east-1)', 'bucket' => 'Bucket'] as $field => $label)
                    <x-form.input name="storage_wasabi_{{ $field }}" label="{{ $label }}"
                        type="{{ $field === 'secret' ? 'password' : 'text' }}"
                        :value="setting('storage.wasabi.' . $field)" />
                    @endforeach
                </div>

                <div class="storage-fields-do_spaces {{ $storageDriver !== 'do_spaces' ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['key' => 'Spaces Key', 'secret' => 'Spaces Secret', 'region' => 'Region (e.g. nyc3)', 'bucket' => 'Space Name', 'endpoint' => 'Endpoint URL'] as $field => $label)
                    <x-form.input name="storage_do_spaces_{{ $field }}" label="{{ $label }}"
                        type="{{ $field === 'secret' ? 'password' : 'text' }}"
                        :value="setting('storage.do_spaces.' . $field)" />
                    @endforeach
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        {{ __('Save Storage') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Social Login Tab --}}
        <div id="social" class="tab-content hidden">
            <form method="POST" action="{{ route('admin.settings.update', 'social') }}" class="space-y-4">
                @csrf @method('PUT')

                @php
                $socialProviders = [
                    'google' => [
                        'label' => 'Google',
                        'icon_bg' => 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600',
                        'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>',
                    ],
                    'facebook' => [
                        'label' => 'Facebook',
                        'icon_bg' => 'bg-[#1877F2]',
                        'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="white"/></svg>',
                    ],
                    'twitter' => [
                        'label' => 'X (Twitter)',
                        'icon_bg' => 'bg-black',
                        'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.258 5.63 5.906-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z" fill="white"/></svg>',
                    ],
                    'linkedin' => [
                        'label' => 'LinkedIn',
                        'icon_bg' => 'bg-[#0A66C2]',
                        'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="white"/></svg>',
                    ],
                ];
                @endphp

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @foreach($socialProviders as $key => $provider)
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 overflow-hidden">
                        {{-- Card header --}}
                        <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $provider['icon_bg'] }}">
                                    <?php echo $provider['icon']; ?>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $provider['label'] }}</span>
                            </div>
                            <x-form.toggle name="social_{{ $key }}_enabled" :checked="setting('social.'.$key.'.enabled') === '1'" />
                        </div>
                        {{-- Credentials --}}
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-slate-400">{{ __('Client ID') }}</label>
                                <input type="text" name="social_{{ $key }}_client_id"
                                    value="{{ old('social_'.$key.'_client_id', setting('social.'.$key.'.client_id')) }}"
                                    placeholder="Paste your {{ $provider['label'] }} Client ID"
                                    class="w-full h-9 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-slate-400">{{ __('Client Secret') }}</label>
                                <input type="password" name="social_{{ $key }}_client_secret"
                                    value="{{ old('social_'.$key.'_client_secret', setting('social.'.$key.'.client_secret')) }}"
                                    placeholder="••••••••••••••••"
                                    class="w-full h-9 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                            </div>
                            <p class="text-xs text-gray-400 dark:text-slate-500">
                                {{ __('Callback:') }}
                                <code class="font-mono text-gray-500 dark:text-slate-400">{{ config('app.url') }}/auth/{{ $key }}/callback</code>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    <x-heroicon-o-check class="h-4 w-4" />
                    {{ __('Save Social Login') }}
                </button>
            </form>
        </div>

        {{-- Queue Tab --}}
        <div id="queue" class="tab-content hidden">
            @php
            $queueEnabled = setting('queue.enabled') === '1';
            @endphp
            <div class="space-y-5">
                <form method="POST" action="{{ route('admin.settings.update', 'queue') }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('Queue Heavy Operations') }}</h3>
                            <p class="text-sm font-normal text-gray-500 mt-1">
                                {{ __('When enabled, broadcast newsletters and bulk notifications are dispatched to the database queue.') }}
                            </p>
                            @if(!$queueEnabled)
                            <p class="text-sm font-normal text-gray-400 mt-2">{{ __('Currently') }} <strong>{{ __('off') }}</strong> — {{ __('operations run synchronously.') }}</p>
                            @else
                            <p class="text-sm font-normal text-green-700 mt-2">{{ __('Currently') }} <strong>{{ __('on') }}</strong> — {{ __('jobs are queued in the database.') }}</p>
                            @endif
                        </div>
                        <x-form.toggle name="queue_enabled" :checked="$queueEnabled" />
                    </div>

                    @if($queueEnabled)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                        <p class="text-xs font-medium text-gray-600 mb-1">{{ __('Required cron entry on your server:') }}</p>
                        <code class="text-xs font-mono text-gray-800 break-all">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
                    </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                            <x-heroicon-o-check class="h-4 w-4" />
                            {{ __('Save Queue') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Integrations Tab --}}
        <div id="integrations" class="tab-content hidden">
            <form method="POST" action="{{ route('admin.settings.update', 'integrations') }}" class="space-y-5">
                @csrf @method('PUT')

                {{-- Cloudflare Turnstile --}}
                <section class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 bg-gray-50/50 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-50">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.5 12.5a4.5 4.5 0 1 0-8.875-1.094A3.5 3.5 0 0 0 8 18h8.5a3 3 0 0 0 0-6 .5.5 0 0 1 0-.5z" fill="#F38020"/>
                                    <path d="M18.5 13a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" fill="#FAAE40"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ __('Cloudflare Turnstile (Captcha)') }}</h3>
                                <p class="mt-1 text-sm font-normal text-gray-500">{{ __('Protects login, registration, and contact forms from bots.') }} <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" class="text-brand-600 hover:text-brand-700">{{ __('Get keys →') }}</a></p>
                            </div>
                        </div>
                        <x-form.toggle name="turnstile_enabled" :checked="setting('integrations.turnstile.enabled') === '1'" />
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-form.input name="turnstile_site_key" label="{{ __('Site Key') }}" :value="old('turnstile_site_key', setting('integrations.turnstile.site_key'))" placeholder="0x4AAAAAAA..." />
                            <x-form.input name="turnstile_secret_key" label="{{ __('Secret Key') }}" type="password" :value="old('turnstile_secret_key', setting('integrations.turnstile.secret_key'))" placeholder="0x4AAAAAAA..." />
                        </div>
                    </div>
                </section>

                {{-- Google Analytics 4 --}}
                <section class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 bg-gray-50/50 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="16" y="3" width="5" height="18" rx="2.5" fill="#F9AB00"/>
                                    <rect x="9.5" y="9" width="5" height="12" rx="2.5" fill="#E37400"/>
                                    <circle cx="5.5" cy="18.5" r="2.5" fill="#E37400"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ __('Google Analytics 4') }}</h3>
                                <p class="mt-1 text-sm font-normal text-gray-500">{{ __('Adds the GA4 tracking snippet to all public pages.') }} <a href="https://analytics.google.com/" target="_blank" class="text-brand-600 hover:text-brand-700">{{ __('Get Measurement ID →') }}</a></p>
                            </div>
                        </div>
                        <x-form.toggle name="ga_enabled" :checked="setting('integrations.ga.enabled') === '1'" />
                    </div>
                    <div class="p-5">
                        <x-form.input name="ga_measurement_id" label="{{ __('Measurement ID') }}" :value="old('ga_measurement_id', setting('integrations.ga.measurement_id'))" placeholder="G-XXXXXXXXXX" />
                    </div>
                </section>

                {{-- Tawk.to Live Chat --}}
                <section class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 bg-gray-50/50 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 6a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-4l-4 4v-4H7a3 3 0 0 1-3-3V6z" fill="#0FBF61"/>
                                    <circle cx="9" cy="10" r="1.2" fill="white"/>
                                    <circle cx="12" cy="10" r="1.2" fill="white"/>
                                    <circle cx="15" cy="10" r="1.2" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ __('Tawk.to Live Chat') }}</h3>
                                <p class="mt-1 text-sm font-normal text-gray-500">{{ __('Embeds the Tawk.to chat widget on all public pages.') }} <a href="https://dashboard.tawk.to/" target="_blank" class="text-brand-600 hover:text-brand-700">{{ __('Get widget IDs →') }}</a></p>
                            </div>
                        </div>
                        <x-form.toggle name="tawk_enabled" :checked="setting('integrations.tawk.enabled') === '1'" />
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-form.input name="tawk_property_id" label="{{ __('Property ID') }}" :value="old('tawk_property_id', setting('integrations.tawk.property_id'))" placeholder="65...abc" />
                            <x-form.input name="tawk_widget_id" label="{{ __('Widget ID') }}" :value="old('tawk_widget_id', setting('integrations.tawk.widget_id'))" placeholder="default" />
                        </div>
                        <p class="text-sm font-normal text-gray-500">{{ __('Find these in Tawk.to → Administration → Chat Widget. The embed URL is') }} <code class="rounded bg-gray-100 px-1 text-xs">embed.tawk.to/<strong>PROPERTY_ID</strong>/<strong>WIDGET_ID</strong></code></p>
                    </div>
                </section>

                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    <x-heroicon-o-check class="h-4 w-4" />
                    {{ __('Save Integrations') }}
                </button>
            </form>
        </div>

        {{-- AI Setup Tab --}}
        <div id="ai" class="tab-content hidden">
            <form method="POST" action="{{ route('admin.settings.update', 'ai') }}" class="space-y-5 max-w-xl">
                @csrf @method('PUT')
                <input type="hidden" name="_tab" value="ai">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('OpenAI API') }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('Used for AI-powered contract generation.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('API Key') }}</label>
                    <div class="flex gap-2">
                        <input id="ai_openai_key_input" type="password" name="ai_openai_key"
                               placeholder="{{ setting('ai.openai_key') ? __('Saved — enter new key to replace') : 'sk-...' }}"
                               class="h-9 flex-1 rounded-lg border border-gray-300 px-3 text-sm font-mono shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                        <button type="button" id="ai_show_key_btn"
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">{{ __('Show') }}</button>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Leave blank to keep the existing key.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Model') }}</label>
                    <select name="ai_openai_model"
                            class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @foreach(['gpt-5.5', 'gpt-5.5-pro', 'gpt-5.4', 'gpt-5.4-pro', 'gpt-5.4-mini', 'gpt-5.4-nano', 'gpt-5-mini', 'gpt-5-nano', 'gpt-5', 'gpt-4.1', 'gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'gpt-3.5-turbo'] as $model)
                            <option value="{{ $model }}" @selected(setting('ai.openai_model', 'gpt-4o') === $model)>{{ $model }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Max Tokens per Section') }}</label>
                        <input type="number" name="ai_openai_max_tokens"
                               value="{{ old('ai_openai_max_tokens', setting('ai.openai_max_tokens', 800)) }}"
                               min="100" max="4000"
                               class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                        <p class="mt-1 text-xs text-gray-400">{{ __('100 – 4000') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Temperature') }}</label>
                        <input type="number" name="ai_openai_temperature" step="0.1"
                               value="{{ old('ai_openai_temperature', setting('ai.openai_temperature', 0.3)) }}"
                               min="0" max="2"
                               class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
                        <p class="mt-1 text-xs text-gray-400">{{ __('0 = precise, 1 = creative, 2 = wild') }}</p>
                    </div>
                </div>

                @if(setting('ai.openai_key'))
                <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    ✓ {{ __('API key is configured.') }}
                    {{ __('Model:') }} <strong>{{ setting('ai.openai_model', 'gpt-4o') }}</strong>
                </div>
                @else
                <div class="rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-700">
                    {{ __('No API key set — contract AI generation will fail until one is added.') }}
                </div>
                @endif

                <div>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        {{ __('Save AI Settings') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('head')
<style>
.tab-content { animation: none; }
.tab-fade { animation: tabFadeIn 0.18s ease; }
@keyframes tabFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.storage-tile[data-active="1"] { border-color: var(--color-brand-500); background-color: var(--color-brand-50); }
[data-theme="dark"] .storage-tile[data-active="1"] { background-color: rgb(99 102 241 / 0.15); }
.storage-tile[data-active="0"]:hover { border-color: #d1d5db; }
[data-theme="dark"] .storage-tile[data-active="0"] { background-color: rgb(30 41 59); border-color: rgb(51 65 85); }
[data-theme="dark"] .storage-tile[data-active="0"]:hover { border-color: rgb(71 85 105); }
</style>
@endpush

@push('scripts')
<script>
onAdminReady(function () {
    function activateTab(tabName) {
        if (!tabName || !$('#' + tabName).length) return;
        $('.tab-content').addClass('hidden').removeClass('tab-fade');
        var $tab = $('#' + tabName).removeClass('hidden');
        // Force reflow so animation restarts
        $tab[0].offsetWidth;
        $tab.addClass('tab-fade');
        $('.tab-button').removeClass('border-brand-600 text-gray-900').addClass('border-transparent text-gray-600');
        $('.tab-button[data-tab="' + tabName + '"]').removeClass('border-transparent text-gray-600').addClass('border-brand-600 text-gray-900');
    }

    // Tab switching
    $(document).on('click', '.tab-button', function () {
        var tabName = $(this).data('tab');
        activateTab(tabName);
        // Update URL hash without scrolling
        history.replaceState(null, '', '#' + tabName);
    });

    // Restore tab from URL hash on load
    if (window.location.hash) {
        activateTab(window.location.hash.slice(1));
    }

    // React to hash changes (e.g. when user clicks a search result while already on Settings)
    $(window).on('hashchange', function () {
        if (window.location.hash) {
            activateTab(window.location.hash.slice(1));
        }
    });

    // Submit each tab's form with a hidden _tab so server can redirect back here
    $('.tab-content form').each(function () {
        var tabName = $(this).closest('.tab-content').attr('id');
        $(this).prepend('<input type="hidden" name="_tab" value="' + tabName + '">');
    });

    // File preview (label natively opens picker since input is inside it)
    $(document).on('change', 'input[type="file"]', function () {
        var file = this.files[0];
        if (!file) return;
        handleFileDrop($(this).closest('label'), file);
    });

    // Drag and drop on upload labels
    $(document).on('dragover', 'label:has(input[type="file"])', function (e) {
        e.preventDefault();
        $(this).addClass('border-brand-400 bg-brand-50');
    });
    $(document).on('dragleave', 'label:has(input[type="file"])', function () {
        $(this).removeClass('border-brand-400 bg-brand-50');
    });
    $(document).on('drop', 'label:has(input[type="file"])', function (e) {
        e.preventDefault();
        $(this).removeClass('border-brand-400 bg-brand-50');
        var file = e.originalEvent.dataTransfer.files[0];
        if (!file) return;
        $(this).find('input[type="file"]')[0].files = e.originalEvent.dataTransfer.files;
        handleFileDrop($(this), file);
    });

    function handleFileDrop($label, file) {
        // Update filename in the info bar (first span)
        $label.find('.upload-info span').first().text(file.name);

        if (file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var $preview = $label.find('.upload-preview');
                // Replace whatever is in the preview area with a new <img>
                $preview.empty().append(
                    $('<img alt="Preview" class="max-h-full max-w-full object-contain">').attr('src', e.target.result)
                );
            };
            reader.readAsDataURL(file);
        }
    }

    // Font preview
    function updateFontPreview(font) {
        var $text = $('#font_preview_text');
        var $label = $('#font_preview_label');
        if (!font || font === 'system') {
            $text.css('font-family', "'Manrope', sans-serif");
            $label.text('Default (Manrope)');
            return;
        }
        var id = 'gfont-' + font.replace(/\s+/g, '-');
        if (!document.getElementById(id)) {
            $('<link>').attr({ id: id, rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(font) + ':wght@400;600&display=swap' }).appendTo('head');
        }
        $text.css('font-family', "'" + font + "', sans-serif");
        $label.text(font);
    }
    updateFontPreview($('#site_font_select').val());
    $('#site_font_select').on('change', function () { updateFontPreview($(this).val()); });

    // Mail driver toggle
    $('#mail_driver').on('change', function () {
        var val = $(this).val();
        $('.driver-fields').addClass('hidden');
        if (val !== 'log') { $('#driver-' + val).removeClass('hidden'); }
    });

    // SMS driver toggle
    $('#sms_driver').on('change', function () {
        var val = $(this).val();
        $('.sms-fields').addClass('hidden');
        if (val !== 'log') { $('#sms-' + val).removeClass('hidden'); }
    });

    // Storage driver tiles
    function activateStorageTile(val) {
        $('#storage_driver_input').val(val);

        $('.storage-tile').each(function () {
            var isActive = $(this).data('storage-driver') === val;
            $(this).attr('data-active', isActive ? '1' : '0');
            $(this).find('.storage-check').remove();
            if (isActive) {
                $(this).append('<div class="storage-check absolute top-2.5 right-2.5 h-4 w-4 rounded-full bg-brand-500 flex items-center justify-center"><svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>');
            }
        });

        $('[class*="storage-fields-"]').addClass('hidden');
        $('.storage-fields-' + val).removeClass('hidden');
    }

    $(document).on('click', '.storage-tile', function () {
        activateStorageTile($(this).data('storage-driver'));
    });

    // Init on page load
    activateStorageTile($('#storage_driver_input').val() || 'local');
});
</script>
@endpush

@push('scripts')
<script>
// AI Setup tab — show/hide key field
$(function () {
    $('#ai_show_key_btn').on('click', function () {
        var inp = $('#ai_openai_key_input');
        inp.attr('type', inp.attr('type') === 'password' ? 'text' : 'password');
        $(this).text(inp.attr('type') === 'password' ? 'Show' : 'Hide');
    });
});
</script>
@endpush

@endsection
