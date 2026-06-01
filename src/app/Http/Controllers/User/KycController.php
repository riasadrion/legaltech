<?php

namespace App\Http\Controllers\User;

use App\Domain\Payment\Models\KycSubmission;
use App\Domain\Upload\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function index()
    {
        $user = auth('user')->user();
        $submission = KycSubmission::where('user_id', $user->id)->latest()->first();

        return view('theme::user.kyc.index', compact('user', 'submission'));
    }

    public function create()
    {
        $user = auth('user')->user();

        if ($user->kyc_status === 'approved') {
            return redirect()->route('user.kyc.index');
        }

        return view('theme::user.kyc.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = auth('user')->user();

        if ($user->kyc_status === 'approved') {
            return redirect()->route('user.kyc.index');
        }

        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'id_type' => 'required|in:passport,national_id,drivers_license',
            'id_number' => 'required|string|max:60',
            'country_code' => 'required|string|size:2',
            'front_doc' => 'required|file|max:5120',
            'back_doc' => 'nullable|file|max:5120',
            'selfie' => 'required|file|max:5120',
        ]);

        $uploader = new FileUploader;
        $documents = [];

        foreach (['front_doc', 'back_doc', 'selfie'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $meta = $uploader->store($file, 'kyc', $user->id);
                $documents[] = ['field' => $field, 'path' => $meta->path, 'mime' => $meta->mime];
            }
        }

        DB::transaction(function () use ($user, $data, $documents) {
            $submission = new KycSubmission;
            $submission->user_id = $user->id;
            $submission->data = [
                'full_name' => $data['full_name'],
                'id_type' => $data['id_type'],
                'id_number' => $data['id_number'],
                'country_code' => $data['country_code'],
            ];
            $submission->documents = $documents;
            $submission->status = KycSubmission::STATUS_PENDING;
            $submission->save();

            $user->kyc_status = 'pending';
            $user->save();
        });

        return redirect()->route('user.kyc.index')
            ->with('success', 'Your KYC documents have been submitted and are under review.');
    }
}
