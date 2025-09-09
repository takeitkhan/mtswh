<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('license_no')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_no')->nullable();            
            $table->enum('vat_type', ['Inclusive', 'Exclusive'])->nullable();
            $table->string('vat_percent')->nullable();
            $table->enum('tax_type', ['Inclusive', 'Exclusive'])->nullable();            
            $table->string('tax_percent')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
