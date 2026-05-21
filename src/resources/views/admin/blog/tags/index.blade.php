@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl space-y-5">

    <h1 class="text-xl font-bold text-gray-900">{{ __('Tags') }}</h1>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 lg:items-start">

        {{-- Existing tags --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('All tags') }}</h3>
            </div>
            @if($tags->isEmpty())
                <p class="px-5 py-6 text-sm text-gray-400 text-center">{{ __('No tags yet.') }}</p>
            @else
            <ul class="divide-y divide-gray-100">
                @foreach($tags as $tag)
                <li class="flex items-center justify-between px-5 py-3">
                    <div>
                        <span class="text-sm font-medium text-gray-900">{{ $tag->getLocalName() }}</span>
                        <span class="ml-2 text-xs text-gray-400 font-mono">{{ $tag->slug }}</span>
                        <span class="ml-2 text-xs text-gray-400">{{ $tag->posts_count }} {{ __('posts') }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.blog.tags.destroy', $tag) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" data-confirm="{{ __('Delete this tag?') }}"
                                class="text-xs text-red-500 hover:text-red-700">{{ __('Delete') }}</button>
                    </form>
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        {{-- Add tag --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('New tag') }}</h3>
            </div>
            <form method="POST" action="{{ route('admin.blog.tags.store') }}" class="px-5 py-5 space-y-4">
                @csrf
                <x-form.input name="name[en]" label="{{ __('Name') }}" :value="old('name.en')" required />
                <x-form.input name="slug" label="{{ __('Slug') }}" :value="old('slug')" hint="{{ __('Auto-generated if blank.') }}" />
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    <x-heroicon-o-plus class="h-4 w-4" />
                    {{ __('Add tag') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
