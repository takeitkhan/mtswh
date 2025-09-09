<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpiSpiHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppi_spi_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ppi_spi_id')->unsigned();
            $table->enum('action_format', ['Ppi', 'Spi']);
            $table->longText('chunck_old_data')->nullable();
            $table->longText('chunck_new_data')->nullable();
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->bigInteger('action_performed_by')->unsigned();
            $table->dateTime('action_time');
            $table->timestamps();

            $table->foreign('ppi_spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('ppi_spi_statuses')->onDelete('cascade');
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
        Schema::dropIfExists('ppi_spi_histories');
    }
}
