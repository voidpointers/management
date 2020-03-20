<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('shop_id')->unsigned()->default(0)->comment('店铺ID');
            $table->bigInteger('user_id')->unsigned()->default(0)->comment('用户ID');
            $table->string('shop_name')->default('')->comment('店铺名');
            $table->string('username')->default('')->comment('用户名');
            $table->string('title')->default('')->comment('店铺主页简短标题');
            $table->string('currency_code', 32)->default('')->comment('货币ISO代码');
            $table->string('shop_name_zh')->default('')->comment('店铺中文名');
            $table->string('url')->default('')->comment('店铺网址');
            $table->string('image')->default('')->comment('图片');
            $table->string('icon')->default('')->comment('店铺图标');
            $table->string('consumer_key')->default('')->comment('请求令牌');
            $table->string('consumer_secret')->default('')->comment('请求密钥');
            $table->string('access_token')->default('')->comment('授权访问token');
            $table->string('access_secret')->default('')->comment('授权密钥');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态');
            $table->string('ip')->default('')->comment('店铺部署IP地址');
            $table->integer('create_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('update_time')->unsigned()->default(0)->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
