<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Name') <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Icon')</label>
    <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}"
           placeholder="e.g. heroicon-o-document-text"
           class="h-9 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
    <p class="mt-1 text-xs text-gray-400">@lang('Heroicon name (optional)')</p>
    @error('icon')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Sort Order')</label>
    <input type="number" name="sort" value="{{ old('sort', $category->sort ?? 0) }}"
           class="h-9 w-24 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
    @error('sort')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
