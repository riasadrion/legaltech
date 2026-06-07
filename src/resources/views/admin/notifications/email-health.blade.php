@extends('admin.layouts.app')

@section('title', 'Email Health')

@section('content')
<x-admin::page-header title="Email Health" back="{{ route('admin.notifications.templates.index') }}" />

<div class="max-w-2xl space-y-5">

    @if(!$domain)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
        <p class="text-sm text-yellow-800">No sender email configured. Set <strong>Mail From Address</strong> in
            <a href="{{ route('admin.settings.index') }}" class="underline">Mail Settings</a> first.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-600">Checking DNS records for <strong>{{ $domain }}</strong></p>
    </div>

    @foreach(['spf' => 'SPF', 'dkim' => 'DKIM', 'dmarc' => 'DMARC'] as $key => $label)
    @php $check = $checks[$key]; @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3 mb-2">
            @if($check['status'] === 'ok')
            <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></span>
            @else
            <span class="w-3 h-3 rounded-full bg-red-500 flex-shrink-0"></span>
            @endif
            <h2 class="font-semibold text-gray-900">{{ $label }}</h2>
            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                {{ $check['status'] === 'ok' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $check['status'] === 'ok' ? 'Found' : 'Missing' }}
            </span>
        </div>

        @if($check['value'])
        <code class="block text-xs bg-gray-50 border border-gray-200 rounded p-3 break-all mt-2">{{ $check['value'] }}</code>
        @endif

        @if($check['hint'])
        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <p class="text-xs text-yellow-800 font-medium mb-1">Action required</p>
            <code class="text-xs text-yellow-700 break-all">{{ $check['hint'] }}</code>
        </div>
        @endif
    </div>
    @endforeach
    @endif

</div>
@endsection
