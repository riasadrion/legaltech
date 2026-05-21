@extends('theme::layouts.user')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Identity Verification (KYC)</h1>
    <p class="mt-1 text-sm text-gray-500">Required before you can request payouts.</p>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-5">
            @if($user->kyc_status === 'approved')
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                        <x-heroicon-s-check-circle class="h-6 w-6 text-green-600" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Verified</p>
                        <p class="text-sm text-gray-500">Your identity has been verified successfully.</p>
                    </div>
                </div>

            @elseif($user->kyc_status === 'pending' && $submission)
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100">
                        <x-heroicon-o-clock class="h-6 w-6 text-amber-600" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Under review</p>
                        <p class="text-sm text-gray-500">Your documents were submitted on {{ $submission->created_at->format('d M Y') }}. We'll notify you when reviewed.</p>
                    </div>
                </div>

            @elseif($user->kyc_status === 'rejected' && $submission)
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                        <x-heroicon-o-x-circle class="h-6 w-6 text-red-600" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Verification rejected</p>
                        @if($submission->rejection_reason)
                            <p class="text-sm text-gray-500">Reason: {{ $submission->rejection_reason }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('user.kyc.create') }}"
                   class="inline-flex rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    Re-submit documents
                </a>

            @else
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <x-heroicon-o-identification class="h-6 w-6 text-gray-500" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Not verified</p>
                        <p class="text-sm text-gray-500">Submit your identity documents to unlock payouts.</p>
                    </div>
                </div>
                <a href="{{ route('user.kyc.create') }}"
                   class="inline-flex rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    Start verification
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
