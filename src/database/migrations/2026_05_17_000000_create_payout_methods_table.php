<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->nullable()->constrained()->nullOnDelete();
            $table->json('name');
            $table->json('delay_text')->nullable();
            $table->decimal('min_amount', 28, 8)->default(0);
            $table->decimal('max_amount', 28, 8)->default(0);
            $table->decimal('fee_flat', 28, 8)->default(0);
            $table->decimal('fee_percent', 8, 4)->default(0);
            $table->boolean('kyc_required')->default(false);
            $table->boolean('enabled')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->index('enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_methods');
    }
};
