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

        if ($request->filled('website')) {
            return back()->with('success', 'Thank you for subscribing!');
        }

        $existing = Subscriber::where('email', $request->input('email'))->first();

        if ($existing?->isConfirmed()) {
            return back()->with('info', 'You are already subscribed.');
        }

        if (! $existing) {
            $subscriber = new Subscriber;
            $subscriber->email = $request->input('email');
            $subscriber->name = $request->input('name');
            $subscriber->token = Subscriber::generateToken();
            $subscriber->status = Subscriber::STATUS_PENDING;
            $subscriber->save();
        } else {
            $subscriber = $existing;
        }

        Mail::to($subscriber->email)->send(new NewsletterConfirm($subscriber));

        return back()->with('success', 'Check your email to confirm your subscription.');
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
