@extends('admin.layouts.app')

@section('content')
<div class="max-w-lg space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('New Category') }}</h1>
        <p class="mt-0.5 text-sm text-gray-500">{{ __('Create a new contract template category.') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.contracts.categories.store') }}" class="space-y-4">
        @csrf
        @include('admin.contracts.categories._form')
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                {{ __('Create Category') }}
            </button>
            <a href="{{ route('admin.contracts.categories.index') }}" class="text-sm text-gray-500 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>

</div>
@endsection
