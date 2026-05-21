@extends('admin.layouts.app')

@section('title', 'System Health')

@section('content')
<x-page-header title="System Health" subtitle="Monitor system status and manage application caches." />

<div class="space-y-6">
    {{-- Health indicators --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['key' => 'db',      'label' => 'Database'],
            ['key' => 'storage', 'label' => 'Storage'],
            ['key' => 'disk',    'label' => 'Disk Space'],
            ['key' => 'dns',     'label' => 'DNS / Email'],
        ] as $item)
        @php $h = $health[$item['key']]; @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-2">
                @if($h['status'] === 'ok')
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                @elseif($h['status'] === 'warn')
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span>
                @else
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
                @endif
                <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
            </div>
            <p class="text-xs text-gray-500">{{ $h['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Cache flush --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Cache Management</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($flushOptions as $opt)
            <form method="POST" action="{{ route('admin.system.flush') }}">
                @csrf
                <input type="hidden" name="command" value="{{ $opt['command'] }}">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Clear {{ $opt['label'] }}
                </button>
            </form>
            @endforeach
        </div>
    </div>

</div>
@endsection
