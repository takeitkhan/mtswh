<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporaryStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('action_format');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('ppi_spi_id');
            $table->unsignedBigInteger('ppi_product_id')->nullable();
            $table->unsignedBigInteger('spi_product_id')->nullable();
            $table->string('waiting_stock_in')->default(0);
            $table->string('waiting_stock_out')->default(0);
            $table->unsignedBigInteger('warehouse_id');
            $table->timestamps();

            //Foreign
            $table->foreign('ppi_spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('ppi_product_id')->references('id')->on('ppi_products')->onDelete('cascade');
            $table->foreign('spi_product_id')->references('id')->on('spi_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_stocks');
    }
}
