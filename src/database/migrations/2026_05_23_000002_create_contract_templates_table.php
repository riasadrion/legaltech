<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('contract_template_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            $table->text('ai_system_prompt')->nullable();
            $table->unsignedInteger('credits_cost')->default(1);
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_templates');
    }
};
