<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableListings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('listings', function (Blueprint $table) {
			$table->json('price_on_property')->comment('控制价格是否独立');
			$table->json('quantity_on_property')->comment('控制数量是否独立');
			$table->json('sku_on_property')->comment('控制sku是否独立');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
