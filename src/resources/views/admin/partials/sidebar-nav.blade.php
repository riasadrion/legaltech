@php
    $navItems = \App\Domain\Admin\Navigation\AdminNavigation::forUser(auth('admin')->user());
@endphp

@foreach ($navItems as $item)
    @if (empty($item['children']))
        {{-- Leaf item --}}
        @php
            $routePrefix = isset($item['route']) ? \Illuminate\Support\Str::beforeLast($item['route'], '.') . '.*' : '';
            $isActive = $routePrefix && request()->routeIs($routePrefix);
        @endphp
        <a href="{{ isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#' }}"
           data-route="{{ isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '' }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ $isActive ? 'bg-brand-50 text-brand-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            @if (!empty($item['icon']))
                <x-dynamic-component :component="$item['icon']" class="w-5 h-5 flex-shrink-0 {{ $isActive ? 'text-brand-600' : 'text-gray-400' }}" />
            @endif
            <span>{{ $item['label'] }}</span>
        </a>
    @else
        {{-- Parent with children --}}
        @php
            $submenuId = 'submenu-' . $item['key'];
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
            <button type="button"
                    data-submenu-toggle="#{{ $submenuId }}"
                    class="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors {{ $isParentActive ? 'bg-brand-50 text-brand-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                @if (!empty($item['icon']))
                    <x-dynamic-component :component="$item['icon']" class="w-5 h-5 flex-shrink-0 {{ $isParentActive ? 'text-brand-600' : 'text-gray-400' }}" />
                @endif
                <span class="flex-1 text-start">{{ $item['label'] }}</span>
                <x-heroicon-o-chevron-down class="w-4 h-4 {{ $isParentActive ? 'text-brand-600' : 'text-gray-400' }} transition-transform" data-chevron />
            </button>
            <div id="{{ $submenuId }}" class="{{ $isParentActive ? '' : 'hidden' }} ms-8 mt-1 space-y-1">
                @foreach ($item['children'] as $child)
                    @php
                        $childRoutePrefix = isset($child['route']) ? \Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*' : '';
                        $isChildActive = $childRoutePrefix && request()->routeIs($childRoutePrefix);
                    @endphp
                    <a href="{{ isset($child['route']) && \Illuminate\Support\Facades\Route::has($child['route']) ? route($child['route']) : '#' }}"
                       data-route="{{ isset($child['route']) && \Illuminate\Support\Facades\Route::has($child['route']) ? route($child['route']) : '' }}"
                       class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors {{ $isChildActive ? 'bg-brand-50 text-brand-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $child['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
