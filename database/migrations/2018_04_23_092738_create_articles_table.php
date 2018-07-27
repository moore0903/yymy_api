<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('catalog_id')->unsigned()->comment('栏目id');
            $table->string('title')->comment('标题');
            $table->string('thumb')->comment('缩略图');
            $table->text('content')->comment('内容');
            $table->tinyInteger('index_rec_status')->default(0)->comment('首页推荐');
            $table->tinyInteger('index_banner_rec_status')->default(0)->comment('首页banner图推荐');
            $table->string('index_banner_rec_thumb')->default(0)->comment('首页banner图');
            $table->integer('read')->default(0)->comment('阅读量');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->integer('sort')->default(0)->comment('排序号');
            $table->timestamps();


            $table->foreign('catalog_id')->references('id')->on('catalogs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
