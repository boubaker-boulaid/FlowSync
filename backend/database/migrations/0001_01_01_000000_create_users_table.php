<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulidColumn('id')->primary();
            $table->string('name', 100);
            $table->string('email', 255)->unique('uq_users_email');
            $table->dateTime('email_verified_at', 3)->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->boolean('is_demo')->default(false);
            $table->index('is_demo', 'idx_users_is_demo');
            $table->datetimes(3);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->dateTime('created_at', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
