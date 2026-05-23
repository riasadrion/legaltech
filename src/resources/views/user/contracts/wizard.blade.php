@extends('theme::layouts.user')

@section('breadcrumb', __('Generate Contract'))

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('user.contracts.create') }}"
           class="flex items-center justify-center h-8 w-8 rounded-lg glass border border-white/10 text-white/60 hover:text-white transition shrink-0 mt-1">
            <x-heroicon-o-arrow-left class="h-4 w-4" />
        </a>
        <div>
            <h1 class="ud-page-title !mb-0">{{ $template->name }}</h1>
            <p class="text-[12px] text-white/45 mt-0.5">
                {{ $template->description }}
                · <span class="text-lime/80">{{ $template->credits_cost }} {{ __('credit(s)') }}</span>
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('user.contracts.store') }}" class="space-y-5">
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

        {{-- Dynamic template fields grouped --}}
        @foreach($groups as $groupName => $fields)
        <div class="rounded-2xl glass overflow-hidden">
            <div class="px-5 py-3 border-b border-white/8">
                <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">{{ $groupName }}</h2>
            </div>
            <div class="px-5 py-5 space-y-4">
                @foreach($fields as $field)
                <div>
                    <label class="block text-[12px] font-medium text-white/60 mb-1.5">
                        {{ $field->label }}
                        @if($field->required)<span class="text-red-400">*</span>@endif
                    </label>

                    @if($field->type === 'textarea')
                        <textarea name="fields[{{ $field->key }}]" rows="3"
                                  placeholder="{{ $field->placeholder }}"
                                  {{ $field->required ? 'required' : '' }}
                                  class="ud-input">{{ old("fields.{$field->key}") }}</textarea>

                    @elseif($field->type === 'select')
                        <select name="fields[{{ $field->key }}]"
                                {{ $field->required ? 'required' : '' }}
                                class="ud-input">
                            <option value="">{{ __('Select…') }}</option>
                            @foreach($field->options ?? [] as $opt)
                                <option value="{{ $opt }}" {{ old("fields.{$field->key}") === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>

                    @else
                        <input type="{{ $field->type }}"
                               name="fields[{{ $field->key }}]"
                               value="{{ old("fields.{$field->key}") }}"
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

        {{-- Parties section --}}
        <div class="rounded-2xl glass overflow-hidden">
            <div class="px-5 py-3 border-b border-white/8 flex items-center justify-between">
                <h2 class="text-[12px] font-mono uppercase tracking-widest text-white/50">{{ __('Parties') }}</h2>
                <button type="button" id="add-party-btn"
                        class="text-[12px] text-lime hover:underline flex items-center gap-1">
                    <x-heroicon-o-plus class="h-3.5 w-3.5" />
                    {{ __('Add party') }}
                </button>
            </div>
            <div class="px-5 py-5 space-y-4" id="parties-list">
                <div class="party-row space-y-3">
                    <div class="text-[11px] font-mono text-white/35 uppercase tracking-widest">{{ __('Party 1') }}</div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="parties[0][name]" placeholder="{{ __('Full name') }}" required class="ud-input" value="{{ old('parties.0.name') }}" />
                        <input type="email" name="parties[0][email]" placeholder="{{ __('Email address') }}" required class="ud-input" value="{{ old('parties.0.email') }}" />
                    </div>
                    <select name="parties[0][role]" class="ud-input">
                        <option value="signer" {{ old('parties.0.role') !== 'viewer' ? 'selected' : '' }}>{{ __('Signer') }}</option>
                        <option value="viewer" {{ old('parties.0.role') === 'viewer' ? 'selected' : '' }}>{{ __('Viewer only') }}</option>
                    </select>
                </div>
            </div>
        </div>

        @error('parties')
            <p class="text-[12px] text-red-400">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="btn-lime rounded-xl w-full py-3 text-[13px] font-semibold flex items-center justify-center gap-2">
            <x-heroicon-o-sparkles class="h-4 w-4" />
            {{ __('Generate Contract') }}
        </button>
    </form>

</div>
@endsection

@push('scripts')
<script>
(function () {
    var idx = 1;
    document.getElementById('add-party-btn').addEventListener('click', function () {
        var list = document.getElementById('parties-list');
        var html = '<div class="party-row space-y-3 pt-4 border-t border-white/8">'
            + '<div class="flex items-center justify-between">'
            + '<div class="text-[11px] font-mono text-white/35 uppercase tracking-widest">{{ __('Party') }} ' + (idx + 1) + '</div>'
            + '<button type="button" class="remove-party text-red-400 hover:text-red-300 text-[11px]">{{ __('Remove') }}</button>'
            + '</div>'
            + '<div class="grid grid-cols-2 gap-3">'
            + '<input type="text" name="parties[' + idx + '][name]" placeholder="{{ __('Full name') }}" required class="ud-input" />'
            + '<input type="email" name="parties[' + idx + '][email]" placeholder="{{ __('Email address') }}" required class="ud-input" />'
            + '</div>'
            + '<select name="parties[' + idx + '][role]" class="ud-input">'
            + '<option value="signer">{{ __('Signer') }}</option>'
            + '<option value="viewer">{{ __('Viewer only') }}</option>'
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
