<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMessageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('message_id')->unsigned()->default(0)->comment('消息ID');
            $table->bigInteger('conversation_id')->unsigned()->default(0)->comment('对话ID');
            $table->bigInteger('sender_id')->unsigned()->default(0)->comment('发送者ID');
            $table->text('message')->comment('消息');
            $table->json('images')->comment('图片');
            $table->tinyInteger('is_me')->unsigned()->default(0)->comment('是否自己');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('排序');
            $table->tinyInteger('is_sync')->unsigned()->default(0)->comment('是否已同步');
            $table->integer('send_time')->unsigned()->default(0)->comment('发送时间');
            $table->index('message_id', 'idx_message_id');
            $table->index('conversation_id', 'idx_conversation_id');
        });

        DB::statement("ALTER TABLE `message_details` comment '消息详细'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_details');
    }
}
