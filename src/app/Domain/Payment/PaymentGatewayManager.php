<?php

namespace App\Domain\Payment;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Models\Gateway;
use RuntimeException;

class PaymentGatewayManager
{
    /** @var array<string, PaymentGateway> */
    private array $drivers = [];

    public function register(PaymentGateway $driver): void
    {
        $this->drivers[$driver->driver()] = $driver;
    }

    public function driver(string $key): PaymentGateway
    {
        if (! isset($this->drivers[$key])) {
            throw new RuntimeException("Payment driver [{$key}] is not registered.");
        }

        return $this->drivers[$key];
    }

    public function forGateway(Gateway $gateway): PaymentGateway
    {
        return $this->driver($gateway->driver);
    }

    /** @return array<string, PaymentGateway> */
    public function all(): array
    {
        return $this->drivers;
    }

    public function registeredDriverKeys(): array
    {
        return array_keys($this->drivers);
    }
}
