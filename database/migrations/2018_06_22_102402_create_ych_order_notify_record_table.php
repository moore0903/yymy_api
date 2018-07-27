<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYchOrderNotifyRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ych_order_notify_record', function (Blueprint $table) {
            $table->increments('id');
            $table->char("order_number", "25")->comment('订单号');
            $table->text('params')->comment("请求参数");
            $table->tinyInteger('is_ok')->default(0)->comment("是否成功:0-否 1-成功");
            $table->tinyInteger('pay_type')->comment("支付方式:0-支付宝 1-微信");
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
        Schema::dropIfExists('ych_order_notify_record');
    }
}
