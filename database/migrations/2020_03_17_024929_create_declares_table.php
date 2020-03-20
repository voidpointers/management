<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeclaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('declares', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('shop_id')->unsigned()->default(0)->comment('店铺ID');
            $table->decimal('price', 10, 2)->unsigned()->default(0)->comment('价格');
            $table->float('weight', 10 ,2)->unsigned()->default(0)->comment('重量');
            $table->tinyInteger('quantity')->unsigned()->default(0)->comment('数量');
            $table->string('name', 120)->default('')->comment('申报英文名');
            $table->string('name_zh')->default('')->comment('申报中文名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('declares');
    }
}
