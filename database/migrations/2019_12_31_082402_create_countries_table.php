<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 2)->default('')->comment('国际简码');
            $table->string('en', 64)->default('')->comment('英文');
            $table->string('name', 64)->default('')->comment('中文');
            $table->index('code', 'idx_code');
        });

        DB::statement("ALTER TABLE `countries` comment '国家列表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
