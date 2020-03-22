<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniqueIdGeneratorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unique_id_generator', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('ticket')->unique('uk_ticket')->comment('数据');
        });

        DB::statement("ALTER TABLE `unique_id_generator` comment '唯一ID生成器'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unique_id_generator');
    }
}
