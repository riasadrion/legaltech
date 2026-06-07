<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand-link hover:opacity-75 transition-opacity flex-1 min-w-0">
            @php
                $siteName = setting('site.name', config('app.name'));
                $siteLogo = setting('site.logo');
                $siteDarkLogo = setting('site.dark_logo');
            @endphp
            @if($siteLogo || $siteDarkLogo)
                @if($siteLogo)
                    <img src="{{ upload_url($siteLogo) }}" alt="{{ $siteName }}" class="sidebar-brand-logo sidebar-brand-logo-dark">
                @endif
                @if($siteDarkLogo)
                    <img src="{{ upload_url($siteDarkLogo) }}" alt="{{ $siteName }}" class="sidebar-brand-logo sidebar-brand-logo-light">
                @elseif($siteLogo)
                    <img src="{{ upload_url($siteLogo) }}" alt="{{ $siteName }}" class="sidebar-brand-logo sidebar-brand-logo-light">
                @endif
            @else
                <div class="sidebar-brand-icon">{{ strtoupper(substr($siteName, 0, 1)) }}</div>
                <span class="sidebar-brand-text">{{ $siteName }}</span>
            @endif
        </a>
        <button class="sidebar-toggle-btn" id="sidebar-toggle" title="Toggle sidebar">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </button>
    </div>

    <div class="sidebar-nav">
        @php $navItems = \App\Support\AdminNavigation::forUser(auth('admin')->user()); @endphp
        @foreach ($navItems as $item)
            @if (empty($item['children']))
                @php
                    $isActive = false;
                    if (isset($item['route'])) {
                        $parts = explode('.', $item['route']);
                        $isActive = count($parts) <= 2
                            ? request()->routeIs($item['route'])
                            : request()->routeIs(\Illuminate\Support\Str::beforeLast($item['route'], '.') . '.*');
                    }
                @endphp
                <div class="sidebar-section">
                    <a href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="sidebar-link {{ $isActive ? 'active' : '' }}"
                       data-label="{{ $item['label'] }}">
                        @if (!empty($item['icon']))
                            <x-dynamic-component :component="$item['icon']" class="w-4 h-4 flex-shrink-0 sidebar-link-icon" />
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                </div>
            @else
                @php
                    $isParentActive = false;
                    foreach($item['children'] as $child) {
                        if (isset($child['route'])) {
                            $parts = explode('.', $child['route']);
                            $match = count($parts) <= 2
                                ? request()->routeIs($child['route'])
                                : request()->routeIs(\Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*');
                            if ($match) { $isParentActive = true; break; }
                        }
                    }
                @endphp
                <div class="sidebar-section">
                    <div class="sidebar-section-label">{{ $item['label'] }}</div>
                    @foreach ($item['children'] as $child)
                        @php
                            $isChildActive = false;
                            if (isset($child['route'])) {
                                $parts = explode('.', $child['route']);
                                $isChildActive = count($parts) <= 2
                                    ? request()->routeIs($child['route'])
                                    : request()->routeIs(\Illuminate\Support\Str::beforeLast($child['route'], '.') . '.*');
                            }
                        @endphp
                        <a href="{{ isset($child['route']) && Route::has($child['route']) ? route($child['route']) : '#' }}"
                           class="sidebar-link {{ $isChildActive ? 'active' : '' }}"
                           data-label="{{ $child['label'] }}">
                            @if (!empty($child['icon']))
                                <x-dynamic-component :component="$child['icon']" class="w-4 h-4 flex-shrink-0 sidebar-link-icon" />
                            @endif
                            <span>{{ $child['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>

    <div class="sidebar-user">
        <div class="sidebar-user-inner">
            <div class="sidebar-user-avatar">{{ strtoupper(substr(auth('admin')->user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="sidebar-user-name">{{ auth('admin')->user()?->name }}</div>
                <div class="sidebar-user-role">Admin</div>
            </div>
        </div>
    </div>
</aside>
