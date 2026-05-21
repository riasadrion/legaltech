@extends('theme::layouts.user')

@section('content')
<div class="mx-auto max-w-lg px-4 sm:px-6 py-10">

    <div class="flex items-start gap-3 mb-6">
        <a href="{{ route('user.kyc.index') }}"
           class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <h1 class="text-xl font-bold text-gray-900">Submit KYC Documents</h1>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('user.kyc.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Full legal name <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}"
                       class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
                @error('full_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Document type <span class="text-red-500">*</span></label>
                <select name="id_type"
                        class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required>
                    <option value="">— Select —</option>
                    <option value="passport" @selected(old('id_type') === 'passport')>Passport</option>
                    <option value="national_id" @selected(old('id_type') === 'national_id')>National ID</option>
                    <option value="drivers_license" @selected(old('id_type') === 'drivers_license')>Driver's License</option>
                </select>
                @error('id_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Document number <span class="text-red-500">*</span></label>
                <input type="text" name="id_number" value="{{ old('id_number') }}"
                       class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
                @error('id_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Country <span class="text-red-500">*</span></label>
                <input type="text" name="country_code" value="{{ old('country_code', $user->country_code ?? '') }}"
                       maxlength="2" placeholder="e.g. US"
                       class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" required />
                @error('country_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">ID — Front <span class="text-red-500">*</span></label>
                <input type="file" name="front_doc" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100" required />
                <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP or PDF, max 5MB</p>
                @error('front_doc')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">ID — Back <span class="text-xs text-gray-400">(optional)</span></label>
                <input type="file" name="back_doc" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100" />
                @error('back_doc')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Selfie holding document <span class="text-red-500">*</span></label>
                <input type="file" name="selfie" accept=".jpg,.jpeg,.png,.webp"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100" required />
                <p class="mt-1 text-xs text-gray-400">JPG, PNG or WebP, max 5MB</p>
                @error('selfie')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            @error('error')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

            <button type="submit"
                    class="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                Submit for review
            </button>
        </form>
    </div>
</div>
@endsection
