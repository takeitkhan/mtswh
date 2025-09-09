<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_lists', function (Blueprint $table) {
            $table->id();
            $table->string('route_title');
            $table->string('route_name')->unique();
            $table->string('route_parameter')->nullable();
            $table->string('route_description')->nullable();
            $table->bigInteger('route_group')->unsigned()->nullable();
            $table->string('route_icon')->nullable();
            $table->integer('route_order')->nullable();
            $table->string('route_hash')->nullable();
            $table->enum('show_menu', ['Yes', 'No'])->nullable();
            $table->integer('parent_menu_id')->nullable();
            $table->set('dashboard_position', ['Left', 'Right', 'Top', 'Bottom'])->nullable();
            $table->string('show_for')->nullable();
            $table->enum('is_show_as', ['Yes', 'No'])->nullable();
            $table->timestamps();

            //Foreign KEy Generate
            $table->foreign('route_group')->references('id')->on('route_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_lists');
    }
}
