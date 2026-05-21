<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Models\KycSubmission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $submissions = KycSubmission::with('user')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = KycSubmission::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        $statusPills = [
            ['value' => 'all',      'label' => 'All',       'count' => $counts->sum()],
            ['value' => 'pending',  'label' => 'Pending',   'count' => $counts['pending'] ?? 0],
            ['value' => 'approved', 'label' => 'Approved',  'count' => $counts['approved'] ?? 0],
            ['value' => 'rejected', 'label' => 'Rejected',  'count' => $counts['rejected'] ?? 0],
        ];

        return view('admin.kyc.index', compact('submissions', 'statusPills', 'status'));
    }

    public function show(KycSubmission $submission)
    {
        $submission->load(['user', 'reviewer']);

        return view('admin.kyc.show', compact('submission'));
    }

    public function approve(KycSubmission $submission)
    {
        if (! $submission->isPending()) {
            return back()->with('error', 'Submission is not pending.');
        }

        $submission->status = KycSubmission::STATUS_APPROVED;
        $submission->reviewed_by = auth('admin')->id();
        $submission->reviewed_at = now();
        $submission->save();

        $submission->user->kyc_status = 'approved';
        $submission->user->save();

        return back()->with('success', 'KYC submission approved.');
    }

    public function reject(Request $request, KycSubmission $submission)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        if (! $submission->isPending()) {
            return back()->with('error', 'Submission is not pending.');
        }

        $submission->status = KycSubmission::STATUS_REJECTED;
        $submission->rejection_reason = $request->input('reason');
        $submission->reviewed_by = auth('admin')->id();
        $submission->reviewed_at = now();
        $submission->save();

        $submission->user->kyc_status = 'rejected';
        $submission->user->save();

        return back()->with('success', 'KYC submission rejected.');
    }

    public function document(Request $request, KycSubmission $submission, int $index)
    {
        $documents = $submission->documents ?? [];
        $doc = $documents[$index] ?? null;

        if (! $doc || ! isset($doc['path'])) {
            abort(404);
        }

        $path = $doc['path'];

        if (! Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('private')->path($path),
            ['Content-Disposition' => 'inline']
        );
    }
}
