<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebSiteJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_site_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('position')->comment('职位');
            $table->string('salary')->comment('薪资');
            $table->string('experience')->comment('经验');
            $table->string('num')->comment('人数');
            $table->string('address')->comment('地点');
            $table->string('tag')->comment('标签');
            $table->text('duty')->comment('职责');
            $table->text('need')->comment('需求');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->integer('sort')->default(0)->comment('排序号');
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
        Schema::dropIfExists('web_site_jobs');
    }
}
