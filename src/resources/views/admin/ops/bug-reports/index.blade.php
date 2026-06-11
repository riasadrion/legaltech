@extends('admin.layouts.app')

@section('title', 'Bug Reports')

@php
    // Central bug-report intake endpoint on cystall.com. Replace with the real URL.
    $bugReportEndpoint = 'https://cystall.com/api/bug-reports';
@endphp

@section('content')
<x-admin::page-header title="Bug Reports" subtitle="Report an issue to the support team." />

<div class="space-y-5">

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Report an Issue</h2>
        <form id="bug-report-form" data-no-page-loader data-endpoint="{{ $bugReportEndpoint }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                    <select name="severity"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="8" required maxlength="5000"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>
            </div>
            <button type="submit" id="bug-report-submit"
                class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 disabled:opacity-60">
                Submit Report
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    'use strict';

    var $form = $('#bug-report-form');
    if (!$form.length) return;

    var $btn = $('#bug-report-submit');

    $form.on('submit', function (e) {
        e.preventDefault();

        var params = {
            title:       $form.find('[name="title"]').val().trim(),
            description: $form.find('[name="description"]').val().trim(),
            severity:    $form.find('[name="severity"]').val(),
            url:         window.location.href,
            project:     @json(config('app.name')),
            app_url:     window.location.origin,
            reported_at: new Date().toISOString()
        };

        var endpoint = $form.data('endpoint') + '?' + $.param(params);

        $btn.prop('disabled', true);
        var original = $btn.text();
        $btn.text('Sending…');

        $.ajax({
            url: endpoint,
            method: 'GET',
            dataType: 'json',
            headers: { 'Accept': 'application/json' },
        }).done(function (data) {
            if (data && data.ok === false) {
                if (window.toast) window.toast('Could not send the report. Please try again.', 'error');
                return;
            }
            if (window.toast) window.toast('Report sent.', 'success');
            $form.trigger('reset');
        }).fail(function (xhr) {
            if (xhr.status === 429) {
                if (window.toast) window.toast('Too many requests.', 'error');
                return;
            }
            if (window.toast) window.toast('Could not send the report. Please try again.', 'error');
        }).always(function () {
            $btn.prop('disabled', false).text(original);
        });
    });
});
</script>
@endpush
@endsection
