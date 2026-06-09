@extends('theme::layouts.user')

@section('breadcrumb', __('Identity Verification'))

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="ud-page-title">@lang('Identity Verification (KYC)')</h1>
        <p class="ud-page-sub">@lang('Verify your identity to access all platform features.')</p>
    </div>

    <div class="ud-card">
        @if($user->kyc_status === 'approved')
            <div class="flex items-center gap-4 p-6">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15">
                    <x-heroicon-s-check-circle class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <p class="font-semibold text-white">@lang('Verified')</p>
                    <p class="text-sm text-white/50">@lang('Your identity has been verified successfully.')</p>
                </div>
                <span class="ml-auto ud-badge ud-badge-green">@lang('Approved')</span>
            </div>

        @elseif($user->kyc_status === 'pending' && $submission)
            <div class="flex items-center gap-4 p-6">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-400/15">
                    <x-heroicon-o-clock class="h-6 w-6 text-amber-400" />
                </div>
                <div>
                    <p class="font-semibold text-white">@lang('Under Review')</p>
                    <p class="text-sm text-white/50">{{ __('Submitted on :date. We\'ll notify you when reviewed.', ['date' => $submission->created_at->format('d M Y')]) }}</p>
                </div>
                <span class="ml-auto ud-badge ud-badge-amber">@lang('Pending')</span>
            </div>

        @elseif($user->kyc_status === 'rejected' && $submission)
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-500/15">
                        <x-heroicon-o-x-circle class="h-6 w-6 text-red-400" />
                    </div>
                    <div>
                        <p class="font-semibold text-white">@lang('Verification Rejected')</p>
                        @if($submission->rejection_reason)
                            <p class="text-sm text-white/50">{{ __('Reason: :reason', ['reason' => $submission->rejection_reason]) }}</p>
                        @endif
                    </div>
                    <span class="ml-auto ud-badge ud-badge-red">@lang('Rejected')</span>
                </div>
                <a href="{{ route('user.kyc.create') }}" class="ud-btn-primary inline-flex">
                    @lang('Re-submit Documents')
                </a>
            </div>

        @else
            <div class="p-6 space-y-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/8">
                        <x-heroicon-o-identification class="h-6 w-6 text-white/50" />
                    </div>
                    <div>
                        <p class="font-semibold text-white">@lang('Not Verified')</p>
                        <p class="text-sm text-white/50">@lang('Submit your identity documents to unlock full access.')</p>
                    </div>
                    <span class="ml-auto ud-badge ud-badge-gray">@lang('Unverified')</span>
                </div>


                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach([
                        ['icon' => 'heroicon-o-document-text', 'step' => '1', 'title' => __('Submit Documents'), 'desc' => __('Upload your ID or passport')],
                        ['icon' => 'heroicon-o-magnifying-glass', 'step' => '2', 'title' => __('Review'), 'desc' => __('We verify within 1–3 days')],
                        ['icon' => 'heroicon-o-check-badge', 'step' => '3', 'title' => __('Get Verified'), 'desc' => __('Full access unlocked')],
                    ] as $s)
                    <div class="rounded-lg bg-white/4 border border-white/8 px-4 py-3 flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary text-xs font-bold">{{ $s['step'] }}</div>
                        <div>
                            <div class="text-sm font-medium text-white">{{ $s['title'] }}</div>
                            <div class="text-xs text-white/60 mt-0.5">{{ $s['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('user.kyc.create') }}" class="ud-btn-primary inline-flex">
                    @lang('Start Verification')
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
