<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('店铺名');
            $table->string('image')->comment('封面图');
            $table->string('inner_image')->comment('内页图');
            $table->string('province')->comment('省');
            $table->string('city')->comment('市');
            $table->string('town')->comment('区');
            $table->integer('province_code')->nullable()->comment('省编码');
            $table->integer('city_code')->nullable()->comment('市编码');
            $table->integer('town_code')->nullable()->comment('区编码');
            $table->string('address')->comment('地址');
            $table->string('lon')->nullable()->comment('经度');
            $table->string('lat')->nullable()->comment('纬度');
            $table->string('average_price')->comment('人均消费');
            $table->string('phone')->comment('电话');
            $table->text('notice')->comment('游玩须知');
            $table->text('detail')->comment('门店介绍');
            $table->integer('status')->default(0)->comment('是否可用:0禁用,1可用');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('shop');
    }
}
