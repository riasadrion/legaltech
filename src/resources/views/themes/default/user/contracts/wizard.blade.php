@extends('theme::layouts.user')

@section('breadcrumb', __('Fill Details'))

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-start gap-3">
        <a href="{{ route('user.contracts.templates', $template->category_id) }}"
           class="flex items-center justify-center h-9 w-9 rounded-xl glass border border-white/10 text-white/60 hover:text-white transition shrink-0 mt-1">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="ud-page-title !mb-0">{{ $template->name }}</h1>
            <p class="text-[12px] text-white/45 mt-1">
                {{ $template->description }}
                @if($template->description) · @endif
                <span class="text-lime/80">{{ $template->credits_cost }} @lang('credit(s)')</span>
            </p>
        </div>
    </div>

    @include('theme::user.contracts._stepper', ['step' => 3])

    <form method="POST" action="{{ route('user.contracts.preview') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="template_id" value="{{ $template->id }}">

        @if($errors->any())
            <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-[13px] text-red-300">
                <ul class="space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php $wizard = session('contract.wizard'); @endphp

        @foreach($groups as $groupName => $fields)
            <div class="rounded-2xl glass overflow-hidden">
                <div class="px-5 py-3 border-b border-white/8">
                    <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">{{ $groupName }}</h2>
                </div>
                <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($fields as $field)
                        @php
                            $oldVal = old("fields.{$field->key}", $wizard['fields'][$field->key] ?? '');
                            $colSpan = in_array($field->type, ['textarea']) ? 'md:col-span-2' : '';
                        @endphp
                        <div class="{{ $colSpan }}">
                            <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                                {{ $field->label }}
                                @if($field->required)<span class="text-red-400">*</span>@endif
                            </label>

                            @if($field->type === 'textarea')
                                <textarea name="fields[{{ $field->key }}]" rows="3"
                                          placeholder="{{ $field->placeholder }}"
                                          {{ $field->required ? 'required' : '' }}
                                          class="ud-input">{{ $oldVal }}</textarea>

                            @elseif($field->type === 'select')
                                <select name="fields[{{ $field->key }}]"
                                        {{ $field->required ? 'required' : '' }}
                                        class="ud-input">
                                    <option value="">@lang('Select…')</option>
                                    @foreach($field->options ?? [] as $opt)
                                        <option value="{{ $opt }}" {{ $oldVal === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>

                            @elseif($field->type === 'date')
                                <input type="text"
                                       name="fields[{{ $field->key }}]"
                                       value="{{ $oldVal }}"
                                       placeholder="{{ $field->placeholder ?: 'YYYY-MM-DD' }}"
                                       {{ $field->required ? 'required' : '' }}
                                       class="ud-input flatpickr-date"
                                       autocomplete="off" />

                            @else
                                <input type="{{ $field->type }}"
                                       name="fields[{{ $field->key }}]"
                                       value="{{ $oldVal }}"
                                       placeholder="{{ $field->placeholder }}"
                                       {{ $field->required ? 'required' : '' }}
                                       class="ud-input" />
                            @endif

                            @error("fields.{$field->key}")
                                <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach


        <div class="rounded-2xl glass overflow-hidden">
            <div class="px-5 py-3 border-b border-white/8 flex items-center justify-between">
                <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">@lang('Parties')</h2>
                <button type="button" id="add-party-btn"
                        class="text-[12px] text-lime hover:underline flex items-center gap-1">
                    <x-heroicon-o-plus class="h-3.5 w-3.5" />
                    @lang('Add party')
                </button>
            </div>
            <div class="px-5 py-5 space-y-4" id="parties-list">
                @php
                    $existingParties = old('parties', $wizard['parties'] ?? [[]]);
                    if (empty($existingParties)) $existingParties = [[]];
                @endphp
                @foreach($existingParties as $i => $p)
                    <div class="party-row space-y-3 {{ $i > 0 ? 'pt-4 border-t border-white/8' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="text-[11px] font-mono text-white/35 uppercase tracking-widest">@lang('Party') {{ $i + 1 }}</div>
                            @if($i > 0)
                                <button type="button" class="remove-party text-red-400 hover:text-red-300 text-[11px]">@lang('Remove')</button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="parties[{{ $i }}][name]" :placeholder="__('Full name')" required class="ud-input" value="{{ $p['name'] ?? '' }}" />
                            <input type="email" name="parties[{{ $i }}][email]" :placeholder="__('Email address')" required class="ud-input" value="{{ $p['email'] ?? '' }}" />
                        </div>
                        <select name="parties[{{ $i }}][role]" class="ud-input">
                            <option value="signer" {{ ($p['role'] ?? 'signer') !== 'viewer' ? 'selected' : '' }}>@lang('Signer')</option>
                            <option value="viewer" {{ ($p['role'] ?? '') === 'viewer' ? 'selected' : '' }}>@lang('Viewer only')</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('user.contracts.templates', $template->category_id) }}"
               class="text-[13px] text-white/50 hover:text-white">@lang('Back')</a>
            <button type="submit" class="btn-lime rounded-xl px-5 py-3 text-[13px] font-semibold flex items-center gap-2">
                @lang('Review & Preview')
                <x-heroicon-o-arrow-right class="h-4 w-4" />
            </button>
        </div>
    </form>

</div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/flatpickr.dark.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/static/js/flatpickr.min.js') }}"></script>
<script>
(function () {
    'use strict';
    if (window.flatpickr) {
        flatpickr('.flatpickr-date', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            allowInput: true,
        });
    }

    var idx = {{ count($existingParties) }};

    document.getElementById('add-party-btn').addEventListener('click', function () {
        var list = document.getElementById('parties-list');
        var html = '<div class="party-row space-y-3 pt-4 border-t border-white/8">'
            + '<div class="flex items-center justify-between">'
            + '<div class="text-[11px] font-mono text-white/35 uppercase tracking-widest">@lang('Party') ' + (idx + 1) + '</div>'
            + '<button type="button" class="remove-party text-red-400 hover:text-red-300 text-[11px]">@lang('Remove')</button>'
            + '</div>'
            + '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">'
            + '<input type="text" name="parties[' + idx + '][name]" :placeholder="__('Full name')" required class="ud-input" />'
            + '<input type="email" name="parties[' + idx + '][email]" :placeholder="__('Email address')" required class="ud-input" />'
            + '</div>'
            + '<select name="parties[' + idx + '][role]" class="ud-input">'
            + '<option value="signer">@lang('Signer')</option>'
            + '<option value="viewer">@lang('Viewer only')</option>'
            + '</select>'
            + '</div>';
        list.insertAdjacentHTML('beforeend', html);
        idx++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-party')) {
            e.target.closest('.party-row').remove();
        }
    });
})();
</script>
@endpush
