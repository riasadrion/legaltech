<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Forms\Models\Form;
use App\Domain\Forms\Models\FormField;
use App\Domain\Payment\Models\PayoutMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayoutMethodController extends Controller
{
    public function index()
    {
        $methods = PayoutMethod::with('form')->orderBy('sort')->get();

        return view('admin.payout-methods.index', compact('methods'));
    }

    public function create()
    {
        $forms = Form::where('context', 'payout_method')->get();

        return view('admin.payout-methods.create', compact('forms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:100',
            'delay_text' => 'nullable|array',
            'min_amount' => 'required|integer|min:0',
            'max_amount' => 'required|integer|min:1',
            'fee_flat' => 'nullable|integer|min:0',
            'fee_percent' => 'nullable|numeric|min:0|max:100',
            'kyc_required' => 'boolean',
            'enabled' => 'boolean',
            'sort' => 'nullable|integer|min:0',
            'fields' => 'nullable|array',
        ]);

        $form = $this->syncForm($request);

        $method = new PayoutMethod;
        $method->name = $data['name'];
        $method->delay_text = $data['delay_text'] ?? null;
        $method->min_amount = $data['min_amount'];
        $method->max_amount = $data['max_amount'];
        $method->fee_flat = $data['fee_flat'] ?? 0;
        $method->fee_percent = $data['fee_percent'] ?? 0;
        $method->kyc_required = $request->boolean('kyc_required', true);
        $method->enabled = $request->boolean('enabled', true);
        $method->sort = $data['sort'] ?? 0;
        $method->form_id = $form?->id;
        $method->save();

        return redirect()->route('admin.payout-methods.index')
            ->with('success', 'Payout method created.');
    }

    public function edit(PayoutMethod $payoutMethod)
    {
        $payoutMethod->load('form.fields');
        $forms = Form::where('context', 'payout_method')->get();

        return view('admin.payout-methods.edit', compact('payoutMethod', 'forms'));
    }

    public function update(Request $request, PayoutMethod $payoutMethod)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:100',
            'delay_text' => 'nullable|array',
            'min_amount' => 'required|integer|min:0',
            'max_amount' => 'required|integer|min:1',
            'fee_flat' => 'nullable|integer|min:0',
            'fee_percent' => 'nullable|numeric|min:0|max:100',
            'kyc_required' => 'boolean',
            'enabled' => 'boolean',
            'sort' => 'nullable|integer|min:0',
            'fields' => 'nullable|array',
        ]);

        $form = $this->syncForm($request, $payoutMethod->form);

        $payoutMethod->name = $data['name'];
        $payoutMethod->delay_text = $data['delay_text'] ?? null;
        $payoutMethod->min_amount = $data['min_amount'];
        $payoutMethod->max_amount = $data['max_amount'];
        $payoutMethod->fee_flat = $data['fee_flat'] ?? 0;
        $payoutMethod->fee_percent = $data['fee_percent'] ?? 0;
        $payoutMethod->kyc_required = $request->boolean('kyc_required', true);
        $payoutMethod->enabled = $request->boolean('enabled', true);
        $payoutMethod->sort = $data['sort'] ?? 0;
        $payoutMethod->form_id = $form?->id;
        $payoutMethod->save();

        return redirect()->route('admin.payout-methods.index')
            ->with('success', 'Payout method updated.');
    }

    public function toggleEnabled(PayoutMethod $payoutMethod)
    {
        $payoutMethod->enabled = ! $payoutMethod->enabled;
        $payoutMethod->save();

        return back()->with('success', 'Status updated.');
    }

    public function destroy(PayoutMethod $payoutMethod)
    {
        $payoutMethod->delete();

        return redirect()->route('admin.payout-methods.index')
            ->with('success', 'Payout method deleted.');
    }

    private function syncForm(Request $request, ?Form $existing = null): ?Form
    {
        $fields = $request->input('fields', []);

        if (empty($fields)) {
            return null;
        }

        $form = $existing ?? new Form;
        $form->key = 'payout_method_'.($existing ? $existing->id : uniqid());
        $form->name = ['en' => $request->input('name.en', 'Payout Form')];
        $form->context = 'payout_method';
        $form->save();

        $form->fields()->delete();

        foreach ($fields as $sort => $fieldData) {
            if (empty($fieldData['name'])) {
                continue;
            }

            $options = null;
            if (($fieldData['type'] ?? '') === 'select' && ! empty($fieldData['options_raw'])) {
                $options = collect(explode("\n", $fieldData['options_raw']))
                    ->filter()
                    ->map(function ($line) {
                        $parts = explode('|', $line, 2);

                        return ['value' => trim($parts[0]), 'label' => trim($parts[1] ?? $parts[0])];
                    })
                    ->values()
                    ->toArray();
            }

            $field = new FormField;
            $field->form_id = $form->id;
            $field->name = $fieldData['name'];
            $field->type = $fieldData['type'] ?? 'text';
            $field->label = $fieldData['label'] ?? ['en' => $fieldData['name']];
            $field->placeholder = $fieldData['placeholder'] ?? null;
            $field->options = $options;
            $field->required = ! empty($fieldData['required']);
            $field->sort = $sort;
            $field->save();
        }

        return $form;
    }
}
