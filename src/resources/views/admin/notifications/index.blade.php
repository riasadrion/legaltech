@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl space-y-6">
    <x-page-header title="Notifications">
        <x-slot name="actions">
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                <button type="submit" class="text-sm text-brand-600 hover:underline">Mark all as read</button>
            </form>
        </x-slot>
    </x-page-header>

    <div class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
        @forelse($notifications as $notification)
        <div class="flex items-start gap-4 px-5 py-4 {{ $notification->isUnread() ? 'bg-brand-50' : '' }}">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                @if($notification->body)
                    <p class="mt-0.5 text-sm text-gray-500">{{ $notification->body }}</p>
                @endif
                <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-3">
                @if($notification->url)
                    <a href="{{ $notification->url }}" class="text-xs text-brand-600 hover:underline">View</a>
                @endif
                @if($notification->isUnread())
                <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">Mark read</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-sm text-gray-400">No notifications.</div>
        @endforelse
    </div>

    <x-pagination :paginator="$notifications" />
</div>
@endsection
