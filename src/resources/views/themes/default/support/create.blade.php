@extends('theme::layouts.frontend')
@section('title', 'Open a Ticket')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('user.support.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← My Tickets</a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Open a Support Ticket</h1>

    <form method="POST" action="{{ route('user.support.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        <x-form.input name="subject" label="Subject" :value="old('subject')" required />

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500 @error('category') border-red-500 @enderror">
                @foreach(\App\Domain\Support\Models\SupportTicket::categories() as $cat)
                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
            <textarea name="body" rows="8" required placeholder="Describe your issue in detail…"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500 @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
            <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.txt,.zip"
                class="text-sm text-gray-600">
            <p class="text-xs text-gray-400 mt-1">Up to 5 files, 10 MB each. Accepted: PDF, images, Word, text, ZIP.</p>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Submit Ticket</button>
            <a href="{{ route('user.support.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
