<?php

namespace App\Http\Controllers\Admin\Ops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->input('type', 'transactions');

        $data = match ($type) {
            'transactions' => $this->transactions($request),
            'payments' => $this->payments($request),
            'payouts' => $this->payouts($request),
            'logins' => $this->logins($request),
            'notifications' => $this->notifications($request),
            default => collect(),
        };

        return view('admin.ops.reports', compact('type', 'data'));
    }

    public function export(Request $request): Response
    {
        $type = $request->input('type', 'transactions');

        $data = match ($type) {
            'transactions' => $this->transactions($request, export: true),
            'payments' => $this->payments($request, export: true),
            'payouts' => $this->payouts($request, export: true),
            'logins' => $this->logins($request, export: true),
            'notifications' => $this->notifications($request, export: true),
            default => collect(),
        };

        $filename = $type.'_'.now()->format('Ymd_His').'.csv';

        return $this->csvResponse($data->toArray(), $filename);
    }

    // --- Report queries ---

    private function transactions(Request $request, bool $export = false)
    {
        $query = DB::table('transactions')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->select([
                'transactions.id',
                'users.email as user_email',
                'transactions.type',
                'transactions.amount',
                'transactions.currency',
                'transactions.balance_after',
                'transactions.description',
                'transactions.created_at',
            ])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('transactions.created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('transactions.created_at', '<=', $request->input('to')))
            ->when($request->filled('type_filter'), fn ($q) => $q->where('transactions.type', $request->input('type_filter')))
            ->orderByDesc('transactions.created_at');

        return $export ? $query->get() : $query->paginate(30)->withQueryString();
    }

    private function payments(Request $request, bool $export = false)
    {
        $query = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->join('gateways', 'gateways.id', '=', 'payments.gateway_id')
            ->select([
                'payments.id',
                'users.email as user_email',
                'gateways.name as gateway',
                'payments.amount',
                'payments.currency',
                'payments.status',
                'payments.created_at',
            ])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('payments.created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('payments.created_at', '<=', $request->input('to')))
            ->when($request->filled('status'), fn ($q) => $q->where('payments.status', $request->input('status')))
            ->orderByDesc('payments.created_at');

        return $export ? $query->get() : $query->paginate(30)->withQueryString();
    }

    private function payouts(Request $request, bool $export = false)
    {
        $query = DB::table('payouts')
            ->join('users', 'users.id', '=', 'payouts.user_id')
            ->join('payout_methods', 'payout_methods.id', '=', 'payouts.payout_method_id')
            ->select([
                'payouts.id',
                'users.email as user_email',
                'payout_methods.name as method',
                'payouts.amount',
                'payouts.currency',
                'payouts.status',
                'payouts.created_at',
            ])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('payouts.created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('payouts.created_at', '<=', $request->input('to')))
            ->when($request->filled('status'), fn ($q) => $q->where('payouts.status', $request->input('status')))
            ->orderByDesc('payouts.created_at');

        return $export ? $query->get() : $query->paginate(30)->withQueryString();
    }

    private function logins(Request $request, bool $export = false)
    {
        $query = DB::table('user_logins')
            ->join('users', 'users.id', '=', 'user_logins.user_id')
            ->select([
                'user_logins.id',
                'users.email as user_email',
                'user_logins.ip_address',
                'user_logins.user_agent',
                'user_logins.created_at',
            ])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('user_logins.created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('user_logins.created_at', '<=', $request->input('to')))
            ->orderByDesc('user_logins.created_at');

        return $export ? $query->get() : $query->paginate(30)->withQueryString();
    }

    private function notifications(Request $request, bool $export = false)
    {
        $query = DB::table('notification_logs')
            ->select([
                'id',
                'notifiable_type',
                'notifiable_id',
                'template_key',
                'channel',
                'status',
                'error',
                'created_at',
            ])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->input('channel')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderByDesc('created_at');

        return $export ? $query->get() : $query->paginate(30)->withQueryString();
    }

    // --- CSV helper ---

    private function csvResponse($rows, string $filename): Response
    {
        $rows = is_array($rows) ? $rows : (array) $rows;

        ob_start();
        $handle = fopen('php://output', 'w');

        if (! empty($rows)) {
            $first = (array) $rows[0];
            fputcsv($handle, array_keys($first));
            foreach ($rows as $row) {
                fputcsv($handle, (array) $row);
            }
        }

        fclose($handle);
        $csv = ob_get_clean();

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
