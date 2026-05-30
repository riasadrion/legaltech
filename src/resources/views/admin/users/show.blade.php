@extends('admin.layouts.app')

@section('content')
@php
    $displayName = $user->name ?: $user->email;
    $initial = strtoupper(substr($displayName, 0, 1));

    $statusTone = match ($user->status) {
        'active'   => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'inactive'  => 'border-slate-200 bg-slate-100 text-slate-700',
        'banned'    => 'border-rose-200 bg-rose-50 text-rose-700',
        default     => 'border-slate-200 bg-slate-100 text-slate-600',
    };

    $kycTone = match ($user->kyc_status ?? 'none') {
        'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'pending'  => 'border-amber-200 bg-amber-50 text-amber-700',
        'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
        default    => 'border-slate-200 bg-slate-100 text-slate-600',
    };

    $subscriptionTone = $subscription && $subscription->isActive()
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-slate-200 bg-slate-100 text-slate-600';
@endphp

<div class="space-y-5 pb-8">


    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-50">{{ $displayName }}</h1>
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $statusTone }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                <p class="mt-0.5 inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                    <x-heroicon-o-clock class="h-4 w-4" />
                    Joined {{ formatDate($user->created_at, false) }}
                </p>
            </div>
        </div>

        @if(auth('admin')->user()->can('users.delete'))
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    data-confirm="Permanently delete this user? This cannot be undone."
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-200 dark:border-rose-900 bg-white dark:bg-slate-900 px-3 py-2 text-xs font-semibold text-rose-700 dark:text-rose-400 shadow-sm transition hover:bg-rose-50 dark:hover:bg-rose-950">
                <x-heroicon-o-trash class="h-4 w-4" />
                Delete user
            </button>
        </form>
        @endif
    </div>


    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-12">
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm lg:col-span-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-700 text-xl font-semibold text-white">
                {{ $initial }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-base font-semibold text-slate-900 dark:text-slate-50">{{ $displayName }}</p>
                <p class="mt-0.5 truncate text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm lg:col-span-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25">
                <x-heroicon-o-banknotes class="h-5 w-5" />
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Balance</p>
                <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-50">${{ number_format($user->balance ?? 0, 2) }}</p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm lg:col-span-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/25">
                <x-heroicon-o-identification class="h-5 w-5" />
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">KYC</p>
                <p class="mt-1"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $kycTone }}">{{ ucfirst($user->kyc_status ?? 'None') }}</span></p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm lg:col-span-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-slate-600 to-slate-700 text-white shadow-lg shadow-slate-500/25">
                <x-heroicon-o-credit-card class="h-5 w-5" />
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Subscription</p>
                <p class="mt-1"><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $subscriptionTone }}">{{ $subscription && $subscription->isActive() ? 'Active' : 'None' }}</span></p>
            </div>
        </div>
        <div class="flex items-start gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm lg:col-span-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white shadow-lg {{ $user->email_verified_at ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-emerald-500/25' : 'bg-gradient-to-br from-slate-500 to-slate-600 shadow-slate-500/25' }}">
                @if($user->email_verified_at)
                    <x-heroicon-s-check-circle class="h-5 w-5" />
                @else
                    <x-heroicon-o-envelope class="h-5 w-5" />
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Email</p>
                @if($user->email_verified_at)
                    <p class="mt-1 text-sm font-semibold text-emerald-700 dark:text-emerald-400">Verified</p>
                @else
                    <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Unverified</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12">

        <div class="space-y-5 lg:col-span-4">
            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Profile details</h3>
                </div>
                <dl class="divide-y divide-slate-100 dark:divide-slate-800">
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Email verified</dt>
                        <dd class="text-right">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                    <x-heroicon-s-check-circle class="h-3.5 w-3.5" />
                                    {{ formatDate($user->email_verified_at, false) }}
                                </span>
                            @else
                                <span class="text-sm text-slate-500 dark:text-slate-400">Not verified</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Account status</dt>
                        <dd><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $statusTone }}">{{ ucfirst($user->status) }}</span></dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">KYC status</dt>
                        <dd><span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $kycTone }}">{{ ucfirst($user->kyc_status ?? 'None') }}</span></dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">2FA</dt>
                        <dd>
                            @if($user->two_factor_enabled)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                    <x-heroicon-s-shield-check class="h-3.5 w-3.5" />
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Disabled</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Country</dt>
                        <dd class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $user->country_code ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Mobile</dt>
                        <dd class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $user->mobile ? ($user->mobile_country_code . ' ' . $user->mobile) : '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Timezone</dt>
                        <dd class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $user->timezone ?: '—' }}</dd>
                    </div>
                </dl>

                @if(auth('admin')->user()->can('users.manage'))
                <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 px-4 py-3">
                    @if($user->email_verified_at)
                        <form method="POST" action="{{ route('admin.users.unverify-email', $user) }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-400 transition hover:text-slate-900 dark:hover:text-slate-100">
                                <x-heroicon-o-arrow-uturn-left class="h-4 w-4" />
                                Remove verification
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.verify-email', $user) }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 transition hover:text-brand-800">
                                <x-heroicon-o-check class="h-4 w-4" />
                                Mark email verified
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Subscription</h3>
                    <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $subscriptionTone }}">
                        {{ $subscription && $subscription->isActive() ? 'Active' : 'None' }}
                    </span>
                </div>

                @if($subscription && $subscription->isActive())
                    <dl class="divide-y divide-slate-100 dark:divide-slate-800">
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Plan</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ $subscription->plan->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Credits remaining</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ number_format($subscription->credits_remaining) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Period ends</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ formatDate($subscription->current_period_end, false) ?? '—' }}</dd>
                        </div>
                    </dl>
                    @if(auth('admin')->user()->can('users.manage'))
                    <form method="POST" action="{{ route('admin.users.cancel-subscription', $user) }}" class="border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 px-4 py-3" data-confirm="Cancel this user's subscription?">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300">Cancel subscription</button>
                    </form>
                    @endif
                @else
                    <div class="px-4 py-4 text-sm text-slate-500 dark:text-slate-400">No active subscription.</div>
                @endif

                @if(auth('admin')->user()->can('users.manage'))
                <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 px-4 py-3">
                    <form method="POST" action="{{ route('admin.users.assign-plan', $user) }}" class="flex flex-col gap-2 sm:flex-row">
                        @csrf
                        <select name="plan_id"
                                class="min-w-0 flex-1 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-slate-100 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <option value="">— Select plan —</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->formattedPrice() }}/{{ $plan->billing_cycle }})</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100">
                            Assign
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>


        <div class="space-y-5 lg:col-span-8">
            @if(auth('admin')->user()->can('users.manage'))
            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Account controls</h3>
                </div>
                <div class="grid grid-cols-1 divide-y divide-slate-100 dark:divide-slate-800 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
                    <form method="POST" action="{{ route('admin.users.update-status', $user) }}" class="space-y-3 p-4">
                        @csrf
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400">Account status</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="active" class="peer sr-only" {{ $user->status === 'active' ? 'checked' : '' }}>
                                <span class="flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition peer-checked:ring-2 peer-checked:ring-emerald-500 peer-checked:ring-offset-1">Active</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="inactive" class="peer sr-only" {{ $user->status === 'inactive' ? 'checked' : '' }}>
                                <span class="flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 transition peer-checked:ring-2 peer-checked:ring-slate-400 peer-checked:ring-offset-1">Inactive</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="banned" class="peer sr-only" {{ $user->status === 'banned' ? 'checked' : '' }}>
                                <span class="flex items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition peer-checked:ring-2 peer-checked:ring-rose-500 peer-checked:ring-offset-1">Banned</span>
                            </label>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100">
                                Update status
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.users.update-kyc', $user) }}" class="space-y-3 p-4">
                        @csrf
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400">KYC status</label>
                        <select name="kyc_status"
                                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-slate-100 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <option value="none" @selected(($user->kyc_status ?? 'none') === 'none')>None</option>
                            <option value="pending" @selected($user->kyc_status === 'pending')>Pending</option>
                            <option value="approved" @selected($user->kyc_status === 'approved')>Approved</option>
                            <option value="rejected" @selected($user->kyc_status === 'rejected')>Rejected</option>
                        </select>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100">
                                Update KYC
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Financial and security</h3>
                </div>
                <div class="grid grid-cols-1 divide-y divide-slate-100 dark:divide-slate-800 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
                    <form method="POST" action="{{ route('admin.users.adjust-balance', $user) }}" class="space-y-3 p-4">
                        @csrf
                        <div class="flex items-center justify-between gap-3">
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Adjust balance</label>
                            <span class="rounded-full border border-brand-200 bg-brand-50 px-2.5 py-0.5 text-sm font-semibold text-brand-700">
                                ${{ number_format($user->balance ?? 0, 2) }}
                            </span>
                        </div>
                        <input type="number"
                               name="amount"
                               step="0.01"
                               placeholder="e.g. 50.00 or -20.00"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-slate-100 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                               required />
                        <input type="text"
                               name="note"
                               placeholder="Reason..."
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-slate-100 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20" />
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100">
                                Apply
                            </button>
                        </div>
                    </form>

                    @if(auth('admin')->user()->can('users.delete'))
                    <form method="POST" action="{{ route('admin.users.update-password', $user) }}" class="space-y-3 p-4">
                        @csrf
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400">Change password</label>
                        <input type="password"
                               name="password"
                               placeholder="Min 8 characters"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-slate-100 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                               required />
                        <input type="password"
                               name="password_confirmation"
                               placeholder="Repeat password"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-slate-100 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                               required />
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100">
                                Update
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Recent logins</h3>
                </div>

                @if($logins->isEmpty())
                    <div class="px-4 py-10 text-center">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                            <x-heroicon-o-arrow-right-end-on-rectangle class="h-5 w-5" />
                        </div>
                        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No login history yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($logins as $login)
                        <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-950 text-brand-600 dark:text-brand-400">
                                        <x-heroicon-o-clock class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ formatDate($login->logged_in_at) }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">IP {{ $login->ip_address ?: '—' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:max-w-xl">
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $login->user_agent ?: '—' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
onAdminReady(function () {
    'use strict';
    $('input[name="status"]').on('change', function () {
        $(this).closest('form').find('span').removeClass('ring-2 ring-offset-2');
    });
});
</script>
@endpush
@endsection
