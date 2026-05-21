<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Cms\Models\Subscriber;
use App\Http\Controllers\Controller;
use App\Mail\NewsletterConfirm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:200'],
            'name' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:1'],  // honeypot
        ]);

        $wantsJson = $request->expectsJson() || $request->ajax();

        if ($request->filled('website')) {
            $msg = __('Thank you for subscribing!');
            return $wantsJson ? response()->json(['status' => 'success', 'message' => $msg]) : back()->with('success', $msg);
        }

        $subscriber = Subscriber::where('email', $request->input('email'))->first();

        // Already a confirmed subscriber — nothing to do.
        if ($subscriber?->isConfirmed()) {
            $msg = __('You are already subscribed.');
            return $wantsJson ? response()->json(['status' => 'info', 'message' => $msg]) : back()->with('info', $msg);
        }

        // Returning an unsubscribed user — reactivate the same row.
        // Pending or new — create or refresh token and re-send confirmation.
        if (! $subscriber) {
            $subscriber = new Subscriber;
            $subscriber->email = $request->input('email');
        }

        $subscriber->name = $request->input('name') ?: $subscriber->name;
        $subscriber->token = Subscriber::generateToken();
        $subscriber->status = Subscriber::STATUS_PENDING;
        $subscriber->confirmed_at = null;
        $subscriber->save();

        Mail::to($subscriber->email)->send(new NewsletterConfirm($subscriber));

        $msg = __('Check your email to confirm your subscription.');
        return $wantsJson ? response()->json(['status' => 'success', 'message' => $msg]) : back()->with('success', $msg);
    }

    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('token', $token)
            ->where('status', Subscriber::STATUS_PENDING)
            ->firstOrFail();

        $subscriber->status = Subscriber::STATUS_CONFIRMED;
        $subscriber->confirmed_at = now();
        $subscriber->save();

        return redirect('/')->with('success', 'You are now subscribed!');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        $subscriber->status = Subscriber::STATUS_UNSUBSCRIBED;
        $subscriber->save();

        return redirect('/')->with('success', 'You have been unsubscribed.');
    }
}
