<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppi_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_id');
            $table->string('qty')->nullable();
            $table->string('unit_price')->nullable()->default(0);
            $table->string('price')->nullable()->default(0);
            $table->enum('product_state', ['New','Used', 'Cut-Piece'])->nullable();
            $table->enum('health_status', ['Useable','Scrapped'])->nullable();
            $table->string('note')->nullable();
            $table->unsignedBigInteger('action_performed_by');
            $table->timestamps();

             //Foreign Key Generate
            $table->foreign('ppi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('ppi_products');
    }
}
