<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('用户ID');
            $table->string('username')->default('')->comment('用户名');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('location')->default('')->comment('用户所在地区');
            $table->string('display_name')->default('')->comment('显示用户名');
            $table->string('shop_name')->default('')->comment('店铺名');
            $table->string('shop_avatar')->default('')->comment('店铺头像');
            $table->tinyInteger('is_admin')->comment('是否管理员');
            $table->tinyInteger('is_seller')->comment('是否卖家');
            $table->unique('user_id', 'uk_user_id');
        });

        DB::statement("ALTER TABLE `customers` COMMENT '客户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
