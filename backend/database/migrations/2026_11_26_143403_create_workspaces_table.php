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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->ulidColumn('id')->primary();
            $table->string('name', 100);
            $table->dateTime('demo_expires_at', 3)->nullable();
            $table->dateTime('deleted_at', 3)->nullable();
            $table->datetimes(3);
            $table->index('demo_expires_at', 'idx_workspaces_demo_expires_at');
            $table->index('deleted_at', 'idx_workspaces_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
