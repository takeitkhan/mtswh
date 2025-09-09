<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppi_spi_id');
            $table->enum('action_format', ['Ppi', 'Spi']);
            $table->unsignedBigInteger('ppi_spi_product_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('bundle_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('original_barcode')->nullable();
            $table->string('product_unique_key')->nullable();
            $table->enum('stock_action', ['In', 'Out'])->nullable();
            $table->enum('stock_type', ['Existing', 'New', 'Purchase'])->nullable();
            $table->unsignedBigInteger('qty')->nullable();
            $table->date('entry_date')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('action_performed_by');

            $table->string('note')->nullable();

            $table->timestamps();

            //Foreign Key
            $table->foreign('ppi_spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('bundle_id')->references('id')->on('ppi_bundle_products')->onDelete('cascade');
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
        Schema::dropIfExists('product_stocks');
    }
}
