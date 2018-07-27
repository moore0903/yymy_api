<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliyunPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliyun_push', function (Blueprint $table) {
            $table->increments('id');
            $table->string('push_title')->comment('推送标题');
            $table->text('push_body')->comment('推送内容');
            $table->dateTime('push_time')->comment('推送时间');
            $table->tinyInteger('push_device_type')->default(1)->comment('推送设备类型 1:all 2:ios 3:android');
            $table->string('push_message_id')->comment('推送返回的messageID');
            $table->string('push_request_id')->comment('推送返回的requestID');
            $table->tinyInteger('push_status')->default(1)->comment('发送状态 1:未发送 2:已发送 3:已取消 4:已失败 5:其它');
            $table->tinyInteger('push_open_type')->default(1)->comment('通知后动作 1:打开应用 2:版本更新 3:文章分类 4活动分类');
            $table->string('push_open_activity')->nullable()->comment('通知后打开页面');
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
        Schema::dropIfExists('aliyun_push');
    }
}
