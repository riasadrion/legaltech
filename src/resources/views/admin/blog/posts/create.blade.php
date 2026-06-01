@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">
    <form method="POST" action="{{ route('admin.blog.posts.store') }}" class="space-y-5">
        @csrf

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.blog.posts.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <h1 class="text-xl font-bold text-gray-900">@lang('New Post')</h1>
        </div>

        @include('admin.blog.posts._form')

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                @lang('Create post')
            </button>
            <a href="{{ route('admin.blog.posts.index') }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                @lang('Cancel')
            </a>
        </div>
    </form>
</div>
@endsection
