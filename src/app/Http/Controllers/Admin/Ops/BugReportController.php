<?php

namespace App\Http\Controllers\Admin\Ops;

use App\Domain\Ops\Models\BugReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BugReportController extends Controller
{
    public function index(): View
    {
        $reports = BugReport::with('admin')
            ->orderByRaw("FIELD(status, 'open', 'in_progress', 'resolved', 'wont_fix')")
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low')")
            ->paginate(20);

        return view('admin.ops.bug-reports.index', compact('reports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'severity' => 'required|in:low,medium,high,critical',
            'url' => 'nullable|url|max:500',
        ]);

        $report = new BugReport;
        $report->admin_id = auth('admin')->id();
        $report->title = $data['title'];
        $report->description = $data['description'];
        $report->severity = $data['severity'];
        $report->url = $data['url'] ?? null;
        $report->status = BugReport::STATUS_OPEN;
        $report->save();

        return redirect()->route('admin.ops.bug-reports.index')
            ->with('success', 'Bug report submitted.');
    }

    public function update(Request $request, BugReport $bugReport): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,wont_fix',
            'admin_note' => 'nullable|string|max:5000',
        ]);

        $bugReport->status = $data['status'];
        $bugReport->admin_note = $data['admin_note'] ?? null;
        $bugReport->save();

        return redirect()->route('admin.ops.bug-reports.index')
            ->with('success', 'Bug report updated.');
    }

    public function destroy(BugReport $bugReport): RedirectResponse
    {
        $bugReport->delete();

        return redirect()->route('admin.ops.bug-reports.index')
            ->with('success', 'Bug report deleted.');
    }
}
