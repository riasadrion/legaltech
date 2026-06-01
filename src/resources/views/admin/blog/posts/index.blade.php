@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">@lang('Blog Posts')</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $posts->total() }} @lang('posts')</p>
        </div>
        <a href="{{ route('admin.blog.posts.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            <x-heroicon-o-plus class="h-4 w-4" />
            @lang('New post')
        </a>
    </div>


    <x-status-pills :items="$statusPills" :current="request('status', '')" />

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Title')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Category')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Published')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900" data-label="@lang('Title')">{{ $post->getLocalTitle() }}</td>
                    <td class="px-4 py-3 text-gray-500" data-label="@lang('Category')">{{ $post->category?->getLocalName() ?? '—' }}</td>
                    <td class="px-4 py-3" data-label="@lang('Status')">
                        @if($post->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">@lang('Published')</span>
                        @elseif($post->status === 'scheduled')
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">@lang('Scheduled')</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">@lang('Draft')</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs" data-label="@lang('Published')">
                        {{ $post->published_at?->format('Y-m-d H:i') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.blog.posts.edit', $post) }}"
                               class="text-gray-400 hover:text-brand-600">
                                <x-heroicon-o-pencil class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('admin.blog.posts.destroy', $post) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" data-confirm="@lang('Delete this post?')"
                                        class="text-gray-400 hover:text-red-500">
                                    <x-heroicon-o-trash class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No posts found.')</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($posts->hasPages())
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-5 py-3 text-xs text-gray-500">
            <span>{{ __('Showing :first–:last of :total', ['first' => $posts->firstItem(), 'last' => $posts->lastItem(), 'total' => $posts->total()]) }}</span>
            <x-pagination :paginator="$posts" />
        </div>
        @endif
    </div>
</div>
@endsection
