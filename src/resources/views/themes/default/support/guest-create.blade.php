@extends('theme::layouts.frontend')
@section('title', 'Contact Support')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Contact Support</h1>
    <p class="text-gray-600 mb-6">Already have an account? <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700">Sign in</a> to track your tickets.</p>

    <form method="POST" action="{{ route('support.guest.store') }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <x-form.input name="guest_name" label="Your Name" :value="old('guest_name')" required />
            <x-form.input name="guest_email" label="Email Address" type="email" :value="old('guest_email')" required />
        </div>

        <x-form.input name="subject" label="Subject" :value="old('subject')" required />

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500">
                @foreach(\App\Domain\Support\Models\SupportTicket::categories() as $cat)
                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
            <textarea name="body" rows="6" required placeholder="Describe your issue…"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500 @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Submit</button>
        </div>
    </form>
</div>
@endsection
