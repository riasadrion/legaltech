<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value')->nullable();
            $table->enum('type', ['string', 'boolean', 'integer', 'float', 'array', 'json'])->default('string');
            $table->string('group')->default('general');
            $table->boolean('autoload')->default(true);
            $table->timestamps();

            $table->index(['group', 'autoload']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
