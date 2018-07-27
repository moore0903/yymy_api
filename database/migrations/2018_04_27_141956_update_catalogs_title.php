<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCatalogsTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->string('color')->comment('颜色');
        });

        foreach (\App\Models\Catalogs::all() as $catalog){
            $catalog->color = \App\Models\Catalogs::$colors_select[array_rand(\App\Models\Catalogs::$colors_select)];
            $catalog->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
