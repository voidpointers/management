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
            $table->increments('id');
            $table->string('username', 64)->default('')->comment('用户名');
            $table->string('password')->default('')->comment('密码');
            $table->integer('create_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('update_time')->unsigned()->default(0)->comment('更新时间');
            $table->unique('username', 'uk_username');
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
