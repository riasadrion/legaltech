<?php

namespace App\Domain\Wallet\ValueObjects;

use InvalidArgumentException;

/**
 * Money value object that stores amounts as decimal (28,8).
 * Supports both fiat (2 decimals) and crypto (8 decimals).
 */
final class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency = 'USD',
    ) {
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException("Currency must be a 3-letter ISO 4217 code, got: {$currency}");
        }
    }

    public static function of(float $amount, string $currency = 'USD'): self
    {
        return new self($amount, strtoupper($currency));
    }

    public static function zero(string $currency = 'USD'): self
    {
        return new self(0.0, strtoupper($currency));
    }

    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isZero(): bool
    {
        return $this->amount == 0.0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function toFloat(): float
    {
        return $this->amount;
    }

    /**
     * Format amount with proper decimals based on currency type.
     * Fiat: 2 decimals, Crypto: 8 decimals
     */
    public function format(bool $withSymbol = true): string
    {
        $decimals = isCryptoCurrency($this->currency) ? 8 : 2;
        $formatted = number_format($this->amount, $decimals);
        
        if (! $withSymbol) {
            return $formatted;
        }
        
        $symbol = setting('site.currency_symbol', '$');
        return $symbol . $formatted;
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Currency mismatch: {$this->currency} vs {$other->currency}"
            );
        }
    }
}
