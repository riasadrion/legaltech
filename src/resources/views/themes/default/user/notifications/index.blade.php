@extends('theme::layouts.user')

@section('breadcrumb', __('Notifications'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="ud-page-title">@lang('Notifications')</h1>
            <p class="ud-page-sub">@lang('Your activity and system notifications.')</p>
        </div>
        @if($notifications->isNotEmpty())
        <form method="POST" action="{{ route('user.notifications.read-all') }}">
            @csrf
            <button type="submit" class="ud-btn-ghost text-xs">
                <x-heroicon-o-check-circle class="h-4 w-4" />
                @lang('Mark all read')
            </button>
        </form>
        @endif
    </div>

    <div class="ud-card">
        @if($notifications->isEmpty())
            <div class="ud-empty py-16">
                <x-heroicon-o-bell class="h-12 w-12" />
                <div class="ud-empty-title">@lang('No notifications yet')</div>
                <div class="ud-empty-body">{{ __('You\'re all caught up. New activity will appear here.') }}</div>
            </div>
        @else
            <div class="divide-y divide-white/6">
                @foreach($notifications as $n)
                <div class="flex items-start gap-4 px-5 py-4 {{ $n->read_at ? 'opacity-60' : '' }} hover:bg-white/3 transition-colors">

                    <div class="mt-1.5 shrink-0">
                        @if(!$n->read_at)
                            <span class="block h-2 w-2 rounded-full bg-primary"></span>
                        @else
                            <span class="block h-2 w-2 rounded-full bg-white/15"></span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white leading-snug">{{ $n->title }}</p>
                        @if($n->body)
                            <p class="text-xs text-white/50 mt-0.5 leading-relaxed">{{ $n->body }}</p>
                        @endif
                        <p class="text-xs text-white/30 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                    </div>

                    @if($n->url)
                    <a href="{{ $n->url }}" class="shrink-0 text-xs text-primary/80 hover:text-primary whitespace-nowrap">
                        @lang('View') →
                    </a>
                    @endif
                </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
            <div class="border-t border-white/8 px-5 py-4 text-xs text-white/60">
                {{ $notifications->links() }}
            </div>
            @endif
        @endif
    </div>

</div>
@endsection
