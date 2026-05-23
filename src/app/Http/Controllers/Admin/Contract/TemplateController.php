<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Domain\Contract\Models\ContractTemplate;
use App\Domain\Contract\Models\ContractTemplateCategory;
use App\Domain\Contract\Models\ContractTemplateField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = ContractTemplate::with('category')->orderBy('sort');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $templates   = $query->paginate(20)->withQueryString();
        $categories  = ContractTemplateCategory::orderBy('sort')->get();

        return view('admin.contracts.templates.index', compact('templates', 'categories'));
    }

    public function create()
    {
        $categories = ContractTemplateCategory::orderBy('sort')->get();
        return view('admin.contracts.templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => ['required', 'exists:contract_template_categories,id'],
            'name'             => ['required', 'string', 'max:200'],
            'description'      => ['nullable', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'ai_system_prompt' => ['nullable', 'string'],
            'credits_cost'     => ['required', 'integer', 'min:0'],
            'active'           => ['nullable', 'boolean'],
            'sort'             => ['nullable', 'integer'],
            'fields'           => ['nullable', 'array'],
            'fields.*.label'   => ['required_with:fields', 'string', 'max:100'],
            'fields.*.key'     => ['required_with:fields', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'fields.*.type'    => ['required_with:fields', 'in:text,textarea,date,number,select,email'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:200'],
            'fields.*.group'   => ['nullable', 'string', 'max:100'],
            'fields.*.options' => ['nullable', 'string'],
            'fields.*.required'=> ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');

        $template = ContractTemplate::create($data);

        $this->syncFields($template, $request->input('fields', []));

        return redirect()->route('admin.contracts.templates.index')
            ->with('success', 'Template created.');
    }

    public function edit(ContractTemplate $template)
    {
        $categories = ContractTemplateCategory::orderBy('sort')->get();
        $template->load('fields');
        return view('admin.contracts.templates.edit', compact('template', 'categories'));
    }

    public function update(Request $request, ContractTemplate $template)
    {
        $data = $request->validate([
            'category_id'      => ['required', 'exists:contract_template_categories,id'],
            'name'             => ['required', 'string', 'max:200'],
            'description'      => ['nullable', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'ai_system_prompt' => ['nullable', 'string'],
            'credits_cost'     => ['required', 'integer', 'min:0'],
            'active'           => ['nullable', 'boolean'],
            'sort'             => ['nullable', 'integer'],
            'fields'           => ['nullable', 'array'],
            'fields.*.label'   => ['required_with:fields', 'string', 'max:100'],
            'fields.*.key'     => ['required_with:fields', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'fields.*.type'    => ['required_with:fields', 'in:text,textarea,date,number,select,email'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:200'],
            'fields.*.group'   => ['nullable', 'string', 'max:100'],
            'fields.*.options' => ['nullable', 'string'],
            'fields.*.required'=> ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');

        $template->update($data);

        $this->syncFields($template, $request->input('fields', []));

        return redirect()->route('admin.contracts.templates.index')
            ->with('success', 'Template updated.');
    }

    public function destroy(ContractTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.contracts.templates.index')
            ->with('success', 'Template deleted.');
    }

    public function toggle(ContractTemplate $template)
    {
        $template->update(['active' => !$template->active]);
        return back()->with('success', 'Template ' . ($template->active ? 'activated' : 'deactivated') . '.');
    }

    private function syncFields(ContractTemplate $template, array $fields): void
    {
        $template->fields()->delete();

        foreach ($fields as $i => $field) {
            if (empty($field['label']) || empty($field['key'])) {
                continue;
            }

            // Parse options from newline-separated string
            $options = null;
            if (!empty($field['options'])) {
                $options = array_filter(array_map('trim', explode("\n", $field['options'])));
            }

            ContractTemplateField::create([
                'template_id' => $template->id,
                'label'       => $field['label'],
                'key'         => $field['key'],
                'type'        => $field['type'] ?? 'text',
                'placeholder' => $field['placeholder'] ?? null,
                'group'       => $field['group'] ?? null,
                'options'     => $options ?: null,
                'required'    => isset($field['required']) ? (bool) $field['required'] : true,
                'sort'        => $i,
            ]);
        }
    }
}
