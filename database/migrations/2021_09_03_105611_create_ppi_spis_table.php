<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_spis', function (Blueprint $table) {
            $table->id();
            $table->enum('action_format', ['Ppi', 'Spi']);
            $table->enum('ppi_spi_type', ['Supply', 'Service', 'Other']);
            $table->string('project');
            $table->enum('tran_type', ['With Money', 'Without Money', 'Other']);
            $table->string('note')->nullable();
            $table->string('transferable')->nullable();
            $table->string('purchase')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('action_performed_by');
            $table->timestamps();

            //Foreign Key Generate
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
        Schema::dropIfExists('ppi_spis');
    }
}
