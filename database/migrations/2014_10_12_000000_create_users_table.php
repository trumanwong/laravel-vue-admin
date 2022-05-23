<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('name', 20)->unique();
            $table->string('nickname', 20)->default('');
            $table->string('avatar', 160)->default('');
            $table->string('email', 50)->unique();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('sex')->default(0)->comment('0:man 1:woman');
            $table->timestamp('birthday')->nullable();
            $table->string('description')->default('');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
