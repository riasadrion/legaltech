<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('contract_templates')->nullOnDelete();
            $table->string('title');
            $table->json('field_data')->nullable();
            $table->longText('body_html')->nullable();
            $table->enum('status', ['draft', 'generating', 'editing', 'ready', 'sent', 'signed', 'expired'])->default('draft');
            $table->string('share_token')->unique()->nullable();
            $table->unsignedInteger('credits_used')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
