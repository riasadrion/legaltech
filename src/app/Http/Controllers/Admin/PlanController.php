<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Settings;
use App\Domain\Subscription\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query()->orderBy('sort');

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%");
        }

        $plans = $query->paginate(25)->withQueryString();

        $activeCount = Plan::where('is_active', true)->count();
        $inactiveCount = Plan::where('is_active', false)->count();

        $statusPills = [
            ['value' => '',        'label' => 'All',      'count' => $activeCount + $inactiveCount],
            ['value' => 'active',  'label' => 'Active',   'count' => $activeCount],
            ['value' => 'inactive', 'label' => 'Inactive', 'count' => $inactiveCount],
        ];

        return view('admin.plans.index', compact('plans', 'statusPills'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:120'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:per_contract,monthly,yearly,lifetime'],
            'credits'       => ['required', 'integer', 'min:0'],
            'ai_contract_generation' => ['boolean'],
            'ai_drafts'     => ['nullable', 'integer', 'min:0'],
            'features'      => ['nullable', 'array'],
            'features.*'    => ['string', 'max:200'],
            'is_featured'   => ['boolean'],
            'cta_text'      => ['nullable', 'string', 'max:100'],
            'cta_url'       => ['nullable', 'string', 'max:500'],
            'is_active'     => ['boolean'],
            'sort'          => ['required', 'integer', 'min:0'],
        ]);

        $data['features']    = array_values(array_filter(array_map('trim', $data['features'] ?? []), fn ($f) => $f !== ''));
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);
        $data['ai_contract_generation'] = $request->boolean('ai_contract_generation');
        $data['ai_drafts'] = (int) ($data['ai_drafts'] ?? 0);

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:120'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:per_contract,monthly,yearly,lifetime'],
            'credits'       => ['required', 'integer', 'min:0'],
            'ai_contract_generation' => ['boolean'],
            'ai_drafts'     => ['nullable', 'integer', 'min:0'],
            'features'      => ['nullable', 'array'],
            'features.*'    => ['string', 'max:200'],
            'is_featured'   => ['boolean'],
            'cta_text'      => ['nullable', 'string', 'max:100'],
            'cta_url'       => ['nullable', 'string', 'max:500'],
            'is_active'     => ['boolean'],
            'sort'          => ['required', 'integer', 'min:0'],
        ]);

        $data['features']    = array_values(array_filter(array_map('trim', $data['features'] ?? []), fn ($f) => $f !== ''));
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);
        $data['ai_contract_generation'] = $request->boolean('ai_contract_generation');
        $data['ai_drafts'] = (int) ($data['ai_drafts'] ?? 0);

        $plan->update($data);

        return redirect()->route('admin.plans.edit', $plan)->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->active()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete a plan with active subscriptions.']);
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted.');
    }

    public function toggle(Plan $plan)
    {
        $plan->is_active = ! $plan->is_active;
        $plan->save();

        return back()->with('success', 'Plan status updated.');
    }
}
