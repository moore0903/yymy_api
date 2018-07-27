<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTAddressProvinceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_address_province', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('省编码');
            $table->string('name')->comment('省名称');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('t_address_city', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('城市编码');
            $table->string('name')->comment('城市名称');
            $table->string('provinceCode')->comment('所属省份编码');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('t_address_town', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('区县编码');
            $table->string('name')->comment('区县名称');
            $table->string('cityCode')->comment('所属城市编码');
            $table->softDeletes();
            $table->timestamps();
        });

        //        读取文件内容
        $sql1 = file_get_contents(storage_path('address/t_address_province.sql'));
        $sql2 = file_get_contents(storage_path('address/t_address_city.sql'));
        $sql3 = file_get_contents(storage_path('address/t_address_town.sql'));
        $arr1 = explode(';', $sql1);
        $arr2 = explode(';', $sql2);
        $arr3 = explode(';', $sql3);
        $_mysqli = new \mysqli(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'));

//执行sql语句
        foreach ($arr1 as $_value) {
            $_mysqli->query('set names utf8');
            $_mysqli->query('use '.env('DB_DATABASE'));
            $_mysqli->query($_value . ';');
        }
        foreach ($arr2 as $_value) {
            $_mysqli->query('set names utf8');
            $_mysqli->query('use '.env('DB_DATABASE'));
            $_mysqli->query($_value . ';');
        }
        foreach ($arr3 as $_value) {
            $_mysqli->query('set names utf8');
            $_mysqli->query('use '.env('DB_DATABASE'));
            $_mysqli->query($_value . ';');
        }

        $_mysqli->close();
        unset($_mysqli);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_address_province');
        Schema::dropIfExists('t_address_city');
        Schema::dropIfExists('t_address_town');
    }
}
