@extends('admin.layouts.app')

@section('title', __('Staff'))

@section('content')
<div class="space-y-5">


    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Staff')</h1>
            <p class="mt-0.5 text-sm text-gray-500">@lang('Admin team members.')</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('Create New Staff')
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Name / Email')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Role')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($staff as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" :data-label="__('Name / Email')">
                        <div class="font-medium text-sm text-gray-900">{{ $member->name }}</div>
                        <div class="text-xs text-gray-500">{{ $member->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600" :data-label="__('Role')">
                        {{ $member->roles->pluck('name')->map(fn($n) => ucfirst(str_replace('_', ' ', $n)))->join(', ') ?: '—' }}
                    </td>
                    <td class="px-4 py-3" :data-label="__('Status')">
                        @php
                            $sc = $member->status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        @if(! $member->isSuperAdmin() && $member->id !== auth('admin')->id())
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.staff.edit', $member) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                                <x-heroicon-o-pencil class="h-3.5 w-3.5" />
                                @lang('Edit')
                            </a>
                            <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" :data-confirm="__('Delete this staff member?')"
                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50">
                                    <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                    @lang('Delete')
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No staff found.')</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($staff->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $staff->firstItem(), 'last' => $staff->lastItem(), 'total' => $staff->total()]) }}</span>
            <x-pagination :paginator="$staff" />
        </div>
        @endif
    </div>
</div>
@endsection
