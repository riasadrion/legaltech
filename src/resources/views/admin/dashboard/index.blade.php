@extends('admin.layouts.app')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/global/css/apexcharts.css') }}">
@endpush

@section('content')
<div class="space-y-4">

    <div class="page-header">
        <div>
            <h1 class="page-title">@lang('Dashboard')</h1>
            <p class="page-subtitle">{{ __('Welcome back, :name', ['name' => auth('admin')->user()->name]) }} 👋</p>
        </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card p-5 relative overflow-hidden">
            <span class="stat-trend-pill up">
                <x-heroicon-m-arrow-trending-up class="w-3.5 h-3.5" /> +12.5%
            </span>
            <div class="stat-icon-soft stat-icon-soft-indigo mb-4">
                <x-heroicon-o-users class="w-5 h-5" />
            </div>
            <div class="stat-label">@lang('Total Users')</div>
            <div class="stat-value">{{ number_format($stats['totalUsers']) }}</div>
            <div class="stat-caption">
                <x-heroicon-o-clock /> @lang('Updated just now')
            </div>
        </div>

        <div class="stat-card p-5 relative overflow-hidden">
            <span class="stat-trend-pill up">
                <x-heroicon-m-arrow-trending-up class="w-3.5 h-3.5" /> +18.4%
            </span>
            <div class="stat-icon-soft stat-icon-soft-emerald mb-4">
                <x-heroicon-o-currency-dollar class="w-5 h-5" />
            </div>
            <div class="stat-label">@lang('Revenue')</div>
            <div class="stat-value">{{ $stats['revenue']->format() }}</div>
            <div class="stat-caption">
                <x-heroicon-o-calendar /> @lang('This month')
            </div>
        </div>

        <div class="stat-card p-5 relative overflow-hidden">
            <span class="stat-trend-pill down">
                <x-heroicon-m-arrow-trending-down class="w-3.5 h-3.5" /> -8.1%
            </span>
            <div class="stat-icon-soft stat-icon-soft-amber mb-4">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
            </div>
            <div class="stat-label">@lang('Open Tickets')</div>
            <div class="stat-value">{{ number_format($stats['openTickets']) }}</div>
            <a href="{{ route('admin.support.tickets.index') }}" class="stat-caption stat-caption-link">
                <x-heroicon-o-arrow-top-right-on-square /> @lang('View support queue')
            </a>
        </div>

        <div class="stat-card p-5 relative overflow-hidden">
            <span class="stat-trend-pill up">
                <x-heroicon-m-arrow-trending-up class="w-3.5 h-3.5" /> +2.1%
            </span>
            <div class="stat-icon-soft stat-icon-soft-violet mb-4">
                <x-heroicon-o-credit-card class="w-5 h-5" />
            </div>
            <div class="stat-label">@lang('Active Gateways')</div>
            <div class="stat-value">{{ number_format($stats['activeGateways']) }}</div>
            <div class="stat-caption">
                <x-heroicon-o-shield-check /> @lang('All operational')
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 card-table">
            <div class="chart-card-header">
                    <h2 class="text-sm font-semibold">@lang('Revenue Analytics')</h2>
                    <p class="chart-card-sub">@lang('Monthly revenue for the past 12 months')</p>
            </div>
            <div class="chart-card-body">
                <div id="revenueChart"></div>
            </div>
        </div>

        @if(!empty($chartData['revenueByPlan']))
        <div class="card-table">
            <div class="chart-card-header">
                <h2 class="text-sm font-semibold">@lang('Revenue Mix')</h2>
                <p class="chart-card-sub">@lang('Where your revenue is coming from')</p>
            </div>
            <div class="chart-card-body">
                <div id="revenueDonut"></div>
                <div class="mt-4 space-y-2" id="donutLegend"></div>
            </div>
        </div>
        @else
        <div class="card-table">
            <div class="chart-card-header">
                <h2 class="text-sm font-semibold">@lang('Payment Methods')</h2>
                <p class="chart-card-sub">@lang('Completed payments by provider')</p>
            </div>
            <div class="chart-card-body">
                <div id="gatewayChart"></div>
            </div>
        </div>
        @endif
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 card-table">
            <div class="chart-card-header flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold">@lang('Recent Transactions')</h2>
                    <p class="chart-card-sub">@lang('Newest credits and debits')</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">View All →</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-brand-500 flex items-center justify-center text-white text-[10px] font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($tx->user->name ?? $tx->user->email ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium">{{ $tx->user->name ?? $tx->user->email ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="text-xs">{{ Str::limit($tx->description ?? '—', 40) }}</td>
                            <td>
                                <span class="badge {{ $tx->isCredit() ? 'badge-success' : 'badge-danger' }}">
                                    {{ $tx->isCredit() ? 'Credit' : 'Debit' }}
                                </span>
                            </td>
                            <td class="text-xs font-semibold {{ $tx->isCredit() ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $tx->formattedAmount() }}
                            </td>
                            <td class="text-xs text-gray-400 whitespace-nowrap">{{ $tx->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-10 text-center text-xs text-gray-400">No transactions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <div class="card-table">
            <div class="chart-card-header flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold">@lang('Recent Users')</h2>
                    <p class="chart-card-sub">@lang('Newest accounts on the platform')</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">View All →</a>
            </div>
            <div class="space-y-2 p-4">
                @forelse($recentUsers as $user)
                <a href="{{ route('admin.users.show', $user) }}" class="recent-user-card-new flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:border-brand-300 hover:bg-brand-50 transition">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-purple-500 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                            {{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $user->name ?? $user->email }}</div>
                            <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @empty
                <div class="py-8 text-center text-xs text-gray-400">No users yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="{{ asset('assets/static/global/js/apexcharts.min.js') }}"></script>
<script>
(function () {
    'use strict';
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var tc = isDark ? '#8b8ea8' : '#6b7280';
    var gc = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    var th = isDark ? 'dark' : 'light';
    var ff = 'DM Sans, sans-serif';

    // Revenue Area Chart
    new ApexCharts($('#revenueChart')[0], {
        series: [
            { name: 'Revenue', data: @json($chartData['monthlyRevenue']['revenues']) },
            { name: 'Expenses', data: @json($chartData['monthlyRevenue']['expenses']) }
        ],
        chart: { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent', animations: { easing: 'easeinout', speed: 800 } },
        colors: ['#6366f1', '#10b981'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 100] } },
        xaxis: {
            categories: @json($chartData['monthlyRevenue']['months']),
            labels: { style: { colors: tc, fontSize: '11px', fontFamily: ff } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: tc, fontSize: '11px', fontFamily: ff },
                formatter: function (v) { return '$' + (v/1000).toFixed(0) + 'K'; }
            }
        },
        grid: { borderColor: gc, strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
        tooltip: { theme: th, style: { fontFamily: ff, fontSize: '12px' }, y: { formatter: function (v) { return '$' + v.toLocaleString(); } } },
        legend: { labels: { colors: [tc, tc] }, fontFamily: ff, fontSize: '12px', markers: { width: 8, height: 8, radius: 2, strokeWidth: 0 } },
        dataLabels: { enabled: false }
    }).render();

    // Donut Chart
    @if(!empty($chartData['revenueByPlan']))
    var donutData = @json($chartData['revenueByPlan']);
    var donutLabels = donutData.map(function(d) { return d.label; });
    var donutValues = donutData.map(function(d) { return d.value; });
    var donutTotal = donutValues.reduce(function(a, b) { return a + b; }, 0);
    var colors = ['#6366f1', '#10b981', '#3b82f6', '#f59e0b', '#ec4899', '#f97316'];

    new ApexCharts($('#revenueDonut')[0], {
        chart: { type: 'donut', height: 200, background: 'transparent', animations: { easing: 'easeinout', speed: 800 } },
        series: donutValues,
        labels: donutLabels,
        colors: colors.slice(0, donutValues.length),
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: isDark ? '#f1f1f5' : '#0f1015',
                            formatter: function () { return '$' + (donutTotal / 1000).toFixed(1) + 'K'; },
                            style: { fontFamily: ff, fontSize: '14px', fontWeight: 600 }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { width: 0 },
        legend: { show: false },
        tooltip: { theme: th, style: { fontFamily: ff, fontSize: '12px' }, y: { formatter: function (v) { return '$' + v.toLocaleString(); } } }
    }).render();

    // Generate legend
    var legendHtml = '';
    donutData.forEach(function(d, i) {
        var pct = ((d.value / donutTotal) * 100).toFixed(0);
        legendHtml += '<div class="flex items-center gap-2"><span class="legend-color-dot" data-color="' + colors[i] + '"></span><span class="text-xs flex-1 legend-label">' + d.label + '</span><span class="text-xs legend-value">$' + (d.value / 1000).toFixed(1) + 'K</span><span class="text-xs legend-pct">' + pct + '%</span></div>';
    });
    $('#donutLegend').html(legendHtml);
    $('#donutLegend .legend-color-dot[data-color]').each(function () {
        $(this).css('background', $(this).data('color'));
    });
    @else
    // Gateway Revenue Bar Chart
    var gatewayData = @json($chartData['revenueByGateway']);
    if (gatewayData.length > 0) {
        var gatewayLabels = gatewayData.map(function(d) { return d.label; });
        var gatewayValues = gatewayData.map(function(d) { return d.value; });

        new ApexCharts($('#gatewayChart')[0], {
            chart: { type: 'bar', height: 200, background: 'transparent', animations: { easing: 'easeinout', speed: 800 }, toolbar: { show: false } },
            series: [{ name: 'Revenue', data: gatewayValues }],
            colors: ['#6366f1'],
            xaxis: {
                categories: gatewayLabels,
                labels: { style: { colors: tc, fontSize: '11px', fontFamily: ff } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: tc, fontSize: '11px', fontFamily: ff },
                    formatter: function (v) { return '$' + (v / 1000).toFixed(0) + 'K'; }
                }
            },
            grid: { borderColor: gc, strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '65%' } },
            tooltip: { theme: th, style: { fontFamily: ff, fontSize: '12px' }, y: { formatter: function (v) { return '$' + v.toLocaleString(); } } },
            dataLabels: { enabled: false }
        }).render();
    }
    @endif

})();
</script>
@endpush
@endsection