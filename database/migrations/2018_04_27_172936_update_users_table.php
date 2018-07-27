<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_name')->nullable()->comment('用户昵称');
            $table->string('avatar', '255')->nullable()->comment('用户头像');
            $table->date('baby_birthday')->nullable()->comment('宝宝生日');
            $table->tinyInteger('baby_sex')->default(0)->comment('宝宝性别  0:未知  1:男 2:女');
            $table->tinyInteger('register_type')->comment('注册类型 1:验证码注册 2:第三方注册');
            $table->string('third_name')->nullable()->comment('第三方昵称');
            $table->string('third_avatar', '255')->nullable()->comment('第三方头像');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_name',
                'avatar',
                'baby_birthday',
                'baby_sex',
                'register_type',
                'third_name',
                'third_avatar'
            ]);
        });
    }
}
