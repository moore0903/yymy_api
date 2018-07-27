<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWebSiteActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_site_activities', function (Blueprint $table) {
            $table->tinyInteger('index_rec_status')->default(0)->comment('是否推荐 0:不推荐 1:推荐');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_site_activities', function (Blueprint $table) {
            $table->dropColumn('index_rec_status');

        });
    }
}
