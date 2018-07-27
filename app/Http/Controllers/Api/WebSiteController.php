<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Systems;
use App\Models\WebSiteActivity;
use App\Models\WebSiteBanner;
use App\Models\WebSiteCooperation;
use App\Models\WebSiteJob;
use Illuminate\Http\Request;

class WebSiteController extends ApiController
{
    /**
     * 获取banner
     * @param Request $request
     * @return mixed
     */
    public function get_banner(Request $request){
        $banners = WebSiteBanner::where('status',Systems::STATUS_YES)->orderBy('sort')->orderByDesc('created_at')->limit($request->size??3)->get();
        return $this->success([
            'list' => $banners
        ]);
    }

    /**
     * 获取首页推荐活动
     * @return mixed
     */
    public function get_home_activites(Request $request){
        $activites = WebSiteActivity::where('status',Systems::STATUS_YES)->where('index_rec_status',Systems::STATUS_YES)->orderBy('sort')->orderByDesc('created_at')->limit($request->size??3)->get();
        $activites = $activites->map(function($item){
            $item->detail = strip_tags($item->detail);
            return $item;
        });
        return $this->success([
            'list' => $activites
        ]);
    }

    /**
     * 获取活动列表
     * @param Request $request
     * @return mixed
     */
    public function get_activites(Request $request){
        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $activites = WebSiteActivity::select('id','title','thumb','detail', 'created_at')->where('status',Systems::STATUS_YES)
            ->orderBy('sort')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $activites = $activites->map(function($item){
            $item->thumb = Systems::image_format($item->thumb);
            $item->detail = strip_tags($item->detail);
            return $item;
        });

        return $this->success([
            'list' => $activites
        ]);
    }

    /**
     * 获取活动详情
     * @param Request $request
     * @return mixed
     */
    public function get_activity_info(Request $request){
        $activity = WebSiteActivity::find((int)$request->id);
        if(empty($activity)) return $this->error('活动不可用','-10002');
        return $this->success([
            'info' => $activity
        ]);
    }

    /**
     * 提交商务合作
     * @param Request $request
     * @return mixed
     */
    public function submit_cooperation(Request $request){
        if(empty($request->name)) return $this->error('联系人不能为空','-10002');  // TODO 错误代码
        if(empty($request->phone)) return $this->error('联系电话不能为空','-10002');  // TODO 错误代码
        WebSiteCooperation::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'remark' => $request->remark,
        ]);
        return $this->message('申请成功');
    }

    /**
     * 获取招聘信息
     * @param Request $request
     * @return mixed
     */
    public function get_jobs(Request $request){
        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $jobs = WebSiteJob::where('status',Systems::STATUS_YES)->orderBy('sort')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $jobs = $jobs->map(function($item){
            $item->tags = explode(',',$item->tag);
            unset($item->tag);
            return $item;
        });

        return $this->success([
            'list' => $jobs
        ]);
    }

    /**
     * 获取招聘详情
     * @param Request $request
     * @return mixed
     */
    public function get_job_info(Request $request){
        $job = WebSiteJob::find((int)$request->id);
        if(empty($job)) return $this->error('招聘信息不可用','-10002');
        return $this->success([
            'info' => $job
        ]);
    }
}
