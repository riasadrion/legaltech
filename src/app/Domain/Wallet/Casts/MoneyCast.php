<?php

namespace App\Domain\Wallet\Casts;

use App\Domain\Wallet\ValueObjects\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyCast implements CastsAttributes
{
    public function __construct(
        private string $currencyColumn = 'currency',
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        $currency = $attributes[$this->currencyColumn] ?? 'USD';

        return Money::of((float) ($value ?? 0), $currency);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value instanceof Money) {
            return [
                $key => $value->amount,
                $this->currencyColumn => $value->currency,
            ];
        }

        return [$key => (float) $value];
    }
}
