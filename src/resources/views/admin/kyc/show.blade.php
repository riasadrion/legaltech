@extends('admin.layouts.app')

@section('title', 'KYC Review')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kyc.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 shadow-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">KYC Submission #{{ $submission->id }}</h1>
        @php
            $sc = match($submission->status) {
                'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                default    => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
            };
        @endphp
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst($submission->status) }}</span>
    </div>

    {{-- User --}}
    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">User</h2>
        </div>
        <div class="px-5 py-4 space-y-1 text-sm text-gray-700 dark:text-slate-300">
            <p><span class="font-medium text-gray-900 dark:text-white">Name:</span> {{ $submission->user->name }}</p>
            <p><span class="font-medium text-gray-900 dark:text-white">Email:</span> {{ $submission->user->email }}</p>
            <p><span class="font-medium text-gray-900 dark:text-white">Submitted:</span> {{ formatDate($submission->created_at) }}</p>
        </div>
    </div>

    {{-- Submitted details --}}
    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Submitted details</h2>
        </div>
        <dl class="divide-y divide-gray-100 dark:divide-slate-700 px-5 text-sm">
            @foreach($submission->data as $key => $value)
            <div class="flex justify-between py-3">
                <dt class="text-gray-500 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                <dd class="font-medium text-gray-900 dark:text-white">{{ $value }}</dd>
            </div>
            @endforeach
        </dl>
    </div>

    {{-- Documents --}}
    @if(!empty($submission->documents))
    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 dark:border-slate-700 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Documents</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 px-5 py-5 sm:grid-cols-2">
            @foreach($submission->documents as $index => $doc)
            <div class="rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 p-3 text-sm">
                <p class="mb-2 font-medium text-gray-700 dark:text-slate-300 capitalize">{{ str_replace('_', ' ', $doc['field']) }}</p>
                <a href="{{ route('admin.kyc.document', [$submission, $index]) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                    <x-heroicon-o-eye class="h-3.5 w-3.5" />
                    View document
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Actions --}}
    @if($submission->isPending())
    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm p-5">
        <div class="flex gap-3" x-data="{ showReject: false }">

            <form method="POST" action="{{ route('admin.kyc.approve', $submission) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors">
                    <x-heroicon-o-check-circle class="h-4 w-4" />
                    Approve KYC
                </button>
            </form>

            <div class="flex-1 space-y-2">
                <button type="button" x-show="!showReject" @click="showReject = true"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-red-300 dark:border-red-800 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 transition-colors">
                    <x-heroicon-o-x-circle class="h-4 w-4" />
                    Reject
                </button>

                <form method="POST" action="{{ route('admin.kyc.reject', $submission) }}" x-show="showReject" class="space-y-2">
                    @csrf
                    <textarea name="reason" rows="3"
                              placeholder="Rejection reason (required)"
                              class="w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400 transition-colors"
                              required></textarea>
                    <div class="flex gap-2">
                        <button type="button" @click="showReject = false"
                                class="flex-1 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            Confirm rejection
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @elseif($submission->reviewer)
    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm p-5">
        <p class="text-sm text-gray-500 dark:text-slate-400">
            Reviewed by <strong class="text-gray-700 dark:text-slate-200">{{ $submission->reviewer->name }}</strong>
            on {{ formatDate($submission->reviewed_at) }}.
            @if($submission->isRejected() && $submission->rejection_reason)
                <span class="block mt-1">Reason: {{ $submission->rejection_reason }}</span>
            @endif
        </p>
    </div>
    @endif

</div>
@endsection
