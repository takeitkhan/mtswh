<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpiTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spi_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spi_id');
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('ppi_id');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spi_transfers');
    }
}
