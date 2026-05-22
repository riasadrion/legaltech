@extends('admin.layouts.app')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/static/css/apexcharts.css') }}">
@endpush

@section('content')
<div class="space-y-4">

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('Dashboard') }}</h1>
            <p class="page-subtitle">{{ __('Welcome back, :name', ['name' => auth('admin')->user()->name]) }} 👋</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card stat-purple p-5 relative overflow-hidden">
            <div class="stat-accent"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="stat-label">{{ __('Total Users') }}</div>
                    <div class="stat-value">{{ number_format($stats['totalUsers']) }}</div>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25">
                    <x-heroicon-o-users class="w-6 h-6" />
                </div>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="stat-change up">+12.5%</span>
                <span class="stat-period">vs last month</span>
            </div>
            <div id="spark1" class="spark-chart"></div>
        </div>

        <div class="stat-card stat-green p-5 relative overflow-hidden">
            <div class="stat-accent"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="stat-label">{{ __('Revenue') }}</div>
                    <div class="stat-value">{{ $stats['revenue']->format() }}</div>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/25">
                    <x-heroicon-o-currency-dollar class="w-6 h-6" />
                </div>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="stat-change up">+18.4%</span>
                <span class="stat-period">vs last month</span>
            </div>
            <div id="spark2" class="spark-chart"></div>
        </div>

        <div class="stat-card stat-amber p-5 relative overflow-hidden">
            <div class="stat-accent"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="stat-label">{{ __('Open Tickets') }}</div>
                    <div class="stat-value">{{ number_format($stats['openTickets']) }}</div>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/25">
                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" />
                </div>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="stat-change down">-8.1%</span>
                <span class="stat-period">vs last month</span>
            </div>
            <div id="spark3" class="spark-chart"></div>
        </div>

        <div class="stat-card stat-purple-dk p-5 relative overflow-hidden">
            <div class="stat-accent"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="stat-label">{{ __('Active Gateways') }}</div>
                    <div class="stat-value">{{ number_format($stats['activeGateways']) }}</div>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-600 to-slate-700 text-white shadow-lg shadow-slate-500/25">
                    <x-heroicon-o-credit-card class="w-6 h-6" />
                </div>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="stat-change up">+2.1%</span>
                <span class="stat-period">vs last month</span>
            </div>
            <div id="spark4" class="spark-chart"></div>
        </div>
    </div>

    {{-- Revenue Chart + Donut --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 card-table">
            <div class="chart-card-header">
                    <h3 class="text-sm font-semibold">{{ __('Revenue Analytics') }}</h3>
                    <p class="chart-card-sub">{{ __('Monthly performance breakdown') }}</p>
            </div>
            <div class="chart-card-body">
                <div id="revenueChart"></div>
            </div>
        </div>

        @if(!empty($chartData['revenueByPlan']))
        <div class="card-table">
            <div class="chart-card-header">
                <h3 class="text-sm font-semibold">{{ __('Revenue Mix') }}</h3>
                <p class="chart-card-sub">{{ __('By subscription tier') }}</p>
            </div>
            <div class="chart-card-body">
                <div id="revenueDonut"></div>
                <div class="mt-4 space-y-2" id="donutLegend"></div>
            </div>
        </div>
        @else
        <div class="card-table">
            <div class="chart-card-header">
                <h3 class="text-sm font-semibold">{{ __('Payment Methods') }}</h3>
                <p class="chart-card-sub">{{ __('Revenue by gateway') }}</p>
            </div>
            <div class="chart-card-body">
                <div id="gatewayChart"></div>
            </div>
        </div>
        @endif
    </div>


    {{-- Activity Feed + Recent Transactions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Recent Transactions --}}
        <div class="lg:col-span-2 card-table">
            <div class="chart-card-header flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold">{{ __('Recent Transactions') }}</h3>
                    <p class="chart-card-sub">{{ __('Latest wallet activity') }}</p>
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

        {{-- Recent Users --}}
        <div class="card-table">
            <div class="chart-card-header flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold">{{ __('Recent Users') }}</h3>
                    <p class="chart-card-sub">{{ __('Latest signups') }}</p>
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
<script src="{{ asset('assets/static/js/apexcharts.min.js') }}"></script>
<script>
(function () {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var tc = isDark ? '#8b8ea8' : '#6b7280';
    var gc = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    var th = isDark ? 'dark' : 'light';
    var ff = 'DM Sans, sans-serif';

    // Sparklines
    function spark(id, data, color) {
        new ApexCharts(document.getElementById(id), {
            chart: { type: 'area', height: 32, sparkline: { enabled: true }, animations: { duration: 800 } },
            series: [{ data: data }],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0 } },
            colors: [color],
            tooltip: { enabled: false },
            grid: { padding: { top: 0, bottom: 0 } }
        }).render();
    }
    spark('spark1', [30,45,28,60,52,72,65,80,75,95,88,110], '#6366f1');
    spark('spark2', [40,38,50,45,60,55,70,72,68,80,85,92], '#10b981');
    spark('spark3', [20,32,28,40,38,52,48,60,55,70,68,75], '#f59e0b');
    spark('spark4', [5.2,5.0,4.9,5.1,5.3,5.0,4.8,4.9,5.1,4.8,4.9,4.87], '#8b5cf6');

    // Revenue Area Chart
    new ApexCharts(document.querySelector("#revenueChart"), {
        series: [
            { name: 'Revenue', data: @json($chartData['monthlyRevenue']['revenues']) },
            { name: 'Expenses', data: @json($chartData['monthlyRevenue']['expenses']) }
        ],
        chart: { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent', animations: { easing: 'easeinout', speed: 800 } },
        colors: ['#6366f1', '#10b981'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.2, opacityTo: 0, stops: [0, 100] } },
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

    new ApexCharts(document.querySelector("#revenueDonut"), {
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
        legendHtml += '<div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-sm flex-shrink-0" style="background:' + colors[i] + '"></span><span class="text-xs flex-1 legend-label">' + d.label + '</span><span class="text-xs legend-value">$' + (d.value / 1000).toFixed(1) + 'K</span><span class="text-xs legend-pct">' + pct + '%</span></div>';
    });
    document.getElementById('donutLegend').innerHTML = legendHtml;
    @else
    // Gateway Revenue Bar Chart
    var gatewayData = @json($chartData['revenueByGateway']);
    if (gatewayData.length > 0) {
        var gatewayLabels = gatewayData.map(function(d) { return d.label; });
        var gatewayValues = gatewayData.map(function(d) { return d.value; });

        new ApexCharts(document.querySelector("#gatewayChart"), {
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