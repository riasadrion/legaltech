<?php

namespace App\Http\Controllers\Admin\Ops;

use App\Domain\Settings\Facades\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SchedulerController extends Controller
{
    private const REGISTERED_COMMANDS = [
        ['command' => 'blog:publish-scheduled', 'description' => 'Publish scheduled blog posts', 'frequency' => 'Hourly'],
        ['command' => 'wallet:reconcile',        'description' => 'Reconcile wallet balance cache', 'frequency' => 'Daily'],
        ['command' => 'queue:prune-failed',      'description' => 'Prune old failed queue jobs',   'frequency' => 'Daily'],
        ['command' => 'schedule:prune-batches',  'description' => 'Prune stale job batches',        'frequency' => 'Daily'],
    ];

    public function index(): View
    {
        $tasks = array_map(function (array $task) {
            $key = 'scheduler.last_run.'.str_replace([':', ' '], '_', $task['command']);
            $task['last_run'] = Settings::string($key);

            return $task;
        }, self::REGISTERED_COMMANDS);

        $cronLine = '* * * * * cd '.base_path().' && php artisan schedule:run >> /dev/null 2>&1';

        return view('admin.ops.scheduler', compact('tasks', 'cronLine'));
    }

    public function run(Request $request): RedirectResponse
    {
        $command = $request->input('command', '');

        $allowed = array_column(self::REGISTERED_COMMANDS, 'command');
        if (! in_array($command, $allowed, true)) {
            return redirect()->route('admin.ops.scheduler')->with('error', 'Unknown command.');
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();

            $key = 'scheduler.last_run.'.str_replace([':', ' '], '_', $command);
            Settings::set($key, now()->toDateTimeString(), 'string', 'scheduler');

            return redirect()->route('admin.ops.scheduler')
                ->with('success', "Command [{$command}] ran successfully.")
                ->with('command_output', trim($output));
        } catch (\Throwable $e) {
            return redirect()->route('admin.ops.scheduler')
                ->with('error', 'Command failed: '.$e->getMessage());
        }
    }
}
