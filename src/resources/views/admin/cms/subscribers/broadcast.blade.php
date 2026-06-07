@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl space-y-5">

    <div class="flex items-start gap-3">
        <a href="{{ route('admin.cms.subscribers.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Broadcast Newsletter</h1>
            <p class="text-sm text-gray-500">Sending to {{ $total }} confirmed subscribers.</p>
        </div>
    </div>

    @if($total === 0)
    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5 text-sm text-yellow-800">
        No confirmed subscribers to send to.
    </div>
    @else
    <form method="POST" action="{{ route('admin.cms.subscribers.broadcast.send') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="offset" value="0">

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5">
            <x-admin::form.input name="subject" label="Subject" :value="old('subject')" required />

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Body <span class="text-red-500">*</span></label>
                <textarea name="body" rows="12" required
                          class="w-full rounded-lg border {{ $errors->has('body') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-400">Plain text or basic HTML. An unsubscribe link is appended automatically.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-paper-airplane class="h-4 w-4" />
                Send to {{ $total }} subscribers
            </button>
            <a href="{{ route('admin.cms.subscribers.index') }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>
    </form>
    @endif
</div>
@endsection
