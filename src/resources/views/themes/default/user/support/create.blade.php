@extends('theme::layouts.user')

@section('breadcrumb', __('New Ticket'))

@push('scripts')
<script>
function udShowFiles(input, listId) {
    var list = document.getElementById(listId);
    list.innerHTML = '';
    Array.from(input.files).forEach(function(f) {
        var li = document.createElement('li');
        li.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>'
            + f.name + ' <span class="file-size-label">(' + (f.size/1024).toFixed(0) + ' KB)</span>';
        list.appendChild(li);
    });
}
</script>
@endpush

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">@lang('Open a Support Ticket')</h1>
        <p class="ud-page-sub">{{ __('Describe your issue and we\'ll get back to you as soon as possible.') }}</p>
    </div>

    <div class="ud-card">
        <form method="POST" action="{{ route('user.support.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="ud-label">@lang('Subject')</label>
                <input type="text" name="subject" value="{{ old('subject') }}"
                       class="ud-input mt-1" placeholder="{{ __('Brief description of your issue') }}" required />
                @error('subject') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="ud-label">@lang('Category')</label>
                <select name="category" class="ud-select mt-1" required>
                    <option value="">@lang('Select a category')</option>
                    @foreach(\App\Models\SupportTicket::categories() as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                            {{ __(ucfirst(str_replace('_', ' ', $cat))) }}
                        </option>
                    @endforeach
                </select>
                @error('category') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="ud-label">@lang('Message')</label>
                <textarea name="body" rows="6"
                          class="ud-input mt-1 resize-none"
                          placeholder="{{ __('Describe your issue in detail...') }}" required>{{ old('body') }}</textarea>
                @error('body') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="ud-label">@lang('Attachments') <span class="text-white/30 font-normal">(@lang('optional, max 5 files'))</span></label>
                <label class="ud-dropzone mt-1" id="dropzone-create">
                    <input type="file" name="attachments[]" multiple class="sr-only" id="file-create"
                           accept=".jpg,.jpeg,.png,.webp,.pdf,.txt" onchange="udShowFiles(this,'file-list-create')" />
                    <x-heroicon-o-paper-clip class="h-5 w-5 text-white/30" />
                    <span class="ud-dropzone-text">@lang('Click to attach files')</span>
                    <span class="ud-dropzone-hint">@lang('JPG, PNG, PDF, TXT — max 10 MB each')</span>
                    <ul id="file-list-create" class="ud-file-list"></ul>
                </label>
                @error('attachments') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="ud-btn-primary inline-flex">
                    <x-heroicon-o-paper-airplane class="h-4 w-4" />
                    @lang('Submit Ticket')
                </button>
                <a href="{{ route('user.support.index') }}" class="ud-btn-ghost inline-flex">
                    @lang('Cancel')
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
