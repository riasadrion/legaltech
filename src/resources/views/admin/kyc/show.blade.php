@extends('admin.layouts.app')

@section('title', 'KYC Review')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.kyc.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900">KYC Submission #{{ $submission->id }}</h1>
        @php $sc = match($submission->status) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-amber-100 text-amber-700' }; @endphp
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sc }}">{{ ucfirst($submission->status) }}</span>
    </div>

    {{-- User info --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900">User</h2>
        </div>
        <div class="px-5 py-4 text-sm text-gray-700">
            <p><span class="font-medium">Name:</span> {{ $submission->user->name }}</p>
            <p class="mt-1"><span class="font-medium">Email:</span> {{ $submission->user->email }}</p>
            <p class="mt-1"><span class="font-medium">Submitted:</span> {{ formatDate($submission->created_at) }}</p>
        </div>
    </div>

    {{-- Submitted data --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900">Submitted details</h2>
        </div>
        <dl class="divide-y divide-gray-100 px-5 text-sm">
            @foreach($submission->data as $key => $value)
            <div class="flex justify-between py-2.5">
                <dt class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                <dd class="font-medium text-gray-900">{{ $value }}</dd>
            </div>
            @endforeach
        </dl>
    </div>

    {{-- Documents --}}
    @if(!empty($submission->documents))
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900">Documents</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 px-5 py-5 sm:grid-cols-2">
            @foreach($submission->documents as $index => $doc)
            <div class="rounded-lg border border-gray-200 p-3 text-sm">
                <p class="mb-2 font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $doc['field']) }}</p>
                <a href="{{ route('admin.kyc.document', [$submission, $index]) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
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
    <div class="flex gap-3">
        <form method="POST" action="{{ route('admin.kyc.approve', $submission) }}" class="flex-1">
            @csrf
            <button type="submit"
                    class="w-full rounded-lg bg-green-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                Approve KYC
            </button>
        </form>

        <form method="POST" action="{{ route('admin.kyc.reject', $submission) }}" class="flex-1" x-data="{ show: false }">
            @csrf
            <div x-show="!show">
                <button type="button" @click="show = true"
                        class="w-full rounded-lg border border-red-300 bg-white py-2.5 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-50">
                    Reject
                </button>
            </div>
            <div x-show="show" class="space-y-2">
                <textarea name="reason" rows="2" placeholder="Rejection reason (required)"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400" required></textarea>
                <button type="submit"
                        class="w-full rounded-lg bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    Confirm rejection
                </button>
            </div>
        </form>
    </div>
    @elseif($submission->reviewer)
    <p class="text-sm text-gray-500">
        Reviewed by <strong>{{ $submission->reviewer->name }}</strong> on {{ formatDate($submission->reviewed_at) }}.
        @if($submission->isRejected() && $submission->rejection_reason)
            Reason: {{ $submission->rejection_reason }}
        @endif
    </p>
    @endif
</div>
@endsection
