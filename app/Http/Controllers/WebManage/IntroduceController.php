<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2018-4-30
 * Time: 09:40
 */

namespace App\Http\Controllers\WebManage;


use App\Http\Controllers\ApiController;

class IntroduceController extends ApiController
{
    public function Index(){
        return view('introduce');
    }

    public function addIntroduce(){

    }
}