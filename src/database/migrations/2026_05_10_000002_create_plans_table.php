<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 28, 8);
            $table->enum('billing_cycle', ['per_contract', 'monthly', 'yearly', 'lifetime']);
            $table->unsignedInteger('credits')->default(0);
            $table->unsignedInteger('trial_days')->default(0);
            $table->boolean('ai_contract_generation')->default(false);
            $table->unsignedInteger('ai_drafts')->default(0);
            $table->json('features')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
