<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSpiDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_spi_disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppi_spi_status_id');
            $table->unsignedBigInteger('ppi_spi_id');
            $table->enum('status_for', ['Ppi', 'Spi']);
            $table->unsignedBigInteger('ppi_spi_product_id');
            $table->string('issue_column')->nullable();
            $table->string('note')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('action_performed_by');
            $table->enum('action_format', ['Dispute', 'Correction'])->nullable();
            $table->string('correction_dispute_id')->nullable();
            $table->timestamps();


            //Foreign Key Generate
            //$table->foreign('ppi_spi_product_id')->references('id')->on('ppi_products')->onDelete('cascade');
            $table->foreign('ppi_spi_status_id')->references('id')->on('ppi_spi_statuses')->onDelete('cascade');
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
        Schema::dropIfExists('ppi_spi_disputes');
    }
}
