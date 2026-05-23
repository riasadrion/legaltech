<?php

namespace App\Http\Controllers\User;

use App\Domain\Contract\Actions\DeductContractCredits;
use App\Domain\Contract\Models\Contract;
use App\Domain\Contract\Models\ContractParty;
use App\Domain\Contract\Models\ContractTemplate;
use App\Domain\Contract\Models\ContractTemplateCategory;
use App\Domain\Contract\Services\ContractAiService;
use App\Domain\Contract\Services\ContractPdfExporter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function index()
    {
        $user      = auth('user')->user();
        $contracts = Contract::where('user_id', $user->id)
            ->with('template')
            ->latest()
            ->paginate(12);

        return view('user.contracts.index', compact('contracts'));
    }

    public function create(Request $request)
    {
        $categories = ContractTemplateCategory::with(['templates' => function ($q) {
            $q->active()->orderBy('sort');
        }])->orderBy('sort')->get()->filter(fn($c) => $c->templates->isNotEmpty());

        $selectedCategory = $request->category;

        return view('user.contracts.create', compact('categories', 'selectedCategory'));
    }

    public function wizard(ContractTemplate $template)
    {
        if (!$template->active) {
            abort(404);
        }

        $template->load('fields');
        $groups = $template->fields->groupBy(fn($f) => $f->group ?: 'Details');

        return view('user.contracts.wizard', compact('template', 'groups'));
    }

    public function store(Request $request, DeductContractCredits $deductor)
    {
        $user     = auth('user')->user();
        $template = ContractTemplate::active()->findOrFail($request->template_id);
        $template->load('fields');

        // Validate dynamic fields
        $rules = ['template_id' => ['required', 'exists:contract_templates,id']];
        foreach ($template->fields as $field) {
            $fieldRules = $field->required ? ['required'] : ['nullable'];
            $fieldRules[] = match ($field->type) {
                'email'  => 'email',
                'number' => 'numeric',
                'date'   => 'date',
                default  => 'string|max:1000',
            };
            $rules["fields.{$field->key}"] = $fieldRules;
        }

        // Party fields
        $rules['parties']             = ['required', 'array', 'min:1'];
        $rules['parties.*.name']      = ['required', 'string', 'max:200'];
        $rules['parties.*.email']     = ['required', 'email'];
        $rules['parties.*.role']      = ['required', 'in:signer,viewer'];

        $data = $request->validate($rules);

        // Check credits
        try {
            $creditsUsed = $deductor->run($user, $template);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['credits' => $e->getMessage()]);
        }

        $contract = DB::transaction(function () use ($user, $template, $data, $creditsUsed) {
            $contract = Contract::create([
                'user_id'      => $user->id,
                'template_id'  => $template->id,
                'title'        => $template->name,
                'field_data'   => $data['fields'] ?? [],
                'status'       => Contract::STATUS_GENERATING,
                'credits_used' => $creditsUsed,
            ]);

            foreach ($data['parties'] as $i => $party) {
                ContractParty::create([
                    'contract_id' => $contract->id,
                    'name'        => $party['name'],
                    'email'       => $party['email'],
                    'role'        => $party['role'],
                    'sort'        => $i,
                ]);
            }

            return $contract;
        });

        // Generate AI content
        try {
            $aiService = app(ContractAiService::class);
            $bodyHtml  = $aiService->generate($contract);

            $contract->update([
                'body_html' => $bodyHtml,
                'status'    => Contract::STATUS_EDITING,
            ]);
        } catch (\Throwable $e) {
            $contract->update(['status' => Contract::STATUS_DRAFT]);
            return redirect()->route('user.contracts.show', $contract)
                ->withErrors(['generation' => 'AI generation failed: ' . $e->getMessage()]);
        }

        return redirect()->route('user.contracts.show', $contract)
            ->with('success', 'Contract generated — review and edit before finalizing.');
    }

    public function show(Contract $contract)
    {
        $this->authorizeContract($contract);
        $contract->load('template', 'parties');

        return view('user.contracts.show', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $this->authorizeContract($contract);

        if (!$contract->isEditable()) {
            return back()->withErrors(['edit' => 'This contract can no longer be edited.']);
        }

        $data = $request->validate([
            'title'    => ['required', 'string', 'max:200'],
            'body_html' => ['required', 'string'],
        ]);

        $contract->update($data);

        return back()->with('success', 'Contract saved.');
    }

    public function finalize(Contract $contract)
    {
        $this->authorizeContract($contract);

        if (!$contract->isEditable()) {
            return back()->withErrors(['finalize' => 'This contract cannot be finalized.']);
        }

        $contract->update(['status' => Contract::STATUS_READY]);

        return redirect()->route('user.contracts.show', $contract)
            ->with('success', 'Contract finalized.');
    }

    public function revertToDraft(Contract $contract)
    {
        $this->authorizeContract($contract);

        if ($contract->isSigned()) {
            return back()->withErrors(['revert' => 'Signed contracts cannot be reverted.']);
        }

        $contract->update([
            'status'      => Contract::STATUS_EDITING,
            'share_token' => null,
        ]);

        return back()->with('success', 'Contract reverted to draft.');
    }

    public function destroy(Contract $contract)
    {
        $this->authorizeContract($contract);

        if ($contract->isSigned()) {
            return back()->withErrors(['delete' => 'Signed contracts cannot be deleted.']);
        }

        $contract->delete();

        return redirect()->route('user.contracts.index')
            ->with('success', 'Contract deleted.');
    }

    public function pdf(Contract $contract, ContractPdfExporter $exporter)
    {
        $this->authorizeContract($contract);

        if (!$contract->isReady() && !$contract->isSigned()) {
            abort(403, 'Contract must be finalized before downloading.');
        }

        $contract->load('parties');
        $pdfBytes = $exporter->stream($contract);
        $filename = Str::slug($contract->title) . '.pdf';

        return response($pdfBytes, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function sendSign(Contract $contract)
    {
        $this->authorizeContract($contract);

        if (!$contract->isReady()) {
            return back()->withErrors(['sign' => 'Contract must be finalized before sending.']);
        }

        $contract->load('parties');

        foreach ($contract->parties as $party) {
            if ($party->role !== 'signer' || $party->hasSigned()) {
                continue;
            }

            $party->generateToken();

            $signUrl = route('contracts.sign', $party->token);

            // Send email notification
            \Illuminate\Support\Facades\Mail::raw(
                "Hello {$party->name},\n\n"
                . "You have been invited to review and sign the contract \"{$contract->title}\".\n\n"
                . "Click the link below to sign:\n{$signUrl}\n\n"
                . "This link is unique to you. Do not share it.",
                function ($m) use ($party, $contract) {
                    $m->to($party->email, $party->name)
                      ->subject("Signature required: {$contract->title}");
                }
            );
        }

        $contract->update(['status' => Contract::STATUS_SENT]);

        return back()->with('success', 'Signature requests sent to all parties.');
    }

    private function authorizeContract(Contract $contract): void
    {
        if ($contract->user_id !== auth('user')->id()) {
            abort(403);
        }
    }
}
