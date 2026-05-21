<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payout_method_id')->constrained();
            $table->decimal('amount', 28, 8);
            $table->string('currency', 3)->default('USD');
            $table->decimal('fee', 28, 8)->default(0);
            $table->string('status')->default('pending');
            $table->json('payout_details')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->cascadeOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->cascadeOnDelete();
            $table->timestamps();
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
