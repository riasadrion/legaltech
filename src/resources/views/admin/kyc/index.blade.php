@extends('admin.layouts.app')

@section('title', __('KYC Submissions'))

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('KYC Submissions')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Review identity verification submissions.')</p>
    </div>

    <x-status-pills :items="$statusPills" :current="$status" />

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Date')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('ID Type')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($submissions as $sub)
                @php $sc = match($sub->status) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-amber-100 text-amber-700' }; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" :data-label="__('User')">
                        <div class="font-medium text-gray-900">{{ $sub->user->name }}</div>
                        <div class="text-xs text-gray-400">{{ $sub->user->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap" :data-label="__('Date')">{{ formatDate($sub->created_at) }}</td>
                    <td class="px-4 py-3 text-gray-700" :data-label="__('ID Type')">{{ $sub->data['id_type'] ? ucfirst(str_replace('_', ' ', __($sub->data['id_type']))) : '—' }}</td>
                    <td class="px-4 py-3" :data-label="__('Status')">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst(__($sub->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.kyc.show', $sub) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-slate-300 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
                            @lang('Review')
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No KYC submissions found.')</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($submissions->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $submissions->firstItem(), 'last' => $submissions->lastItem(), 'total' => $submissions->total()]) }}</span>
            <x-pagination :show-total="false" :paginator="$submissions" />
        </div>
        @endif
    </div>
</div>
@endsection
