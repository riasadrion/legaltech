@extends('admin.layouts.app')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">@lang('Payment Gateways')</h1>
        <p class="mt-0.5 text-sm text-gray-500">@lang('Configure and enable payment methods for payments.')</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Gateway')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Status')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Min / Max')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">@lang('Fee')</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($registeredDrivers as $driverKey)
                @php $gw = $gateways->get($driverKey); $driver = $manager->driver($driverKey); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3" data-label="@lang('Gateway')">
                        <div class="flex items-center gap-3">
                            @if($gw?->logoUrl())
                            <img src="{{ $gw->logoUrl() }}" alt="{{ $gw->name }}" class="h-10 w-10 rounded-lg object-contain flex-shrink-0">
                            @else
                            <div class="h-10 w-10 rounded-lg bg-gray-100 flex-shrink-0"></div>
                            @endif
                            <div>
                                <div class="font-semibold text-gray-900">{{ $gw?->name ?? $driver->name() }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $driverKey }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3" data-label="@lang('Status')">
                        @if($gw?->enabled)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">@lang('Enabled')</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">@lang('Disabled')</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600" data-label="@lang('Min / Max')">
                        @if($gw)
                            {{ $gw->formatAmount($gw->min_amount) }} – {{ $gw->formatAmount($gw->max_amount) }}
                        @else
                            <span class="text-gray-400">@lang('Not configured')</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600" data-label="@lang('Fee')">
                        @if($gw)
                            @if($gw->fee_flat > 0 || $gw->fee_percent > 0)
                                @if($gw->fee_flat > 0) {{ $gw->formatAmount($gw->fee_flat) }} @endif
                                @if($gw->fee_flat > 0 && $gw->fee_percent > 0) + @endif
                                @if($gw->fee_percent > 0) {{ $gw->fee_percent }}% @endif
                            @else
                                @lang('No fee')
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right" data-label="">
                        <a href="{{ route('admin.gateways.edit', $driverKey) }}"
                           class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                            @lang('Configure')
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-14 text-center text-sm text-gray-400">@lang('No payment gateways configured.')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
