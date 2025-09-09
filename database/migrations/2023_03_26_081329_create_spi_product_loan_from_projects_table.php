<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpiProductLoanFromProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spi_product_loan_from_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spi_id');
            $table->unsignedBigInteger('spi_product_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('ppi_id');
            $table->unsignedBigInteger('ppi_product_id');
            $table->string('original_project')->nullable();
            $table->string('original_project_id')->nullable();
            $table->string('landed_project')->nullable();
            $table->string('landed_project_id')->nullable();
            $table->string('qty')->default(0);
            $table->enum('status', ['processing', 'done'])->nullable()->default('processing');
            $table->timestamps();

            //Foreign Key Generate
            $table->foreign('spi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('ppi_id')->references('id')->on('ppi_spis')->onDelete('cascade');
            $table->foreign('spi_product_id')->references('id')->on('spi_products')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('ppi_product_id')->references('id')->on('ppi_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spi_product_loan_from_projects');
    }
}
