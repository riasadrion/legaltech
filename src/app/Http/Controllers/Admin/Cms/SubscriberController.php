<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Domain\Cms\Models\Subscriber;
use App\Http\Controllers\Controller;
use App\Mail\NewsletterBroadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = Subscriber::query()
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('search'), fn ($q, $s) => $q->where('email', 'like', "%{$s}%"))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.cms.subscribers.index', compact('subscribers'));
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed.');
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'offset' => ['integer', 'min:0'],
        ]);

        $offset = (int) $request->input('offset', 0);
        $chunk = 50;

        $subscribers = Subscriber::confirmed()
            ->skip($offset)
            ->take($chunk)
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(
                new NewsletterBroadcast(
                    $request->input('subject'),
                    $request->input('body'),
                    $subscriber->token
                )
            );
        }

        $nextOffset = $offset + $subscribers->count();
        $total = Subscriber::confirmed()->count();
        $done = $nextOffset >= $total;

        if ($request->wantsJson()) {
            return response()->json([
                'done' => $done,
                'sent' => $subscribers->count(),
                'next_offset' => $done ? null : $nextOffset,
                'total' => $total,
            ]);
        }

        if ($done) {
            return redirect()->route('admin.cms.subscribers.index')->with('success', "Newsletter sent to {$total} subscribers.");
        }

        // Chunked-redirect: browser re-posts with next offset
        return redirect()->route('admin.cms.subscribers.broadcast.send', [
            'offset' => $nextOffset,
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
        ]);
    }

    public function broadcastForm()
    {
        $total = Subscriber::confirmed()->count();

        return view('admin.cms.subscribers.broadcast', compact('total'));
    }
}
