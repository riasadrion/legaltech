<?php

namespace App\Http\Controllers\User;

use App\Domain\Contract\Actions\DeductAiGenerationCredits;
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
use Illuminate\Support\Facades\Mail;
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

    // Step 1: Pick a category
    public function create()
    {
        $categories = ContractTemplateCategory::withCount(['templates' => fn($q) => $q->active()])
            ->orderBy('sort')
            ->get()
            ->filter(fn($c) => $c->templates_count > 0);

        return view('user.contracts.create', compact('categories'));
    }

    // Step 2: Pick a template within a category
    public function templates(ContractTemplateCategory $category)
    {
        $templates = $category->templates()->active()->orderBy('sort')->get();

        return view('user.contracts.templates', compact('category', 'templates'));
    }

    // Step 3: Fill wizard fields
    public function wizard(ContractTemplate $template)
    {
        if (!$template->active) {
            abort(404);
        }

        $template->load('fields');
        $groups = $template->fields->groupBy(fn($f) => $f->group ?: 'Details');

        return view('user.contracts.wizard', compact('template', 'groups'));
    }

    // Step 4: Preview filled values before generating
    public function preview(Request $request)
    {
        $template = ContractTemplate::active()->findOrFail($request->template_id);
        $template->load('fields');

        $data = $this->validateWizard($request, $template);

        // Stash to session so the user can edit and resubmit
        session([
            'contract.wizard' => [
                'template_id' => $template->id,
                'fields'      => $data['fields'] ?? [],
                'parties'     => $data['parties'],
            ],
        ]);

        return view('user.contracts.preview', [
            'template' => $template,
            'fields'   => $data['fields'] ?? [],
            'parties'  => $data['parties'],
        ]);
    }

    // Step 5: Generate (instant placeholder substitution)
    public function store(Request $request, DeductContractCredits $deductor, ContractAiService $ai)
    {
        $payload = session('contract.wizard');

        if (!$payload || (int) $payload['template_id'] !== (int) $request->template_id) {
            return redirect()->route('user.contracts.create')
                ->withErrors(['session' => 'Your wizard session expired. Please start again.']);
        }

        $user     = auth('user')->user();
        $template = ContractTemplate::active()->findOrFail($payload['template_id']);

        try {
            $creditsUsed = $deductor->run($user, $template);
        } catch (\RuntimeException $e) {
            return redirect()->route('user.contracts.create')
                ->withErrors(['credits' => $e->getMessage()]);
        }

        $contract = DB::transaction(function () use ($user, $template, $payload, $creditsUsed) {
            $contract = Contract::create([
                'user_id'      => $user->id,
                'template_id'  => $template->id,
                'title'        => $template->name,
                'field_data'   => $payload['fields'],
                'status'       => Contract::STATUS_EDITING,
                'credits_used' => $creditsUsed,
            ]);

            foreach ($payload['parties'] as $i => $party) {
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

        // Substitute placeholders → final HTML body
        $contract->update([
            'body_html' => $ai->generate($contract),
        ]);

        session()->forget('contract.wizard');

        return redirect()->route('user.contracts.show', $contract)
            ->with('success', 'Contract generated. Review, edit, and finalize when ready.');
    }

    private function validateWizard(Request $request, ContractTemplate $template): array
    {
        $rules = ['template_id' => ['required', 'exists:contract_templates,id']];

        foreach ($template->fields as $field) {
            $fieldRules = $field->required ? ['required'] : ['nullable'];

            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:2000';
            }

            $rules["fields.{$field->key}"] = $fieldRules;
        }

        $rules['parties']         = ['required', 'array', 'min:1'];
        $rules['parties.*.name']  = ['required', 'string', 'max:200'];
        $rules['parties.*.email'] = ['required', 'email'];
        $rules['parties.*.role']  = ['required', 'in:signer,viewer'];

        return $request->validate($rules);
    }

    // AI custom contract — show the brief form
    public function aiCreate()
    {
        $user = auth('user')->user();

        if (!$user->canGenerateAiContract()) {
            return redirect()->route('user.subscription.plans')
                ->with('upgrade_ai', 'Unlock AI contract drafting — upgrade to a Pro plan to draft custom agreements in plain English.');
        }

        return view('user.contracts.ai-create');
    }

    // AI custom contract — generate from user brief
    public function aiStore(Request $request, ContractAiService $ai, DeductAiGenerationCredits $deductor)
    {
        $user = auth('user')->user();

        if (!$user->canGenerateAiContract()) {
            return redirect()->route('user.subscription.index')
                ->withErrors(['ai' => 'Your current plan does not include AI contract generation.']);
        }

        $data = $request->validate([
            'brief'           => ['required', 'string', 'min:20', 'max:4000'],
            'parties'         => ['required', 'array', 'min:1'],
            'parties.*.name'  => ['required', 'string', 'max:200'],
            'parties.*.email' => ['required', 'email'],
            'parties.*.role'  => ['required', 'in:signer,viewer'],
        ]);

        try {
            $creditsUsed = $deductor->run($user);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['credits' => $e->getMessage()]);
        }

        try {
            $draft = $ai->draftFromUserBrief($data['brief'], $data['parties']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['ai' => 'AI generation failed: ' . $e->getMessage()]);
        }

        $contract = DB::transaction(function () use ($user, $data, $draft, $creditsUsed) {
            $contract = Contract::create([
                'user_id'      => $user->id,
                'template_id'  => null,
                'title'        => $draft['title'],
                'body_html'    => $draft['body'],
                'field_data'   => ['brief' => $data['brief']],
                'status'       => Contract::STATUS_EDITING,
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

        return redirect()->route('user.contracts.show', $contract)
            ->with('success', 'Contract drafted. Review, edit, and finalize when ready.');
    }

    public function aiRegenerate(Contract $contract, ContractAiService $ai, DeductAiGenerationCredits $deductor)
    {
        $this->authorizeContract($contract);

        if (!$contract->isEditable()) {
            return back()->withErrors(['ai' => 'Only contracts in editing state can be regenerated.']);
        }

        $brief = $contract->field_data['brief'] ?? null;

        if (!$brief) {
            return back()->withErrors(['ai' => 'No brief found — this contract cannot be regenerated.']);
        }

        $user = auth('user')->user();

        try {
            $deductor->run($user);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['ai' => $e->getMessage()]);
        }

        $contract->load('parties');
        $parties = $contract->parties->map(fn ($p) => [
            'name'  => $p->name,
            'email' => $p->email,
            'role'  => $p->role,
        ])->toArray();

        try {
            $draft = $ai->draftFromUserBrief($brief, $parties);
        } catch (\Throwable $e) {
            return back()->withErrors(['ai' => 'AI generation failed: ' . $e->getMessage()]);
        }

        $contract->title    = $draft['title'];
        $contract->body_html = $draft['body'];
        $contract->save();

        return redirect()->route('user.contracts.show', $contract)
            ->with('success', 'Contract regenerated successfully.');
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

        if (!$contract->isReady() && !$contract->isSigned() && $contract->status !== Contract::STATUS_EDITING) {
            abort(403, 'Contract must be generated before downloading.');
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

            Mail::send([], [], function ($m) use ($party, $contract, $signUrl) {
                $appName = config('app.name');
                $appUrl  = config('app.url');
                $html    = view('emails.contracts.sign-request', compact('party', 'contract', 'signUrl', 'appName', 'appUrl'))->render();

                $m->to($party->email, $party->name)
                  ->subject("Signature required: {$contract->title}")
                  ->html($html);
            });
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
