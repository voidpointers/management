<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('listing_id')->unsigned()->default(0)->comment('商品ID');
            $table->bigInteger('product_id')->unsigned()->default(0)->comment('库存ID');
            $table->string('sku', 120)->default('')->comment('SKU');
            $table->mediumInteger('price')->unsigned()->default(0)->comment('单价');
            $table->mediumInteger('quantity')->unsigned()->default(0)->comment('数量');
            $table->tinyInteger('is_deleted')->unsigned()->default(0)->comment('是否删除');
            $table->tinyInteger('is_enabled')->unsigned()->default(0)->comment('是否启用');
            $table->json('properties')->comment('属性');
            $table->unique('product_id', 'uk_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_inventories');
    }
}
