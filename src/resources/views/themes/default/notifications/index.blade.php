@extends('theme::layouts.frontend')

@section('title', 'Notifications')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('user.notifications.read-all') }}">
                @csrf
                <button type="submit" class="text-sm text-brand-600 hover:text-brand-700">Mark all read</button>
            </form>
            <a href="{{ route('user.notifications.preferences') }}" class="text-sm text-gray-500 hover:text-gray-700">Preferences</a>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notif)
        <div class="bg-white rounded-xl border {{ $notif->isUnread() ? 'border-brand-200 bg-brand-50' : 'border-gray-200' }} p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $notif->title }}</p>
                    @if($notif->body)
                    <p class="text-sm text-gray-600 mt-0.5">{{ $notif->body }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if($notif->url)
                <a href="{{ $notif->url }}" class="text-xs text-brand-600 hover:text-brand-700 flex-shrink-0">View →</a>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400">No notifications yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
@endsection
