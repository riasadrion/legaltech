<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('contract_templates')->cascadeOnDelete();
            $table->string('label');
            $table->string('key');
            $table->enum('type', ['text', 'textarea', 'date', 'number', 'select', 'email'])->default('text');
            $table->string('placeholder')->nullable();
            $table->string('group')->nullable();
            $table->json('options')->nullable();
            $table->boolean('required')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_template_fields');
    }
};
