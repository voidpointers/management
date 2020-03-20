<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMessageContextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_contexts', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('conversation_id')->unsigned()->default(0)->comment('对话ID');
            $table->tinyInteger('type')->unsigned()->default(0)->comment('类型 1 listing 2 receipt');
            $table->bigInteger('context_id')->unsigned()->default(0)->comment('订单或产品ID');
            $table->bigInteger('sub_id')->unsigned()->default(0)->comment('子ID');
            $table->string('title')->default('')->comment('标题');
            $table->string('image')->default('')->comment('图片');
            $table->string('url')->default('')->comment('url');
            $table->index('conversation_id', 'idx_conversation_id');
        });

        DB::statement("ALTER TABLE `message_contexts` comment '消息上下文'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_contexts');
    }
}
