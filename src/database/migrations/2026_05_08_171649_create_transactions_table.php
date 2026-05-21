<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // type: credit | debit | adjustment | fee | refund | transfer_in | transfer_out
            $table->string('type', 32);

            // Positive = credit, negative = debit.
            $table->decimal('amount', 28, 8);

            // ISO 4217 currency code
            $table->string('currency', 3)->default('USD');

            // Running balance after this row — never recalculated, insert-only
            $table->decimal('balance_after', 28, 8);

            $table->string('description')->nullable();

            // Polymorphic source (payment id, payout id, etc.)
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Admin who triggered manual adjustments
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->json('meta')->nullable();

            // No updated_at — rows are immutable
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
