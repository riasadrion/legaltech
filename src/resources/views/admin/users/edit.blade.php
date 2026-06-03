@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl space-y-5">

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.users.show', $user) }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit User</h1>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-5 mt-5">
            <x-form.input name="name" label="Name" :value="old('name', $user->name)" />
            <x-form.input name="email" label="Email" type="email" :value="old('email', $user->email)" required />

            <x-form.select name="status" label="Status">
                <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
                <option value="banned" @selected(old('status', $user->status) === 'banned')>Banned</option>
            </x-form.select>

            <x-form.select name="kyc_status" label="KYC Status">
                <option value="none" @selected(old('kyc_status', $user->kyc_status) === 'none')>None</option>
                <option value="pending" @selected(old('kyc_status', $user->kyc_status) === 'pending')>Pending</option>
                <option value="approved" @selected(old('kyc_status', $user->kyc_status) === 'approved')>Approved</option>
                <option value="rejected" @selected(old('kyc_status', $user->kyc_status) === 'rejected')>Rejected</option>
            </x-form.select>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                Save changes
            </button>
            <a href="{{ route('admin.users.show', $user) }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
