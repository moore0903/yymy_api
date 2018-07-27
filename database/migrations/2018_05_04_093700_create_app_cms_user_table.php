<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppCmsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_cms_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->comment('用户名');
            $table->string('password',255)->comment('密码');
            $table->string('phone')->comment('电话');
            $table->string('avatar',255)->nullable()->default('nopic.jpg')->comment('头像');
            $table->tinyInteger('status')->default(1)->comment('是否启用,1启用,0禁用');
            $table->string('log_time')->nullable()->comment('最后登录时间');
            $table->string('log_ip',15)->nullable()->comment('最后登录ip');
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('app_cms_user');
    }
}
