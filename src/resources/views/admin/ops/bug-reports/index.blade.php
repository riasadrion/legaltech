@extends('admin.layouts.app')

@section('title', 'Bug Reports')

@section('content')
<x-page-header title="Bug Reports" subtitle="Track issues flagged from the admin panel." />



<div class="space-y-5">

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Report an Issue</h2>
        <form method="POST" action="{{ route('admin.ops.bug-reports.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                    <select name="severity"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page URL (optional)</label>
                    <input type="url" name="url" value="{{ old('url') }}" maxlength="500"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" required maxlength="5000"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700">
                Submit Report
            </button>
        </form>
    </div>


    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($reports->isEmpty())
            <x-empty-state title="No bug reports" description="No issues have been flagged yet." />
        @else
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Severity</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">By</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" data-label="Title">
                        <div class="font-medium text-gray-800">{{ $report->title }}</div>
                        @if($report->url)
                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ $report->url }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3" data-label="Severity">
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $report->severityBadgeClass() }}">
                            {{ ucfirst($report->severity) }}
                        </span>
                    </td>
                    <td class="px-4 py-3" data-label="Status">
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $report->statusBadgeClass() }}">
                            {{ ucwords(str_replace('_', ' ', $report->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500" data-label="By">{{ $report->admin?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500" data-label="Date">{{ $report->created_at->format('M j, Y') }}</td>
                    <td class="px-4 py-3" data-label="Actions">
                        <button class="text-xs text-brand-600 hover:underline"
                            data-modal-open="edit-report-{{ $report->id }}">Edit</button>
                        <form method="POST" action="{{ route('admin.ops.bug-reports.destroy', $report) }}"
                            class="inline" data-confirm="Delete this report?">
                            @csrf @method('DELETE')
                            <button type="submit" class="ml-2 text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>


                <x-modal id="edit-report-{{ $report->id }}" title="Update Report">
                    <form method="POST" action="{{ route('admin.ops.bug-reports.update', $report) }}">
                        @csrf @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'wont_fix' => "Won't Fix"] as $val => $lbl)
                                    <option value="{{ $val }}" {{ $report->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Note</label>
                                <textarea name="admin_note" rows="3"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ $report->admin_note }}</textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700">
                                Save
                            </button>
                        </div>
                    </form>
                </x-modal>
                @endforeach
            </tbody>
        </table>
        {{ $reports->links() }}
        @endif
    </div>
</div>
@endsection
