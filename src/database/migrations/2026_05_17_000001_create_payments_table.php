<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gateway_id')->constrained();
            $table->decimal('amount', 28, 8);
            $table->string('currency', 3)->default('USD');
            $table->decimal('fee', 28, 8)->default(0);
            $table->string('status')->default('pending');
            $table->string('gateway_ref')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->cascadeOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('user_id');
        });

        // Add the deferred FK from user_subscriptions.payment_id
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
        });
        Schema::dropIfExists('payments');
    }
};
