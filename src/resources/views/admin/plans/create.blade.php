@extends('admin.layouts.app')

@php
$plan = $plan ?? null;
@endphp

@section('content')
<div class="space-y-6 max-w-6xl">
    <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-5">
        @csrf

        <div class="flex items-start gap-4">
            <a href="{{ route('admin.plans.index') }}"
               class="flex items-center justify-center h-8 w-8 rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ __('New Plan') }}</h1>
                <p class="text-sm text-gray-400">{{ __('Create a new subscription plan') }}</p>
            </div>
        </div>

        @include('admin.plans._form')

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                {{ __('Create Plan') }}
            </button>
            <a href="{{ route('admin.plans.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
