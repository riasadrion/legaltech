@extends('theme::layouts.frontend')

@section('title', config('app.name'))

@section('content')
<div class="py-24 text-center">
    <h1 class="text-4xl font-bold text-gray-900">{{ config('app.name') }}</h1>
    <p class="mt-4 text-gray-500">Welcome. Set up your homepage in the admin panel.</p>
</div>
@endsection
