@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl">
    <x-page-header title="Add Language" subtitle="Add a new language to your site." />

    <form method="POST" action="{{ route('admin.languages.store') }}" class="space-y-5">
        @csrf

        <div class="rounded-xl border border-gray-200 bg-white px-6 py-5 space-y-5">
            <x-form.input name="name" label="Language Name (English)" placeholder="Arabic" required />
            <x-form.input name="native_name" label="Native Name" placeholder="العربية" required />
            <x-form.input name="code" label="ISO 639-1 Code" placeholder="ar" maxlength="2" required />

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Text Direction</label>
                <select name="direction" class="w-full h-9 rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="ltr" @selected(old('direction', 'ltr') === 'ltr')>LTR — Left to Right</option>
                    <option value="rtl" @selected(old('direction') === 'rtl')>RTL — Right to Left</option>
                </select>
            </div>

            <x-form.input name="sort" label="Sort Order" type="number" :value="old('sort', 0)" />

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-brand-600">
                Active (visible to users)
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" id="add-lang-btn"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg id="add-lang-spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <x-heroicon-o-check id="add-lang-icon" class="h-4 w-4" />
                <span id="add-lang-text">Add Language</span>
            </button>
            <a href="{{ route('admin.languages.index') }}" class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('add-lang-btn');
    const spinner = document.getElementById('add-lang-spinner');
    const icon = document.getElementById('add-lang-icon');
    const text = document.getElementById('add-lang-text');

    btn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = 'Creating language…';
});
</script>
@endpush
@endsection
