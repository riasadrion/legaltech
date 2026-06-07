<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Domain\Contract\Models\ContractTemplate;
use App\Domain\Contract\Models\ContractTemplateCategory;
use App\Domain\Contract\Models\ContractTemplateField;
use App\Domain\Contract\Services\ContractAiService;
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
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        $templates = $query->paginate(20)->withQueryString();
        $categories = ContractTemplateCategory::orderBy('sort')->get();

        return view('admin.contracts.templates.index', compact('templates', 'categories'));
    }

    public function create()
    {
        $categories = ContractTemplateCategory::orderBy('sort')->get();

        return view('admin.contracts.templates.create', compact('categories'));
    }

    public function store(Request $request, ContractAiService $ai)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:contract_template_categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'credits_cost' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'integer'],
        ]);

        $data['active'] = $request->boolean('active');

        try {
            $data['body'] = $ai->buildTemplate($data['description']);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['description' => 'AI generation failed: '.$e->getMessage()]);
        }

        $template = ContractTemplate::create($data);

        $this->syncFieldsFromBody($template);

        return redirect()->route('admin.contracts.templates.edit', $template)
            ->with('success', 'Template generated. Review the body and fields below.');
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
            'category_id' => ['required', 'exists:contract_template_categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'body' => ['required', 'string'],
            'credits_cost' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'integer'],
            'fields' => ['nullable', 'array'],
            'fields.*.label' => ['required_with:fields', 'string', 'max:100'],
            'fields.*.key' => ['required_with:fields', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/'],
            'fields.*.type' => ['required_with:fields', 'in:text,textarea,date,number,select,email'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:200'],
            'fields.*.group' => ['nullable', 'string', 'max:100'],
            'fields.*.options' => ['nullable', 'string'],
            'fields.*.required' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');

        $template->update($data);

        if ($request->has('fields')) {
            $this->syncFieldsManual($template, $request->input('fields', []));
        } else {
            $this->syncFieldsFromBody($template);
        }

        return redirect()->route('admin.contracts.templates.index')
            ->with('success', 'Template updated.');
    }

    public function regenerate(ContractTemplate $template, ContractAiService $ai)
    {
        if (! $template->description) {
            return back()->withErrors(['regenerate' => 'Template has no description to regenerate from.']);
        }

        try {
            $body = $ai->buildTemplate($template->description);
        } catch (\Throwable $e) {
            return back()->withErrors(['regenerate' => 'AI generation failed: '.$e->getMessage()]);
        }

        $template->update(['body' => $body]);
        $this->syncFieldsFromBody($template);

        return back()->with('success', 'Template regenerated.');
    }

    public function destroy(ContractTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.contracts.templates.index')
            ->with('success', 'Template deleted.');
    }

    public function toggle(ContractTemplate $template)
    {
        $template->update(['active' => ! $template->active]);

        return back()->with('success', 'Template '.($template->active ? 'activated' : 'deactivated').'.');
    }

    private function syncFieldsFromBody(ContractTemplate $template): void
    {
        preg_match_all('/\{\{([a-z0-9_]+)\}\}/i', $template->body ?? '', $matches);
        $keys = array_values(array_unique($matches[1] ?? []));

        // Drop fields whose placeholder no longer exists in the body
        $template->fields()->whereNotIn('key', $keys ?: [''])->delete();

        $existing = $template->fields()->get()->keyBy('key');

        foreach ($keys as $i => $key) {
            if ($existing->has($key)) {
                // Preserve admin customizations; just resync sort order
                $existing[$key]->update(['sort' => $i]);

                continue;
            }

            ContractTemplateField::create([
                'template_id' => $template->id,
                'label' => ucwords(str_replace('_', ' ', $key)),
                'key' => $key,
                'type' => $this->guessType($key),
                'placeholder' => null,
                'group' => null,
                'options' => null,
                'required' => true,
                'sort' => $i,
            ]);
        }
    }

    private function syncFieldsManual(ContractTemplate $template, array $fields): void
    {
        $template->fields()->delete();

        foreach ($fields as $i => $field) {
            if (empty($field['label']) || empty($field['key'])) {
                continue;
            }

            $options = null;
            if (! empty($field['options'])) {
                $options = array_filter(array_map('trim', explode("\n", $field['options'])));
            }

            ContractTemplateField::create([
                'template_id' => $template->id,
                'label' => $field['label'],
                'key' => $field['key'],
                'type' => $field['type'] ?? 'text',
                'placeholder' => $field['placeholder'] ?? null,
                'group' => $field['group'] ?? null,
                'options' => $options ?: null,
                'required' => isset($field['required']) ? (bool) $field['required'] : true,
                'sort' => $i,
            ]);
        }
    }

    private function guessType(string $key): string
    {
        $k = strtolower($key);
        if (str_contains($k, 'date')) {
            return 'date';
        }
        if (str_contains($k, 'email')) {
            return 'email';
        }
        if (str_contains($k, 'amount') || str_contains($k, 'price') ||
            str_contains($k, 'share') || str_contains($k, 'percent') ||
            str_contains($k, 'years') || str_contains($k, 'months') ||
            str_contains($k, 'count') || str_contains($k, 'number')) {
            return 'number';
        }
        if (str_contains($k, 'address') || str_contains($k, 'description') ||
            str_contains($k, 'notes') || str_contains($k, 'details')) {
            return 'textarea';
        }

        return 'text';
    }
}
