<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Http\Controllers\Controller;
use App\Support\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        $page = Page::where('slug', 'contact')->published()->firstOrFail();
        $sections = $page->enabledSections();

        return view('theme::cms.page', compact('page', 'sections'));
    }

    public function submit(Request $request)
    {
        // Honeypot
        if ($request->filled('website')) {
            return back()->with('success', 'Message sent!');
        }

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:1'], // honeypot
        ];

        if (Turnstile::enabled()) {
            $rules['cf-turnstile-response'] = ['required', function ($_, $value, $fail) use ($request) {
                if (! Turnstile::verify($value, $request->ip())) {
                    $fail(__('Captcha verification failed. Please try again.'));
                }
            }];
        }

        $data = $request->validate($rules);

        Mail::raw(
            "Name: {$data['name']}\nEmail: {$data['email']}\n\n{$data['message']}",
            fn ($m) => $m
                ->to(config('mail.from.address'))
                ->subject("Contact: {$data['subject']}")
                ->replyTo($data['email'], $data['name'])
        );

        return back()->with('contact_success', true);
    }
}
