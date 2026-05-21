@php $admin = auth('admin')->user(); @endphp
@if ($admin)
<div class="flex items-center gap-3 px-1 py-1">
    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0">
        <span class="text-xs font-semibold text-brand-700">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 truncate">{{ $admin->name }}</p>
        <p class="text-xs text-gray-500 truncate">{{ $admin->email }}</p>
    </div>
</div>
@endif
