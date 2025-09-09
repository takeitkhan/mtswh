<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSpiSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_spi_sources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppi_spi_id');
            $table->enum('action_format', ['Ppi', 'Spi']);
            $table->string('source_type')->nullable();
            $table->string('who_source')->nullable();
            $table->unsignedBigInteger('who_source_id')->unsigned()->nullable();
            $table->string('levels')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->timestamps();

            //foreign key
            $table->foreign('ppi_spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('who_source_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ppi_spi_sources');
    }
}
