<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->integer('version')->default(1);
            $table->json('subject')->nullable();
            $table->json('email_body')->nullable();
            $table->json('sms_body')->nullable();
            $table->timestamps();

            $table->index(['notification_template_id', 'version'], 'notif_template_versions_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_template_versions');
    }
};
