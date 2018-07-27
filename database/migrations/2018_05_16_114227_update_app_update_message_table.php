<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAppUpdateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_update_message', function (Blueprint $table) {
            $table->string('upload_file')->nullable()->comment('上传文件');
            $table->string('download_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_update_message', function (Blueprint $table) {
            $table->dropColumn('upload_file');

        });
    }
}
