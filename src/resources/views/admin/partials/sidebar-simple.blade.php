
<aside class="w-64 border-r border-gray-200 bg-gradient-to-b from-gray-50 to-white flex flex-col h-screen shadow-sm">

    <div class="h-16 flex items-center justify-start border-b border-gray-200 px-4">
        <a href="{{ route('admin.dashboard') }}" class="max-w-full">
            @php
                $logo = setting('site.logo');
            @endphp
            @if($logo)
                <img src="{{ upload_url($logo) }}" alt="{{ config('app.name') }}" class="h-10 object-contain">
            @else
                <span class="text-lg font-bold bg-gradient-to-r from-brand-600 to-brand-700 bg-clip-text text-transparent">
                    {{ config('app.name') }}
                </span>
            @endif
        </a>
    </div>

    <div class="overflow-y-auto overflow-x-hidden scrollbar-hide flex-1">

        <nav class="px-3 py-5 space-y-4">
            @php
                $navItems = \App\Domain\Admin\Navigation\AdminNavigation::forUser(auth('admin')->user());
                $currentRoute = request()->route()?->getName();
            @endphp

            @foreach($navItems as $item)
                @php
                    $hasRoute = isset($item['route']);
                    $itemRoute = $hasRoute ? route($item['route']) : '#';
                    $isActive = $hasRoute && request()->routeIs($item['route'] . '*');
                    $isParent = !empty($item['children'] ?? false);
                    $icon = $item['icon'] ?? 'heroicon-o-folder';
                @endphp

                @if($isParent)

                    @php
                        $isParentActive = false;
                        foreach($item['children'] as $child) {
                            if (isset($child['route'])) {
                                $routePrefix = \Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*';
                                if (request()->routeIs($routePrefix)) {
                                    $isParentActive = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <div>
                        <p class="px-2 mb-2 text-xs font-semibold uppercase tracking-wider {{ $isParentActive ? 'text-brand-600' : 'text-gray-400' }}">{{ $item['label'] }}</p>
                        <div class="space-y-1">
                            @foreach($item['children'] as $child)
                                @php
                                    $childRoute = isset($child['route']) ? route($child['route']) : '#';
                                    $childRoutePrefix = isset($child['route']) ? \Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*' : '';
                                    $childActive = $childRoutePrefix && request()->routeIs($childRoutePrefix);
                                    $childIcon = $child['icon'] ?? 'heroicon-o-folder';
                                @endphp
                                <a href="{{ $childRoute }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all {{ $childActive ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                    <x-dynamic-component :component="$childIcon" class="h-5 w-5 flex-shrink-0 {{ $childActive ? 'text-brand-600' : 'text-gray-400' }}" />
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else

                    <div class="space-y-1">
                        <a href="{{ $itemRoute }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all {{ $isActive ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            <x-dynamic-component :component="$icon" class="h-5 w-5 flex-shrink-0 {{ $isActive ? 'text-brand-600' : 'text-gray-400' }}" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </div>
                @endif
            @endforeach

        </nav>
    </div>


    <div class="border-t border-gray-200 bg-gradient-to-t from-gray-50 to-white p-4">
        <div class="flex items-center gap-3 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 p-3 border border-blue-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-600 text-white font-semibold flex-shrink-0 shadow-sm">
                {{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-gray-900">{{ auth('admin')->user()->name ?? 'Admin' }}</p>
                <p class="truncate text-xs text-gray-600">{{ auth('admin')->user()->email ?? 'admin@example.com' }}</p>
            </div>
        </div>
    </div>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide:hover {
            scrollbar-width: thin;
        }
        .scrollbar-hide:hover::-webkit-scrollbar {
            display: block;
            width: 6px;
        }
        .scrollbar-hide::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-hide::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        .scrollbar-hide::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</aside>
