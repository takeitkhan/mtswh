<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteListRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_list_roles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('route_id')->unsigned();
            $table->enum('show_as', ['All', 'User', 'Permission'])->unsigned();
            $table->timestamps();

            //Foreign KEy Generate
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('route_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_list_roles');
    }
}
