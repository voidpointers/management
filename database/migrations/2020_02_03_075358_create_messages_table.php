<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigInteger('conversation_id')->unsigned()->default(0)->comment('对话ID');
            $table->string('title', 255)->default('')->comment('标题');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态 1 待读取 2 待审核 8 已完成');
            $table->bigInteger('shop_id')->unsigned()->default(0)->comment('店铺ID');
            $table->bigInteger('sender_id')->unsigned()->default(0)->comment('发送者ID');
            $table->tinyInteger('count')->unsigned()->default(0)->comment('消息总量');
            $table->string('excerpt', 255)->default('')->comment('对话摘要');
            $table->tinyInteger('context_type')->unsigned()->default(0)->comment('类型 1 普通 2 产品 3 订单');
            $table->bigInteger('context_id')->unsigned()->default(0)->comment('');
            $table->json('tags')->comment('标签');
            $table->tinyInteger('is_unread')->unsigned()->default(0)->comment('是否未读');
            $table->integer('create_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('update_time')->unsigned()->default(0)->comment('更新时间');
            $table->unique(['conversation_id', 'shop_id'], 'uk_conversation_id');
            $table->index('sender_id', 'idx_sender_id');
            $table->index('context_id', 'idx_context_id');
            $table->index('create_time', 'idx_create_time');
            $table->index('update_time', 'idx_update_time');
        });

        DB::statement("ALTER TABLE `messages` comment '消息列表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
