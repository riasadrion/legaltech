@extends('theme::layouts.frontend')

@section('title', 'Contact')

@section('content')
<div class="mx-auto max-w-xl px-4 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Contact us</h1>
    <p class="text-gray-500 mb-8">We'll get back to you as soon as possible.</p>

    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
        @csrf
        {{-- Honeypot --}}
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

        <x-form.input name="name" label="Your name" :value="old('name')" required />
        <x-form.input name="email" label="Email address" type="email" :value="old('email')" required />
        <x-form.input name="subject" label="Subject" :value="old('subject')" required />
        <x-form.textarea name="message" label="Message" rows="6" required />

        <x-turnstile />

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            Send message
        </button>
    </form>
</div>
@endsection
