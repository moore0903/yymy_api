<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppUpdateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_update_message', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('app_id')->comment('1:android 2:ios');
            $table->string('app_code','50')->comment('app 包名');
            $table->string('app_name','50')->comment('应用名称');
            $table->string('version_code','50')->comment('版本号');
            $table->string('version_name','50')->comment('版本名');
            $table->string('download_url')->comment('更新地址');
            $table->string('update_title')->comment('升级信息简要');
            $table->text('update_message')->nullable()->comment('升级信息详情');
            $table->tinyInteger('status')->comment('0:不可用 1:可用');
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
        Schema::dropIfExists('app_update_message');
    }
}
