<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->unsigned()->default(0)->comment('店铺ID');
            $table->integer('taxonomy_id')->unsigned()->default(0)->comment('分类ID');
            $table->integer('listing_id')->unsigned()->default(0)->comment('产品ID');
            $table->string('title', 255)->default('')->comment('标题');
            $table->string('image', 255)->default('')->comment('图片');
            $table->string('taxonomy_path', 255)->default('')->comment('分类路径');
            $table->string('tags', 255)->default('')->comment('标签');
            $table->mediumInteger('views')->unsigned()->default(0)->comment('查看次数');
            $table->mediumInteger('num_favorers')->unsigned()->default(0)->comment('喜欢次数');
            $table->string('url', 255)->default('')->comment('商品URL');
            $table->json('inventories')->comment('属性');
            $table->integer('creation_tsz')->unsigned()->default(0)->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_stats');
    }
}
