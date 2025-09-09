<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('employee_no')->nullable();
            $table->string('username')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('father')->nullable();
            $table->string('mother')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('company')->nullable();
            $table->string('department')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('district')->nullable();
            $table->date('birthday')->nullable();
            $table->date('join_date')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('employee_status', ['Enroll', 'Terminated', 'Long Leave', 'Left Job', 'On Hold'])->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
