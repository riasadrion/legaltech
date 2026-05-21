<?php

namespace App\Http\Controllers\User;

use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\Services\WalletService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request, WalletService $wallet)
    {
        $user = auth('user')->user();

        $query = Transaction::where('user_id', $user->id)->latest('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $balance = $wallet->getBalance($user);

        return view('user.wallet.index', compact('transactions', 'balance'));
    }
}
