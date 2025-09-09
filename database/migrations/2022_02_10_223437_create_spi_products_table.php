<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spi_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spi_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('from_warehouse');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('ppi_product_id');
            $table->unsignedBigInteger('ppi_id');
            $table->unsignedBigInteger('bundle_id')->nullable();
            $table->string('qty')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('price')->nullable();
            $table->string('note')->nullable();
            $table->unsignedBigInteger('action_performed_by');
            $table->string('any_warning_cls')->nullable();
            $table->timestamps();

            //Foreign Key Generate
            $table->foreign('spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('ppi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('bundle_id')->references('id')->on('ppi_bundle_products')->onDelete('cascade');
            $table->foreign('ppi_product_id')->references('id')->on('ppi_products')->onDelete('cascade');
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
        Schema::dropIfExists('spi_products');
    }
}
