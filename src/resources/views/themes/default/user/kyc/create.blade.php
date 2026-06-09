@extends('theme::layouts.user')

@section('breadcrumb', __('Submit KYC'))

@section('content')
<div class="max-w-xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('user.kyc.index') }}"
           class="flex h-9 w-9 items-center justify-center rounded-xl glass border border-white/10 text-white/60 hover:text-white transition shrink-0">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="ud-page-title !mb-0">@lang('Submit KYC Documents')</h1>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-[13px] text-red-300">
            <ul class="space-y-0.5 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl glass overflow-hidden">
        <form method="POST" action="{{ route('user.kyc.store') }}" enctype="multipart/form-data" class="px-5 py-6 space-y-5">
            @csrf

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('Full legal name') <span class="text-red-400">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name') }}"
                       class="ud-input" required />
            </div>

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('Document type') <span class="text-red-400">*</span>
                </label>
                <select name="id_type" class="ud-input" required>
                    <option value="">@lang('— Select —')</option>
                    <option value="passport" @selected(old('id_type') === 'passport')>@lang('Passport')</option>
                    <option value="national_id" @selected(old('id_type') === 'national_id')>@lang('National ID')</option>
                    <option value="drivers_license" @selected(old('id_type') === 'drivers_license')>@lang("Driver's License")</option>
                </select>
            </div>

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('Document number') <span class="text-red-400">*</span>
                </label>
                <input type="text" name="id_number" value="{{ old('id_number') }}"
                       class="ud-input" required />
            </div>

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('Country') <span class="text-red-400">*</span>
                </label>
                <input type="text" name="country_code" value="{{ old('country_code', $user->country_code ?? '') }}"
                       maxlength="2" placeholder="e.g. US"
                       class="ud-input" required />
            </div>

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('ID — Front') <span class="text-red-400">*</span>
                </label>
                <input type="file" name="front_doc" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-[13px] text-white/50
                              file:mr-3 file:rounded-lg file:border-0
                              file:bg-white/10 file:px-3 file:py-2
                              file:text-[12px] file:font-medium file:text-white/80
                              hover:file:bg-white/15" required />
                <p class="mt-1.5 text-[11px] text-white/35">@lang('JPG, PNG, WebP or PDF, max 5MB')</p>
            </div>

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('ID — Back') <span class="text-[11px] text-white/35">(@lang('optional'))</span>
                </label>
                <input type="file" name="back_doc" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-[13px] text-white/50
                              file:mr-3 file:rounded-lg file:border-0
                              file:bg-white/10 file:px-3 file:py-2
                              file:text-[12px] file:font-medium file:text-white/80
                              hover:file:bg-white/15" />
            </div>

            <div>
                <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                    @lang('Selfie holding document') <span class="text-red-400">*</span>
                </label>
                <input type="file" name="selfie" accept=".jpg,.jpeg,.png,.webp"
                       class="block w-full text-[13px] text-white/50
                              file:mr-3 file:rounded-lg file:border-0
                              file:bg-white/10 file:px-3 file:py-2
                              file:text-[12px] file:font-medium file:text-white/80
                              hover:file:bg-white/15" required />
                <p class="mt-1.5 text-[11px] text-white/35">@lang('JPG, PNG or WebP, max 5MB')</p>
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary rounded-full px-5 py-3 text-[13px] font-semibold w-full">
                    @lang('Submit for review')
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
