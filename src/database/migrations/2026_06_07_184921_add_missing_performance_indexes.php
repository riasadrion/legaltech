<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
            $table->index('kyc_status');
            $table->index('created_at');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['kyc_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
