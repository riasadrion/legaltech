@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Edit Template') }}</h1>
        <p class="mt-0.5 text-sm text-gray-500">{{ $template->name }}</p>
    </div>

    <form method="POST" action="{{ route('admin.contracts.templates.update', $template) }}" class="space-y-5">
        @csrf @method('PUT')
        @include('admin.contracts.templates._form')
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                {{ __('Save Changes') }}
            </button>
            <a href="{{ route('admin.contracts.templates.index') }}" class="text-sm text-gray-500 hover:underline">{{ __('Cancel') }}</a>
        </div>
    </form>

</div>
@endsection
