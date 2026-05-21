@extends('theme::layouts.frontend')
@section('title', 'Unsubscribed')
@section('content')
<div class="max-w-md mx-auto px-4 py-24 text-center">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <x-heroicon-o-check class="w-8 h-8 text-green-600" />
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Unsubscribed</h1>
    <p class="text-gray-600 mb-6"><strong>{{ $email }}</strong> has been removed from our mailing list.</p>
    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Go Home</a>
</div>
@endsection
