<?php

namespace App\Http\Controllers\Admin\Notifications;

use App\Facades\Settings;
use Illuminate\View\View;

class EmailHealthController
{
    public function index(): View
    {
        $fromEmail = Settings::get('mail.from_address', config('mail.from.address', ''));
        $domain = $fromEmail ? substr(strrchr($fromEmail, '@'), 1) : null;
        $checks = $domain ? $this->runChecks($domain) : null;

        return view('admin.notifications.email-health', compact('domain', 'checks', 'fromEmail'));
    }

    private function runChecks(string $domain): array
    {
        return [
            'spf' => $this->checkSpf($domain),
            'dkim' => $this->checkDkim($domain),
            'dmarc' => $this->checkDmarc($domain),
        ];
    }

    private function checkSpf(string $domain): array
    {
        $records = dns_get_record($domain, DNS_TXT);
        $found = false;
        $value = null;

        foreach ($records as $r) {
            if (isset($r['txt']) && str_starts_with($r['txt'], 'v=spf1')) {
                $found = true;
                $value = $r['txt'];
                break;
            }
        }

        return [
            'found' => $found,
            'value' => $value,
            'status' => $found ? 'ok' : 'missing',
            'hint' => $found ? null : "Add a TXT record to {$domain}: v=spf1 include:_spf.yourmailprovider.com ~all",
        ];
    }

    private function checkDkim(string $domain): array
    {
        $selectors = ['default', 'google', 'mail', 'smtp', 'k1', 'dkim'];
        $found = false;
        $value = null;
        $selector = null;

        foreach ($selectors as $sel) {
            $lookup = "{$sel}._domainkey.{$domain}";
            $records = @dns_get_record($lookup, DNS_TXT);
            if (! empty($records)) {
                foreach ($records as $r) {
                    if (isset($r['txt']) && str_contains($r['txt'], 'p=')) {
                        $found = true;
                        $value = $r['txt'];
                        $selector = $sel;
                        break 2;
                    }
                }
            }
        }

        return [
            'found' => $found,
            'value' => $value,
            'selector' => $selector,
            'status' => $found ? 'ok' : 'missing',
            'hint' => $found ? null : "Add a TXT record to {$selectors[0]}._domainkey.{$domain} with your DKIM public key (provided by your email sender).",
        ];
    }

    private function checkDmarc(string $domain): array
    {
        $lookup = "_dmarc.{$domain}";
        $records = @dns_get_record($lookup, DNS_TXT);
        $found = false;
        $value = null;

        foreach ($records as $r) {
            if (isset($r['txt']) && str_starts_with($r['txt'], 'v=DMARC1')) {
                $found = true;
                $value = $r['txt'];
                break;
            }
        }

        return [
            'found' => $found,
            'value' => $value,
            'status' => $found ? 'ok' : 'missing',
            'hint' => $found ? null : "Add a TXT record to _dmarc.{$domain}: v=DMARC1; p=none; rua=mailto:dmarc@{$domain}",
        ];
    }
}
