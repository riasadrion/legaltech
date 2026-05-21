<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();

            // Gateway identifier (stripe, paypal, etc.)
            $table->string('gateway', 32);

            // The event type string from the gateway (e.g. "payment_intent.succeeded")
            $table->string('event_type', 128);

            // Gateway's own unique event ID — used for idempotency
            $table->string('event_id', 255)->unique();

            // Raw payload stored for replay / audit
            $table->json('payload');

            // processed | failed | ignored
            $table->string('status', 16)->default('processed');

            $table->text('error')->nullable();

            $table->timestamps();

            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
