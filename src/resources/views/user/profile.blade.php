@extends('theme::layouts.user')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Profile') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ __('Manage your account details.') }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">{{ __('Profile settings — coming soon.') }}</p>
    </div>

</div>
@endsection
