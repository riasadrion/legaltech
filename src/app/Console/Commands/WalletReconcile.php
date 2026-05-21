<?php

namespace App\Console\Commands;

use App\Domain\Wallet\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WalletReconcile extends Command
{
    protected $signature = 'wallet:reconcile
                            {--fix : Automatically correct mismatched balance values}
                            {--user= : Reconcile a single user by ID}';

    protected $description = 'Check that users.balance matches SUM(transactions.amount)';

    public function handle(): int
    {
        $fix = $this->option('fix');
        $userId = $this->option('user');

        $query = User::query();
        if ($userId) {
            $query->where('id', $userId);
        }

        $mismatches = 0;
        $checked = 0;

        $query->chunkById(200, function ($users) use ($fix, &$mismatches, &$checked) {
            foreach ($users as $user) {
                $checked++;
                $ledgerSum = (float) Transaction::where('user_id', $user->id)->sum('amount');
                $cached = (float) ($user->balance ?? 0);

                if (bccomp((string) $ledgerSum, (string) $cached, 8) !== 0) {
                    $mismatches++;
                    $this->warn(sprintf(
                        'MISMATCH user #%d (%s): cache=%s ledger=%s diff=%s',
                        $user->id,
                        $user->email,
                        rtrim(rtrim(number_format($cached, 8, '.', ''), '0'), '.'),
                        rtrim(rtrim(number_format($ledgerSum, 8, '.', ''), '0'), '.'),
                        rtrim(rtrim(number_format($ledgerSum - $cached, 8, '.', ''), '0'), '.'),
                    ));

                    if ($fix) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['balance' => $ledgerSum]);

                        $this->line("  → Fixed: balance set to {$ledgerSum}");
                    }
                }
            }
        });

        $this->info("Checked {$checked} users. Mismatches: {$mismatches}.");

        if ($mismatches > 0 && ! $fix) {
            $this->comment('Run with --fix to correct mismatches.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
