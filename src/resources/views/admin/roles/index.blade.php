@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">


    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Roles')</h1>
            <p class="mt-0.5 text-sm text-gray-500">@lang('Manage admin roles and permissions.')</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('Create New Role')
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Role')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Permissions')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900" :data-label="__('Role')">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</td>
                    <td class="px-4 py-3 text-gray-600" :data-label="__('Permissions')">{{ $role->permissions_count }}</td>
                    <td class="px-4 py-3 text-right" data-label="">
                        @if($role->name === 'super_admin')
                        <span class="text-xs text-gray-400">@lang('Protected')</span>
                        @else
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-transparent px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-slate-300 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-800">
                                <x-heroicon-o-pencil class="h-3.5 w-3.5" />
                                @lang('Edit')
                            </a>
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" :data-confirm="__('Delete this role?')"
                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 dark:border-red-900/50 bg-white dark:bg-transparent px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 shadow-sm hover:bg-red-50 dark:hover:bg-red-950/30">
                                    <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                    @lang('Delete')
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No roles found.')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
