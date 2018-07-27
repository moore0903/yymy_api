<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop', function (Blueprint $table) {
            $table->dropColumn('notice');
            $table->text('notice_age')->comment('适玩年龄');
            $table->text('notice_time')->comment('营业时间');
            $table->text('notice_rule')->comment('注意事项');
        });
        \App\Models\Shop::where('id','<','100')->update([
            'notice_age' => '2岁到8岁儿童',
            'notice_time' => '10:00-21:00',
            'notice_rule' => '1.参与者需穿袜子经常,若未带袜子可至前台购买 5元/双 \r\n 2.限1.4米以下儿童入园'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop', function (Blueprint $table) {
            $table->dropColumn([
                'notice_age',
                'notice_time',
                'notice_rule',
            ]);
        });
    }
}
