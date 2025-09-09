<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSetProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_set_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppi_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->string('set_name');
            $table->string('ppi_product_id');
            $table->unsignedBigInteger('action_performed_by');
            $table->timestamps();

            //Foreign Key Generate
            $table->foreign('ppi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('action_performed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ppi_set_products');
    }
}
