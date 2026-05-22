@if(\App\Support\Turnstile::enabled())
    @once
        @push('head')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce
    <div class="cf-turnstile" data-sitekey="{{ \App\Support\Turnstile::siteKey() }}" data-theme="dark"></div>
    @error('cf-turnstile-response')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
@endif
