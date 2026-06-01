@extends('admin.layouts.app')

@section('title', __('Edit Template: :name', ['name' => $template->name]))

@section('content')
<x-page-header :title="__('Edit: :name', ['name' => $template->name])" back="{{ route('admin.notifications.templates.index') }}" />

@php $languages = \App\Models\Language::all(); @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.notifications.templates.update', $template) }}">
                @csrf @method('PUT')

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Channels')</label>
                    <div class="flex gap-4">
                        @foreach(['email' => __('Email'), 'sms' => __('SMS'), 'in_app' => __('In-App')] as $ch => $label)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="channels[]" value="{{ $ch }}"
                                {{ in_array($ch, $template->channels ?? []) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-600">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-5">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" value="1"
                            {{ $template->is_active ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-600">
                        @lang('Active')
                    </label>
                </div>


                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">@lang('Subject')</label>
                    <x-translatable-input name="subject" :value="$template->subject ?? []" />
                </div>


                <div class="mb-5 translatable-input space-y-2">
                    <label class="block text-sm font-medium text-gray-700">@lang('Email Body')</label>
                    <div class="flex gap-1.5">
                        @foreach($languages as $lang)
                        <button type="button"
                                class="locale-tab rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors
                                       {{ $loop->first ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600' }}"
                                data-locale="{{ $lang->code }}"
                                data-target="email_body">
                            {{ strtoupper($lang->code) }}
                        </button>
                        @endforeach
                    </div>
                    @foreach($languages as $lang)
                    <div class="locale-panel {{ $loop->first ? '' : 'hidden' }}" data-locale="{{ $lang->code }}" data-field="email_body">
                        <input type="hidden" name="email_body[{{ $lang->code }}]" id="email_body_{{ $lang->code }}" value="{{ ($template->email_body ?? [])[$lang->code] ?? '' }}">
                        <div class="prose-editor-init" data-input="email_body_{{ $lang->code }}"></div>
                    </div>
                    @endforeach
                </div>


                <div class="mb-5 translatable-input space-y-2">
                    <label class="block text-sm font-medium text-gray-700">@lang('SMS Body')</label>
                    <div class="flex gap-1.5">
                        @foreach($languages as $lang)
                        <button type="button"
                                class="locale-tab rounded px-1.5 py-0.5 text-[11px] font-medium transition-colors
                                       {{ $loop->first ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600' }}"
                                data-locale="{{ $lang->code }}"
                                data-target="sms_body">
                            {{ strtoupper($lang->code) }}
                        </button>
                        @endforeach
                    </div>
                    @foreach($languages as $lang)
                    <div class="locale-panel {{ $loop->first ? '' : 'hidden' }}" data-locale="{{ $lang->code }}" data-field="sms_body">
                        <textarea name="sms_body[{{ $lang->code }}]" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500">{{ ($template->sms_body ?? [])[$lang->code] ?? '' }}</textarea>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        <x-heroicon-o-check class="h-4 w-4" />
                        @lang('Save Template')
                    </button>
                    <a href="{{ route('admin.notifications.templates.index') }}" class="btn-secondary">
                        <x-heroicon-o-x-mark class="h-4 w-4" />
                        @lang('Cancel')
                    </a>
                </div>
            </form>
        </div>


        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">@lang('Send Test Email')</h3>
            <form method="POST" action="{{ route('admin.notifications.templates.test', $template) }}" class="flex gap-3">
                @csrf
                <input type="email" name="test_email" placeholder="you@example.com" required
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-brand-500 focus:border-brand-500">
                <button type="submit" class="btn-secondary">@lang('Send Test')</button>
            </form>
        </div>
    </div>


    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">@lang('Available Variables')</h3>
            <ul class="space-y-1.5">
                @foreach($template->variables ?? [] as $var)
                <li>
                    <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded cursor-pointer select-all"
                        title="{{ $var['description'] ?? '' }}">{{ '{'.'{'.$var['key'].'}'.'}' }}</code>
                    @if(!empty($var['description']))
                    <span class="text-xs text-gray-400 ml-1">{{ $var['description'] }}</span>
                    @endif
                </li>
                @endforeach
                <li><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ '{'.'{user.name}'.'}' }}</code> <span class="text-xs text-gray-400">{{ __('User\'s name') }}</span></li>
                <li><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ '{'.'{user.email}'.'}' }}</code> <span class="text-xs text-gray-400">{{ __('User\'s email') }}</span></li>
                <li><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ '{'.'{app.name}'.'}' }}</code> <span class="text-xs text-gray-400">@lang('App name')</span></li>
                <li><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ '{'.'{app.url}'.'}' }}</code> <span class="text-xs text-gray-400">@lang('App URL')</span></li>
            </ul>
        </div>

        @if($versions->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">@lang('Version History')</h3>
            <ul class="space-y-2">
                @foreach($versions as $version)
                <li class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">v{{ $version->version }} &mdash; {{ $version->created_at->diffForHumans() }}</span>
                    <form method="POST" action="{{ route('admin.notifications.templates.rollback', [$template, $version]) }}">
                        @csrf
                        <button type="submit" class="text-xs text-brand-600 hover:text-brand-700"
                            data-confirm="{{ __('Roll back to v:version?', ['version' => $version->version]) }}"
                            :data-confirm-title="__('Restore version')">@lang('Restore')</button>
                    </form>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/editor.css') }}">
<script src="{{ asset('assets/static/js/editor.js') }}"></script>
@endpush

@push('scripts')
<script>

    'use strict';
    document.querySelectorAll('.prose-editor-init').forEach(function (el) {
        ProseEditor.init(el)
    })
})
</script>
@endpush
