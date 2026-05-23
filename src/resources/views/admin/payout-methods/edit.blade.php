@extends('admin.layouts.app')

@section('title', 'Edit Payout Method')

@section('content')
<div class="max-w-3xl space-y-5">

    <form method="POST" action="{{ route('admin.payout-methods.update', $payoutMethod) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="flex items-start gap-3">
            <a href="{{ route('admin.payout-methods.index') }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit: {{ $payoutMethod->getLocaleName() }}</h1>
            </div>
        </div>

        @include('admin.payout-methods._form', ['method' => $payoutMethod])

        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                <x-heroicon-o-check class="h-4 w-4" />
                Save changes
            </button>
            <a href="{{ route('admin.payout-methods.index') }}"
               class="btn-secondary">
                <x-heroicon-o-x-mark class="h-4 w-4" />
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
