<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYchPayOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ych_pay_order', function (Blueprint $table) {
            $table->increments('id');
            $table->char("order_number", "25")->comment('订单号');
            $table->tinyInteger('status')->default(0)->comment('支付状态：0-未支付 1-支付成功 2-支付失败');
            $table->bigInteger('user_id')->comment('用户id');
            $table->uuid('ych_id')->comment('油菜花id');
            $table->integer('total_amount')->comment('订单金额，单位：分');
            $table->integer('paid_amount')->comment('支付金额，单位：分');
            $table->tinyInteger('pay_type')->comment('支付方式：1-支付宝 2-微信');
            $table->tinyInteger('goods_type')->comment('油菜花商品类型：101-套票 102-代币');
            $table->string("goods_id", "50")->comment('商品id');
            $table->text('remark');
            $table->timestamp('pay_time')->comment("支付时间");
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
        //
        Schema::dropIfExists('ych_pay_order');
    }
}
