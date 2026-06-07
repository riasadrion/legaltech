<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\ValueObjects\Money;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'totalUsers' => User::count(),
            'revenue' => $this->getRevenueThisMonth(),
            'openTickets' => SupportTicket::whereIn('status', [
                SupportTicket::STATUS_OPEN,
                SupportTicket::STATUS_IN_PROGRESS,
                SupportTicket::STATUS_WAITING,
            ])->count(),
            'activeGateways' => Gateway::where('enabled', true)->count(),
        ];

        $recentTransactions = Transaction::with('user')
            ->whereIn('type', [Transaction::TYPE_CREDIT, Transaction::TYPE_DEBIT])
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::latest()->take(6)->get(['id', 'name', 'email', 'created_at']);

        $chartData = [
            'monthlyRevenue' => $this->getMonthlyRevenueData(),
            'revenueByPlan' => $this->getRevenueByPlan(),
            'revenueByGateway' => $this->getRevenueByGateway(),
        ];

        return view('admin.dashboard.index', compact('stats', 'recentTransactions', 'recentUsers', 'chartData'));
    }

    private function getRevenueThisMonth(): Money
    {
        $sum = Transaction::where('type', Transaction::TYPE_CREDIT)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        return Money::of((float) $sum);
    }

    private function getMonthlyRevenueData(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        $rows = Transaction::where('type', Transaction::TYPE_CREDIT)
            ->where('created_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $months = [];
        $revenues = [];
        $expenses = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $revenue = (float) ($rows[$date->format('Y-m')] ?? 0);
            $revenues[] = $revenue;
            $expenses[] = $revenue * 0.4; // Assume 40% expenses
        }

        return [
            'months' => $months,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    private function getRevenueByPlan(): array
    {
        $latestSubscriptionPerUser = DB::table('user_subscriptions')
            ->selectRaw('user_id, MAX(id) as id')
            ->groupBy('user_id');

        $revenueByPlanId = Payment::query()
            ->joinSub($latestSubscriptionPerUser, 'latest_subs', 'latest_subs.user_id', '=', 'payments.user_id')
            ->join('user_subscriptions', 'user_subscriptions.id', '=', 'latest_subs.id')
            ->where('payments.status', 'completed')
            ->groupBy('user_subscriptions.plan_id')
            ->selectRaw('user_subscriptions.plan_id as plan_id, SUM(payments.amount) as total')
            ->pluck('total', 'plan_id');

        if ($revenueByPlanId->isEmpty()) {
            return [];
        }

        return Plan::whereIn('id', $revenueByPlanId->keys())
            ->get()
            ->map(fn ($plan) => [
                'label' => $plan->name,
                'value' => (float) $revenueByPlanId[$plan->id],
            ])
            ->values()
            ->all();
    }

    private function getRevenueByGateway(): array
    {
        $revenueByGatewayId = Payment::where('status', 'completed')
            ->groupBy('gateway_id')
            ->selectRaw('gateway_id, SUM(amount) as total')
            ->pluck('total', 'gateway_id');

        if ($revenueByGatewayId->isEmpty()) {
            return [];
        }

        return Gateway::whereIn('id', $revenueByGatewayId->keys())
            ->where('enabled', true)
            ->get()
            ->map(fn ($gateway) => [
                'label' => $gateway->name,
                'value' => (float) $revenueByGatewayId[$gateway->id],
            ])
            ->values()
            ->all();
    }
}
