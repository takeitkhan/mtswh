<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ppi_products', function (Blueprint $table) {
            // Add soft delete column if it doesn't exist
            if (!Schema::hasColumn('ppi_products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ppi_products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
