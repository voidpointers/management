<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptLogisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_logistics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_code', 128)->default('')->comment('运单号');
            $table->bigInteger('receipt_sn')->unsigned()->default(0)->comment('订单编号');
            $table->bigInteger('shipping_id')->default(0)->comment('配送ID');
            $table->integer('provider_id')->default(0)->comment('物流商ID');
            $table->integer('channel_id')->default(0)->comment('物流渠道ID');
            $table->string('waybill_url')->default('')->comment('面单');
            $table->string('tracking_url', 128)->default('')->comment('跟踪url');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->json('provider')->comment('物流公司');
            $table->string('remark')->default('')->comment('备注');
            $table->integer('create_time')->default(0)->comment('创建时间');
            $table->integer('update_time')->default(0)->comment('更新时间');
            $table->unique('receipt_sn', 'uk_receipt_sn');
            $table->index(['provider_id', 'channel_id'], 'idx_provider_channel_id');
            $table->index('tracking_code', 'idx_tracking_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_logistics');
    }
}
