@extends('theme::layouts.frontend')

@section('title', 'Notification Preferences')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notification Preferences</h1>
        <a href="{{ route('user.notifications.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
    </div>

    <form method="POST" action="{{ route('user.notifications.preferences.update') }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Notification</th>
                        @foreach(['email' => 'Email', 'sms' => 'SMS', 'in_app' => 'In-App'] as $ch => $label)
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($templates as $template)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900">{{ $template->name }}</div>
                            @if($template->description)
                            <div class="text-xs text-gray-400">{{ $template->description }}</div>
                            @endif
                        </td>
                        @foreach(['email', 'sms', 'in_app'] as $ch)
                        <td class="text-center px-4 py-3">
                            @if(in_array($ch, $template->channels ?? []))
                            @php $key = $template->key . '.' . $ch; $enabled = !isset($prefs[$key]) || $prefs[$key]->enabled; @endphp
                            <input type="checkbox"
                                name="prefs[{{ $template->key }}][{{ $ch }}]"
                                value="1"
                                {{ $enabled ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-600">
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No notifications available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Save Preferences</button>
        </div>
    </form>
</div>
@endsection
