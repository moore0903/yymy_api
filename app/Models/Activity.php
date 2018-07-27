<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table='activity';

    protected $guarded=[];


    /**
     * 判断活动状态
     * @return int
     */
    public function activity_time(){
        if(strtotime($this->offline_time) < time()){
            return Activity::ACTIVITY_TIME_OVER;
        }else if(strtotime($this->online_time) > time()  ){
            return Activity::ACTIVITY_TIME_FUTURE;
        }else if(strtotime($this->online_time) < time() && strtotime($this->offline_time) > time()){
            return Activity::ACTIVITY_TIME_CURRENT;
        }else{
            return 0;
        }
    }

    /**
     * 活动web URL
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function web_url(){
        return env('WEB_HOST').'/activity?id='.$this->id;
    }

    /**
     * 分享相关内容
     * @return array
     */
    public function share(){
        return [
            'share_url' => env('WEB_HOST').'/activity_share?id='.$this->id,
            'share_title' => $this->title,
            'share_desc' => mb_substr(strip_tags(htmlspecialchars_decode($this->content)),0,50,'utf-8'),
            'share_logo' => $this->thumb??asset('logo.png'),
        ];
    }

    const ACTIVITY_TIME_OVER = 1;  //已结束
    const ACTIVITY_TIME_CURRENT = 2;  //进行中
    const ACTIVITY_TIME_FUTURE = 3;  //未开始


    /**
     * 状态转中文
     * @param $status
     * @return string
     */
    public static function statusToChinese($status){
        switch($status){
            case Articles::STATUS_AVAILABLE:
                return '可用';
                break;
            case Articles::STATUS_UNAVAILABLE:
                return '禁用';
                break;
            default:
                return '未定义';
                break;
        }
    }


    const STATUS_AVAILABLE =1;  //可用
    const STATUS_UNAVAILABLE=0; //禁用
}
