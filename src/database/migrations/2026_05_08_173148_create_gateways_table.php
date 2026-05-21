<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();

            // Driver key: manual, stripe, paypal, razorpay, etc.
            $table->string('driver', 32)->unique();

            $table->string('name');
            $table->boolean('enabled')->default(false);

            // Currency display & conversion
            $table->string('currency_code', 3)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->decimal('exchange_rate', 20, 8)->default(1.0);
            $table->tinyInteger('decimal_places')->default(2);
            $table->enum('symbol_position', ['before', 'after'])->default('before');

            // Encrypted credentials JSON (API keys, secrets, etc.)
            $table->text('credentials')->nullable();

            // Extra config (webhook secret, sandbox mode, etc.)
            $table->json('config')->nullable();

            // Min/max payment amounts
            $table->decimal('min_amount', 28, 8)->default(0);
            $table->decimal('max_amount', 28, 8)->default(0);

            // Fee: flat amount + percentage
            $table->decimal('fee_flat', 28, 8)->default(0);
            $table->decimal('fee_percent', 5, 2)->default(0.00);

            // Display order in payment picker
            $table->unsignedSmallInteger('sort')->default(0);

            // Instructions shown to user (for manual gateway)
            $table->text('instructions')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
