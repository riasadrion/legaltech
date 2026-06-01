@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">


    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Users')</h1>
        <p class="mt-0.5 text-sm text-gray-500">{{ __(':count registered users', ['count' => number_format($users->total())]) }}</p>
    </div>


    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-status-pills :items="$statusPills" :current="request('status', '')" />
        <form method="GET" class="flex items-center gap-2">
            <select name="kyc" data-auto-submit
                    class="h-9 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <option value="">@lang('All KYC')</option>
                <option value="none"     @selected(request('kyc') === 'none')>@lang('None')</option>
                <option value="pending"  @selected(request('kyc') === 'pending')>@lang('Pending')</option>
                <option value="approved" @selected(request('kyc') === 'approved')>@lang('Approved')</option>
                <option value="rejected" @selected(request('kyc') === 'rejected')>@lang('Rejected')</option>
            </select>
            <input type="text" name="q" value="{{ request('q') }}" :placeholder="__('Name or email…')"
                    class="h-9 w-52 rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">@lang('Search')</button>
            @if(request()->hasAny(['q','kyc']))
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">@lang('Clear')</a>
            @endif
        </form>
    </div>


    <form method="POST" action="{{ route('admin.users.bulk') }}" id="bulk-form">
        @csrf


        <div id="bulk-bar" class="hidden mb-3 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 shadow-sm">
            <span class="text-sm font-semibold text-amber-700" id="bulk-count">@lang('0 selected')</span>
            <span class="h-4 w-px bg-amber-200"></span>
            <button type="submit" name="action" value="ban"
                    :data-confirm="__('Ban selected users?')"
                    class="rounded-md bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-yellow-300 hover:bg-yellow-200">
                @lang('Ban')
            </button>
            <button type="submit" name="action" value="unban"
                    class="rounded-md bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 ring-1 ring-green-300 hover:bg-green-200">
                @lang('Unban')
            </button>
            <button type="submit" name="action" value="delete"
                    :data-confirm="__('Delete selected users? This cannot be undone.')"
                    class="rounded-md bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 ring-1 ring-red-300 hover:bg-red-200">
                @lang('Delete')
            </button>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="hide-mobile w-10 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <input type="checkbox" id="select-all"
                                   class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('User')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('KYC')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Email')</th>
                        <th class="hide-mobile px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Joined')</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="hide-mobile px-4 py-3 text-center" data-label="">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                   class="row-check h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        </td>
                        <td class="px-4 py-3" :data-label="__('User')">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">
                                    {{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 leading-tight">{{ $user->name ?: '—' }}</div>
                                    <div class="text-xs text-gray-400 leading-tight mt-0.5">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3" :data-label="__('Status')">
                            @php
                                $sc = match($user->status) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'banned' => 'bg-red-100 text-red-700',
                                    default  => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $sc }}">
                                {{ ucfirst(__($user->status)) }}
                            </span>
                        </td>
                        <td class="hide-mobile px-4 py-3" :data-label="__('KYC')">
                            @php
                                $kc = match($user->kyc_status ?? 'none') {
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'pending'  => 'bg-amber-100 text-amber-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $kc }}">
                                {{ ucfirst(__($user->kyc_status ?? 'none')) }}
                            </span>
                        </td>
                        <td class="hide-mobile px-4 py-3" :data-label="__('Email')">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                    <x-heroicon-s-check-circle class="h-3.5 w-3.5" /> @lang('Verified')
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                    <x-heroicon-o-x-circle class="h-3.5 w-3.5" /> @lang('Unverified')
                                </span>
                            @endif
                        </td>
                        <td class="hide-mobile px-4 py-3 text-xs text-gray-500 whitespace-nowrap" :data-label="__('Joined')">
                            {{ formatDate($user->created_at, false) }}
                        </td>
                        <td class="px-4 py-3 text-right" data-label="">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                        data-impersonate="{{ route('admin.users.impersonate', $user) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-950 px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-300 shadow-sm hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-800 dark:hover:text-blue-200"
                                        title="Login as this user">
                                    <x-heroicon-o-arrow-right-on-rectangle class="h-3.5 w-3.5" />
                                    @lang('Login')
                                </button>
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200">
                                    <x-heroicon-o-eye class="h-3.5 w-3.5" />
                                    @lang('View')
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-14 text-center text-sm text-gray-400">
                            @if(request()->hasAny(['q','status','kyc']))
                                @lang('No users match your filters.') <a href="{{ route('admin.users.index') }}" class="text-brand-600 hover:underline">@lang('Clear filters')</a>
                            @else
                                @lang('No users registered yet.')
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($users->hasPages())
            <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
                <span>{{ __('Showing :first–:last of :total', ['first' => $users->firstItem(), 'last' => $users->lastItem(), 'total' => number_format($users->total())]) }}</span>
                <x-pagination :paginator="$users" />
            </div>
            @endif
        </div>
    </form>

</div>


<form id="impersonate-form" method="POST" action="" class="hidden" target="_blank">
    @csrf
</form>

@push('scripts')
<script>
onAdminReady(function () {
    'use strict';

    $(document).on('click', '[data-impersonate]', function () {
        $('#impersonate-form').attr('action', $(this).data('impersonate')).submit();
    });

    $('[data-auto-submit]').on('change', function () {
        $('#filter-form').trigger('submit');
    });

    $('#select-all').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
        updateBulkBar();
    });

    $(document).on('change', '.row-check', function () {
        $('#select-all').prop('checked', $('.row-check').length === $('.row-check:checked').length);
        updateBulkBar();
    });

    function updateBulkBar() {
        var n = $('.row-check:checked').length;
        if (n > 0) {
            $('#bulk-bar').removeClass('hidden');
            $('#bulk-count').text(n + (n === 1 ? ' @lang('user selected')' : ' @lang('users selected')'));
        } else {
            $('#bulk-bar').addClass('hidden');
        }
    }
});
</script>
@endpush
@endsection
