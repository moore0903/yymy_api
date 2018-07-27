<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2018-4-30
 * Time: 09:36
 */

namespace App\Http\Controllers\WebManage;


use App\Http\Controllers\ApiController;

class IndexController extends ApiController
{
    /**
     * 官网首页展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Index(){
        $thumb=Articles::where('')->where('status',1)->orderBy('sort','asc')->limit(3)->get();
        return view('index',['thumb'=>$thumb]);
    }

    public function addIndex(){

    }
}