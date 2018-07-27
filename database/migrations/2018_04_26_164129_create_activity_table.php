<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('活动主题');
            $table->string('image')->comment('封面图片');
            $table->text('content')->comment('活动内容');
            $table->dateTime('online_time')->comment('上线时间');
            $table->dateTime('offline_time')->comment('下线时间');
            $table->string('address')->comment('地点');
            $table->integer('status')->default(1)->comment('状态');
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
        Schema::dropIfExists('activity');
    }
}
