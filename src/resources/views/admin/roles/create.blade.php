@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-6">

    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-5">
        @csrf

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.roles.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Create Role</h1>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-6 mt-5">
            <x-form.input name="name" label="Role name" placeholder="e.g. moderator" :value="old('name')"
                hint="Lowercase letters and underscores only." required />

            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Permissions</p>
                @foreach($permissions as $group => $perms)
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase text-gray-400 mb-2">{{ ucfirst($group) }}</p>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach($perms as $perm)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                   @checked(in_array($perm->id, old('permissions', [])))
                                   class="rounded border-gray-300 text-brand-600">
                            {{ $perm->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                Create role
            </button>
            <a href="{{ route('admin.roles.index') }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
