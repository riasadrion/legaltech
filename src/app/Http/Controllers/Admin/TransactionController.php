<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Wallet\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->latest('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('user', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })->orWhere('description', 'like', "%{$q}%");
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(30)->withQueryString();

        $summary = [
            'total_credit' => Transaction::where('type', Transaction::TYPE_CREDIT)->sum('amount'),
            'total_debit'  => Transaction::where('type', Transaction::TYPE_DEBIT)->sum('amount'),
            'count'        => Transaction::count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'summary'));
    }
}
