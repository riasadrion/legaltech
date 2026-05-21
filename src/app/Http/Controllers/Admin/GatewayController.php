<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\PaymentGatewayManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function index(PaymentGatewayManager $manager)
    {
        $gateways = Gateway::orderBy('sort')->get()->keyBy('driver');
        $registeredDrivers = $manager->registeredDriverKeys();

        return view('admin.gateways.index', compact('gateways', 'registeredDrivers', 'manager'));
    }

    public function edit(string $driver, PaymentGatewayManager $manager)
    {
        $gatewayDriver = $manager->driver($driver);
        $gateway = Gateway::firstOrNew(['driver' => $driver], [
            'name' => $gatewayDriver->name(),
            'enabled' => false,
        ]);

        return view('admin.gateways.edit', compact('gateway', 'gatewayDriver'));
    }

    public function update(Request $request, string $driver, PaymentGatewayManager $manager)
    {
        $manager->driver($driver); // ensure driver exists

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'enabled' => ['boolean'],
            'currency_code' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:8'],
            'symbol_position' => ['required', 'in:before,after'],
            'min_amount' => ['required', 'numeric', 'min:0.01'],
            'max_amount' => ['required', 'numeric', 'gt:min_amount'],
            'fee_flat' => ['required', 'numeric', 'min:0'],
            'fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'sort' => ['required', 'integer', 'min:0'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'config' => ['nullable', 'array'],
            'credentials' => ['nullable', 'array'],
        ]);

        $gateway = Gateway::firstOrNew(['driver' => $driver]);
        $gateway->driver = $driver;
        $gateway->name = $data['name'];
        $gateway->enabled = $request->boolean('enabled');
        $gateway->currency_code = strtoupper($data['currency_code']);
        $gateway->currency_symbol = $data['currency_symbol'];
        $gateway->exchange_rate = $data['exchange_rate'];
        $gateway->decimal_places = $data['decimal_places'];
        $gateway->symbol_position = $data['symbol_position'];
        $gateway->min_amount = $data['min_amount'];
        $gateway->max_amount = $data['max_amount'];
        $gateway->fee_flat = $data['fee_flat'];
        $gateway->fee_percent = $data['fee_percent'];
        $gateway->sort = $data['sort'];
        $gateway->instructions = $data['instructions'] ?? null;
        $gateway->config = $data['config'] ?? null;

        // Only update credentials if any non-empty values were submitted
        $creds = array_filter($data['credentials'] ?? [], fn ($v) => $v !== '' && $v !== null);
        if (! empty($creds)) {
            // Merge with existing credentials so masked fields aren't overwritten with empty
            $existing = $gateway->exists ? $gateway->getCredentials() : [];
            $gateway->setCredentials(array_merge($existing, $creds));
        }

        $gateway->save();

        return redirect()->route('admin.gateways.index')->with('success', "{$gateway->name} settings saved.");
    }

    public function toggleEnabled(Gateway $gateway)
    {
        $gateway->enabled = ! $gateway->enabled;
        $gateway->save();

        $state = $gateway->enabled ? 'enabled' : 'disabled';

        return back()->with('success', "{$gateway->name} {$state}.");
    }
}
