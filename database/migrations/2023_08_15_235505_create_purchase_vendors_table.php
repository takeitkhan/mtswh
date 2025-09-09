<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spi_id');
            $table->unsignedBigInteger('spi_product_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('vendor_name');
            $table->unsignedBigInteger('warehouse_id');
            $table->string('qty');
            $table->string('price')->nullable()->default(0);
            $table->unsignedBigInteger('create_ppi_id')->nullable();
            $table->unsignedBigInteger('create_ppi_product_id')->nullable();
            $table->unsignedBigInteger('action_performed_by');
            $table->timestamps();

            $table->foreign('spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('action_performed_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('spi_product_id')->references('id')->on('spi_products')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('create_ppi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('create_ppi_product_id')->references('id')->on('ppi_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_vendors');
    }
}
