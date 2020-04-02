<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMessageTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->unsigned()->default(0)->comment('店铺ID');
            $table->string('title', 255)->default('')->comment('标题');
            $table->text('content')->comment('内容');
            $table->string('remark')->default('')->comment('备注');
            $table->text('attachments')->comment('附件');
            $table->integer('create_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('update_time')->unsigned()->default(0)->comment('更新时间');
        });

        DB::statement("ALTER TABLE `message_templates` comment '消息模板'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_templates');
    }
}
