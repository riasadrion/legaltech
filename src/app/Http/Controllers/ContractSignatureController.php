<?php

namespace App\Http\Controllers;

use App\Domain\Contract\Models\Contract;
use App\Domain\Contract\Models\ContractParty;

class ContractSignatureController extends Controller
{
    public function show(string $token)
    {
        $party = ContractParty::where('token', $token)->firstOrFail();
        $contract = Contract::with('parties', 'template')->findOrFail($party->contract_id);

        if ($party->hasSigned()) {
            return view('theme::contracts.sign.already-signed', compact('party', 'contract'));
        }

        return view('theme::contracts.sign.show', compact('party', 'contract'));
    }

    public function confirm(string $token)
    {
        $party = ContractParty::where('token', $token)->firstOrFail();
        $contract = Contract::with('parties', 'template')->findOrFail($party->contract_id);

        if ($party->hasSigned()) {
            return redirect()->route('contracts.sign', $token);
        }

        $party->signed_at = now();
        $party->ip_address = request()->ip();
        $party->user_agent = request()->userAgent();
        $party->save();

        $contract->load('parties');

        if ($contract->allPartiesSigned()) {
            $contract->update(['status' => Contract::STATUS_SIGNED]);
        }

        return view('theme::contracts.sign.confirmed', compact('party', 'contract'));
    }
}
