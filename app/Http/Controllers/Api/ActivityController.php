<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Activity;
use App\Models\Systems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends ApiController
{
    /**
     * 获取活动列表
     * @param Request $request
     * @return mixed
     */
    public function get_activity_list(Request $request){
        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $activitys = Activity::select('id','title',DB::raw('image as thumb'),'online_time','offline_time','address','content')->where('status',Systems::STATUS_YES)
            ->orderBy('sort')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $activitys = $activitys->map(function($item){
            $item->thumb = Systems::image_format($item->thumb);
            $item->type = $item->activity_time();
            $item->web_url = $item->web_url();
            $item->share = $item->share();
            unset($item->content);
            return $item;
        });

        return $this->success([
            'list' => $activitys
        ]);
    }

    /**
     * 获取活动详情 APP
     * @param Request $request
     * @return mixed
     */
    public function get_activity_api_info(Request $request){
        $activity = Activity::select('id','title',DB::raw('image as thumb'),'online_time','offline_time','address','content')->where('status',Systems::STATUS_YES)->find((int)$request->id);
        if(empty($activity)) return $this->error('活动不可用','-10002');
        $activity->thumb = Systems::image_format($activity->thumb);
        $activity->share = $activity->share();
        $activity->type = $activity->activity_time();
        $activity->web_url = $activity->web_url();
        unset($activity->content);
        return $this->success(['info' => $activity]);
    }

    /**
     * 获取活动详情
     * @param Request $request
     * @return mixed
     */
    public function get_activity_info(Request $request){
        $activity = Activity::where('status',Systems::STATUS_YES)->find((int)$request->id);
        if(empty($activity)) return $this->error('活动不可用','-10002');  // TODO 错误代码

        $activity->share = $activity->share();
        $activity->image = Systems::image_format($activity->image);
        $activity->type = $activity->activity_time();

        unset($activity->status,$activity->sort,$activity->deleted_at,$activity->created_at,$activity->updated_at);

        return $this->success([
            'info' => $activity
        ]);
    }
}
