<?php

namespace App\Http\Controllers\Admin\Notifications;

use App\Services\HtmlPurifier;
use App\Models\Language;
use App\Notifications\Channels\EmailChannel;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateVersion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController
{
    public function index(): View
    {
        $templates = NotificationTemplate::orderBy('name')->get();

        return view('admin.notifications.templates.index', compact('templates'));
    }

    public function edit(NotificationTemplate $template): View
    {
        $versions = $template->versions()->orderByDesc('version')->limit(10)->get();

        return view('admin.notifications.templates.edit', compact('template', 'versions'));
    }

    public function update(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $languages = Language::all();

        $subject = [];
        $emailBody = [];
        $smsBody = [];

        foreach ($languages as $lang) {
            $code = $lang->code;
            $subject[$code] = $request->input("subject.{$code}", '');
            $emailBody[$code] = HtmlPurifier::purify($request->input("email_body.{$code}", ''));
            $smsBody[$code] = $request->input("sms_body.{$code}", '');
        }

        $this->snapshotVersion($template, $request->user('admin'));

        $template->subject = $subject;
        $template->email_body = $emailBody;
        $template->sms_body = $smsBody;
        $template->channels = $request->input('channels', ['email']);
        $template->is_active = $request->boolean('is_active', true);
        $template->save();

        return redirect()->route('admin.notifications.templates.edit', $template)->with('success', 'Template saved.');
    }

    public function testSend(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $request->validate(['test_email' => 'required|email']);

        $fakeUser = new User;
        $fakeUser->email = $request->input('test_email');
        $fakeUser->name = 'Test User';

        try {
            app(EmailChannel::class)->send(
                $fakeUser,
                $template,
                ['user.name' => 'Test User', 'user.email' => $request->input('test_email'), 'app.name' => config('app.name'), 'app.url' => config('app.url')],
                app()->getLocale()
            );
            $msg = 'Test email sent to '.$request->input('test_email');
        } catch (\Throwable $e) {
            $msg = 'Failed: '.$e->getMessage();

            return redirect()->back()->with('error', $msg);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function rollback(Request $request, NotificationTemplate $template, NotificationTemplateVersion $version): RedirectResponse
    {
        $this->snapshotVersion($template, $request->user('admin'));

        $template->subject = $version->subject;
        $template->email_body = $version->email_body;
        $template->sms_body = $version->sms_body;
        $template->save();

        return redirect()->route('admin.notifications.templates.edit', $template)->with('success', 'Rolled back to version '.$version->version.'.');
    }

    private function snapshotVersion(NotificationTemplate $template, ?object $admin): void
    {
        $latestVersion = $template->versions()->max('version') ?? 0;

        $version = new NotificationTemplateVersion;
        $version->notification_template_id = $template->id;
        $version->created_by = $admin?->id;
        $version->version = $latestVersion + 1;
        $version->subject = $template->subject;
        $version->email_body = $template->email_body;
        $version->sms_body = $template->sms_body;
        $version->save();
    }
}
