<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateListingImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_images', function (Blueprint $table) {
            $table->bigInteger('listing_id')->unsigned()->default(0)->comment('产品ID');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('排序');
            $table->bigInteger('image_id')->unsigned()->default(0)->comment('图片ID');
            $table->string('url')->default('')->comment('图片URL');
            $table->tinyInteger('is_sync')->unsigned()->default(0)->comment('是否同步');
            $table->integer('create_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('update_time')->unsigned()->default(0)->comment('更新时间');
            $table->unique(['listing_id', 'sort'], 'uk_id');
            $table->index('image_id', 'idx_image_id');
        });

        DB::statement("ALTER TABLE `listing_images` comment '图片表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_images');
    }
}
