<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('causer')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('causer')) {
            $query->where('causer_type', 'App\\Models\\Admin')
                ->whereHas('causer', fn ($q) => $q->where('email', 'like', '%'.$request->causer.'%'));
        }

        $logs = $query->paginate(50)->withQueryString();
        $actions = ActivityLog::distinct()->orderBy('action')->pluck('action');

        return view('admin.activity-log.index', compact('logs', 'actions'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['causer', 'subject']);

        return view('admin.activity-log.show', ['log' => $activityLog]);
    }
}
