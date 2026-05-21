@php
    $p = $post ?? null;
    $languages = \App\Domain\I18n\Models\Language::where('is_active', true)->orderBy('sort')->get();
@endphp

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:items-start">

    {{-- Main content --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5">
            <x-translatable-input name="title" label="Title" :values="old('title', $p?->title ?? [])" required />

            <div class="translatable-input space-y-2">
                <span class="block text-sm font-medium text-gray-700">Body</span>
                <div class="flex gap-1.5">
                    @foreach ($languages as $lang)
                        <button type="button"
                                class="locale-tab rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors
                                       {{ $loop->first ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600' }}"
                                data-locale="{{ $lang->code }}"
                                data-target="body">
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>
                @foreach ($languages as $lang)
                    <div class="locale-panel {{ $loop->first ? '' : 'hidden' }}" data-locale="{{ $lang->code }}" data-field="body">
                        <input type="hidden" name="body[{{ $lang->code }}]" id="body_{{ $lang->code }}" value="{{ old('body.' . $lang->code, $p?->body[$lang->code] ?? '') }}">
                        <div class="prose-editor-init" data-input="body_{{ $lang->code }}"></div>
                        @error('body.' . $lang->code)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            <div class="translatable-input space-y-2">
                <span class="block text-sm font-medium text-gray-700">Excerpt</span>
                <div class="flex gap-1.5">
                    @foreach ($languages as $lang)
                        <button type="button"
                                class="locale-tab rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors
                                       {{ $loop->first ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600' }}"
                                data-locale="{{ $lang->code }}"
                                data-target="excerpt">
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>
                @foreach ($languages as $lang)
                    <div class="locale-panel {{ $loop->first ? '' : 'hidden' }}" data-locale="{{ $lang->code }}" data-field="excerpt">
                        <textarea name="excerpt[{{ $lang->code }}]" rows="2" dir="{{ $lang->direction }}"
                                  class="w-full rounded-lg border {{ $errors->has('excerpt.' . $lang->code) ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('excerpt.' . $lang->code, $p?->excerpt[$lang->code] ?? '') }}</textarea>
                        @error('excerpt.' . $lang->code)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SEO --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-900">SEO</h3>
            <x-translatable-input name="meta_title" label="Meta title" :values="old('meta_title', $p?->meta_title ?? [])" />
            <x-translatable-input name="meta_description" label="Meta description" type="textarea" :values="old('meta_description', $p?->meta_description ?? [])" />
        </div>
    </div>

    {{-- Sidebar settings --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-900">Settings</h3>

            <x-form.select name="status" label="Status">
                <option value="draft"     @selected(old('status', $p?->status ?? 'draft') === 'draft')>Draft</option>
                <option value="scheduled" @selected(old('status', $p?->status) === 'scheduled')>Scheduled</option>
                <option value="published" @selected(old('status', $p?->status) === 'published')>Published</option>
            </x-form.select>

            <x-form.input name="published_at" label="Publish date" type="datetime-local"
                :value="old('published_at', $p?->published_at?->format('Y-m-d\TH:i'))" />

            <x-form.select name="blog_category_id" label="Category">
                <option value="">— No category —</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('blog_category_id', $p?->blog_category_id) == $cat->id)>
                    {{ $cat->getLocalName() }}
                </option>
                @endforeach
            </x-form.select>

            <x-form.input name="slug" label="Slug" :value="old('slug', $p?->slug ?? '')"
                hint="Auto-generated from title if left blank." />
        </div>

        {{-- Tags --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-3">
            <h3 class="text-sm font-semibold text-gray-900">Tags</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                           @checked(in_array($tag->id, old('tag_ids', $p?->tags->pluck('id')->toArray() ?? [])))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    {{ $tag->getLocalName() }}
                </label>
                @endforeach
            </div>
            @if($tags->isEmpty())
                <p class="text-xs text-gray-400">No tags yet. <a href="{{ route('admin.blog.tags.index') }}" class="text-brand-600">Add tags →</a></p>
            @endif
        </div>
    </div>
</div>

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/editor.css') }}">
<script src="{{ asset('assets/static/js/editor.js') }}"></script>
@endpush

@push('scripts')
<script>
onAdminReady(function () {
    document.querySelectorAll('.prose-editor-init').forEach(function (el) {
        ProseEditor.init(el)
    })
})
</script>
@endpush
