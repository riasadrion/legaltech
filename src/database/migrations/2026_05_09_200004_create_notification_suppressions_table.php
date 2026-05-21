<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('reason');
            $table->string('source')->default('manual');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_suppressions');
    }
};
