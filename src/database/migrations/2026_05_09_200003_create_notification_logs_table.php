<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('notification_key');
            $table->string('channel');
            $table->nullableMorphs('notifiable');
            $table->string('recipient')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->string('status')->default('sent');
            $table->text('error')->nullable();
            $table->string('locale', 10)->default('en');
            $table->timestamps();

            $table->index(['notification_key', 'channel']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
