<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('meta_title')->nullable();
            $table->string('meta_name')->unique();
            $table->string('meta_value')->nullable();
            $table->enum('meta_type', ['Text', 'Textarea', 'Select', 'Richeditor', 'Number', 'Checkbox'])->nullable();   
            $table->enum('meta_group', ['General', 'Homepage', 'Header Section'])->nullable();   
            $table->integer('meta_order')->nullable();
            $table->string('meta_placeholder')->nullable();
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
        Schema::dropIfExists('global_settings');
    }
}
