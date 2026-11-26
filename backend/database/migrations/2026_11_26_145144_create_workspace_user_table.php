<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workspace_user', function (Blueprint $table) {
            $table->ulidColumn('id')->primary();
            $table->ulidColumn('workspace_id');
            $table->ulidColumn('user_id');
            $table->string('role', 16);
            $table->tinyInteger('owner_marker')->storedAs("IF(role = 'owner', 1, NULL)");
            $table->datetimes(3);
            $table->unique(['workspace_id', 'user_id'], 'uq_workspace_user_ws_user');
            $table->unique(['workspace_id', 'owner_marker'], 'uq_workspace_user_one_owner'); // at most one owner per Workspace
            $table->index('user_id', 'idx_workspace_user_user');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        DB::statement("ALTER TABLE workspace_user ADD CONSTRAINT ck_workspace_user_role CHECK (role IN ('owner','admin','builder','operator','viewer'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_user');
    }
};
