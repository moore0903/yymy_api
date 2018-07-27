<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2018-4-30
 * Time: 09:47
 */

namespace App\Http\Controllers\WebManage;


use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ActivityController extends ApiController
{
    /**
     * 官网活动首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Index(){
        $activity=WebActivity::where('online_time','<',time())->where('offline_time','>',time())->where('status',1)->orderBy('sort','asc')->orderBy('created_at','desc')->paginate(5);
        return view('activity',['activity'=>$activity]);
    }

    public function addActivity(Request $request){
        $activity=WebActivity::create([
            'title'=>$request->title,
            'contents'=>$request->contents,
            'online_time'=>$request->online_time,
            'offline_time'=>$request->offline_time,
            'status'=>$request->status,
            'sort'=>$request->sort
        ]);
        return view('activity');
    }
}