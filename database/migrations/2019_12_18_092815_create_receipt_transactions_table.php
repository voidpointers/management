<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateReceiptTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('receipt_sn')->unsigned()->default(0)->comment('收据编号');
            $table->bigInteger('receipt_id')->unsigned()->default(0)->comment('Etsy收据ID');
            $table->bigInteger('transaction_sn')->unsigned()->default(0)->comment('交易ID');
            $table->bigInteger('listing_id')->unsigned()->default(0)->comment('商品ID');
            $table->string('title')->default('')->comment('标题');
            $table->string('etsy_sku', 64)->default('')->comment('Etsy sku');
            $table->string('local_sku', 64)->default('')->comment('本地sku');
            $table->string('image')->default('')->comment('图片地址');
            $table->mediumInteger('quantity')->unsigned()->default(0)->comment('数量');
            $table->decimal('price', 12, 2)->unsigned()->default(0)->comment('单价');
            $table->json('attributes')->nullable()->comment('商品属性');
            $table->json('variations')->nullable()->comment('Etsy属性');
            $table->string('description', 255)->default('')->comment('描述');
            $table->integer('paid_tsz')->unsigned()->default(0)->comment('支付时间');
            $table->integer('shipped_tsz')->unsigned()->default(0)->comment('发货时间');
            $table->index('receipt_sn', 'idx_receipt_sn');
            $table->index('transaction_sn', 'idx_transaction_sn');
            $table->index('listing_id', 'idx_listing_id');
        });

        DB::statement("ALTER TABLE `receipt_transactions` comment '订单交易列表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_transactions');
    }
}
