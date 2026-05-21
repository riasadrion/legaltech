@extends('admin.layouts.app')

@section('title', 'Scheduler')

@section('content')
<x-page-header title="Scheduler" subtitle="View and manually trigger registered scheduled commands." />



<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Command</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Frequency</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Last Run</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($tasks as $task)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-800" data-label="Command">{{ $task['command'] }}</td>
                    <td class="px-4 py-3 text-gray-600" data-label="Description">{{ $task['description'] }}</td>
                    <td class="px-4 py-3 text-gray-500" data-label="Frequency">{{ $task['frequency'] }}</td>
                    <td class="px-4 py-3 text-gray-500" data-label="Last Run">
                        {{ $task['last_run'] ? \Carbon\Carbon::parse($task['last_run'])->diffForHumans() : '—' }}
                    </td>
                    <td class="px-4 py-3" data-label="Actions">
                        <form method="POST" action="{{ route('admin.ops.scheduler.run') }}">
                            @csrf
                            <input type="hidden" name="command" value="{{ $task['command'] }}">
                            <button type="submit"
                                class="px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded hover:bg-blue-100">
                                Run now
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
        <p class="text-sm font-medium text-gray-700 mb-2">Server Cron Line</p>
        <p class="text-xs text-gray-500 mb-2">Add this to your server's crontab so scheduled commands run automatically:</p>
        <code class="block bg-white border border-gray-200 rounded px-3 py-2 text-xs font-mono text-gray-800 break-all">{{ $cronLine }}</code>
    </div>

    @if(session('command_output'))
    <div class="bg-gray-900 text-green-400 font-mono text-xs rounded-xl p-4 whitespace-pre-wrap">{{ session('command_output') }}</div>
    @endif
</div>
@endsection
