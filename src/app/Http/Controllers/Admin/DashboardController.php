<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use App\Models\SupportTicket;
use App\Domain\Subscription\Models\Plan;
use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\ValueObjects\Money;
use App\Http\Controllers\Controller;
use App\Models\User;
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
        $months = [];
        $revenues = [];
        $expenses = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $revenue = (float) Transaction::where('type', Transaction::TYPE_CREDIT)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

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
        $plans = Plan::get();
        $data = [];
        $total = 0;

        foreach ($plans as $plan) {
            $revenue = (float) Payment::where('status', 'completed')
                ->whereHas('user.subscription', function ($q) use ($plan) {
                    $q->where('plan_id', $plan->id);
                })
                ->sum('amount');

            if ($revenue > 0) {
                $data[] = [
                    'label' => $plan->name,
                    'value' => $revenue,
                ];
                $total += $revenue;
            }
        }

        return $total > 0 ? $data : [];
    }

    private function getRevenueByGateway(): array
    {
        $gateways = Gateway::where('enabled', true)->get();
        $data = [];

        foreach ($gateways as $gateway) {
            $revenue = (float) Payment::where('status', 'completed')
                ->where('gateway_id', $gateway->id)
                ->sum('amount');

            if ($revenue > 0) {
                $data[] = [
                    'label' => $gateway->name,
                    'value' => $revenue,
                ];
            }
        }

        return $data;
    }
}
