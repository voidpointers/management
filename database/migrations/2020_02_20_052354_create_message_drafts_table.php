<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_drafts', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型');
            $table->bigInteger('shop_id')->unsigned()->default(0)->comment('店铺ID');
            $table->bigInteger('conversation_id')->unsigned()->default(0)->comment('会话ID');
            $table->string('conversation_url', 255)->default('')->comment('会话URL');
            $table->bigInteger('receipt_id')->unsigned()->default(0)->comment('订单ID');
            $table->bigInteger('sender_id')->unsigned()->default(0)->comment('发送者ID');
            $table->string('title', 255)->default('')->comment('');
            $table->string('subject', 255)->default('')->comment('');
            $table->string('buyer_name')->default('')->comment('');
            $table->string('buyer_user_name')->default('')->comment('');
            $table->text('message')->comment('');
            $table->json('images')->comment('图片');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态');
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
        Schema::dropIfExists('message_drafts');
    }
}
