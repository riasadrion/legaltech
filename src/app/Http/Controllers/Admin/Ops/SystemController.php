<?php

namespace App\Http\Controllers\Admin\Ops;

use App\Domain\Settings\Facades\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function index(): View
    {
        $health = [
            'db' => $this->checkDb(),
            'storage' => $this->checkStorage(),
            'disk' => $this->checkDisk(),
            'mail' => null, // tested on demand
            'dns' => $this->checkDns(),
        ];

        $flushOptions = [
            ['key' => 'config',  'label' => 'Config Cache',  'command' => 'config:clear'],
            ['key' => 'routes',  'label' => 'Route Cache',   'command' => 'route:clear'],
            ['key' => 'views',   'label' => 'View Cache',    'command' => 'view:clear'],
            ['key' => 'cache',   'label' => 'Application Cache', 'command' => 'cache:clear'],
        ];

        return view('admin.ops.system', compact('health', 'flushOptions'));
    }

    public function flush(Request $request): RedirectResponse
    {
        $allowed = ['config:clear', 'route:clear', 'view:clear', 'cache:clear'];
        $command = $request->input('command', '');

        if (! in_array($command, $allowed, true)) {
            return redirect()->route('admin.system.index')->with('error', 'Invalid flush command.');
        }

        Artisan::call($command);

        $label = match ($command) {
            'config:clear' => 'Config',
            'route:clear' => 'Route',
            'view:clear' => 'View',
            'cache:clear' => 'Application',
            default => '',
        };

        return redirect()->route('admin.system.index')->with('success', "{$label} cache cleared.");
    }

    public function testMail(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $to = $request->input('email');
        $back = route('admin.settings.index').'#email';

        try {
            $appName = config('app.name');
            $domain  = parse_url(config('app.url'), PHP_URL_HOST);
            Mail::raw("Your mail configuration is working correctly on {$domain}.\n\nThis is a test message sent from {$appName}.", function ($msg) use ($to, $appName, $domain) {
                $msg->to($to)->subject("Mail configuration success on {$domain}");
            });

            return redirect($back)->with('success', "Test email sent to [{$to}].");
        } catch (\Throwable $e) {
            return redirect($back)->with('error', 'Mail send failed: '.$e->getMessage());
        }
    }

    // --- Health checks ---

    private function checkDb(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $ms = round((microtime(true) - $start) * 1000, 1);

            return ['status' => 'ok', 'label' => "Connected ({$ms} ms)"];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'label' => 'Connection failed: '.$e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        $driver = Settings::string('storage.driver', 'local');
        $disk = $driver === 'local' ? 'local' : $driver;

        try {
            $testPath = '_health_'.now()->timestamp.'.txt';
            Storage::disk($disk)->put($testPath, 'ok');
            Storage::disk($disk)->delete($testPath);

            return ['status' => 'ok', 'label' => ucfirst($driver).' — write/delete OK'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'label' => 'Storage error: '.$e->getMessage()];
        }
    }

    private function checkDisk(): array
    {
        $free = @disk_free_space(base_path());
        $total = @disk_total_space(base_path());

        if ($free === false || $total === false || $total === 0.0) {
            return ['status' => 'warn', 'label' => 'Disk info unavailable'];
        }

        $usedPct = round(($total - $free) / $total * 100, 1);
        $freeGb = round($free / 1024 / 1024 / 1024, 2);

        $status = $usedPct >= 90 ? 'error' : ($usedPct >= 75 ? 'warn' : 'ok');

        return ['status' => $status, 'label' => "{$usedPct}% used — {$freeGb} GB free"];
    }

    private function checkDns(): array
    {
        $domain = $this->fromDomain();
        if (! $domain) {
            return ['status' => 'warn', 'label' => 'From domain not configured'];
        }

        $spf = $this->checkSpf($domain);
        $dmarc = $this->checkDmarc($domain);

        $issues = array_filter([$spf ? null : 'SPF missing', $dmarc ? null : 'DMARC missing']);

        if (empty($issues)) {
            return ['status' => 'ok', 'label' => "SPF + DMARC found for {$domain}"];
        }

        return ['status' => 'warn', 'label' => implode(', ', $issues)." for {$domain}"];
    }

    private function fromDomain(): ?string
    {
        $from = Settings::string('mail.from_address', config('mail.from.address', ''));
        if (! $from || ! str_contains($from, '@')) {
            return null;
        }

        return explode('@', $from)[1];
    }

    private function checkSpf(string $domain): bool
    {
        $records = @dns_get_record($domain, DNS_TXT) ?: [];
        foreach ($records as $record) {
            if (str_contains($record['txt'] ?? '', 'v=spf1')) {
                return true;
            }
        }

        return false;
    }

    private function checkDmarc(string $domain): bool
    {
        $records = @dns_get_record('_dmarc.'.$domain, DNS_TXT) ?: [];
        foreach ($records as $record) {
            if (str_contains($record['txt'] ?? '', 'v=DMARC1')) {
                return true;
            }
        }

        return false;
    }
}
