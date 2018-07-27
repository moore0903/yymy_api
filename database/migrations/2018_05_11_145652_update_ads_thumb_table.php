<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdsThumbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('ads', function (Blueprint $table) {
            $table->string('thumb')->after('ad_table_type')->comment('缩略图');
            $table->string('sort')->after('thumb')->comment('排序');
            $table->string('status')->after('sort')->comment('是否可用');
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
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['thumb','sort','status']);

        });
    }
}
