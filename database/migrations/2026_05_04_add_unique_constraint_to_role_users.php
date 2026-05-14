<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueConstraintToRoleUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Remove duplicates keeping only the oldest record
        DB::statement("
            DELETE FROM role_users 
            WHERE id NOT IN (
                SELECT MIN(id) 
                FROM (
                    SELECT MIN(id) as id 
                    FROM role_users 
                    GROUP BY user_id, role_id, COALESCE(warehouse_id, 0)
                ) as temp
            )
        ");

        // Add unique constraint
        Schema::table('role_users', function (Blueprint $table) {
            $table->unique(['user_id', 'role_id', 'warehouse_id'], 'unique_user_role_warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('role_users', function (Blueprint $table) {
            $table->dropUnique('unique_user_role_warehouse');
        });
    }
}
