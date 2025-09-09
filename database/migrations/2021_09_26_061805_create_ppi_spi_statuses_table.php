<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSpiStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_spi_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppi_spi_id');
            $table->enum('status_for', ['Ppi', 'Spi']);
            $table->unsignedBigInteger('warehouse_id');
            $table->string('code');
            $table->unsignedBigInteger('action_performed_by');
            $table->integer('status_order')->nullable();
            $table->string('message')->nullable();
            $table->enum('status_type',['success', 'warning', 'danger', 'info', 'purple']);
            $table->enum('status_format',['Main', 'Optional'])->nullable();
            $table->longText('note')->nullable();
            $table->string('ppi_spi_product_id')->nullable();
            $table->timestamps();

            //Foreign Key Generate
            $table->foreign('ppi_spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
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
        Schema::dropIfExists('ppi_spi_statuses');
    }
}
