<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->nullable()->comment('请求链接');
            $table->text('body')->nullable()->comment('请求内容');
            $table->text('sql')->nullable()->comment('执行sql语句');
            $table->text('response_body')->nullable()->comment('响应');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_log');
    }
}
