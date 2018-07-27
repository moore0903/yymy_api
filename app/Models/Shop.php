<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table='shop';

    protected $guarded=['deleted_at'];

    /**
     * 门店web URL
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function detail_web_url(){
        return env('WEB_HOST').'/shop_info?id='.$this->id;
    }

    /**
     * 分享相关内容
     * @return array
     */
    public function share(){
        return [
            'share_url' => env('WEB_HOST').'/shop_share?id='.$this->id,
            'share_title' => $this->title,
            'share_desc'=>mb_substr(strip_tags(htmlspecialchars_decode($this->detail)),0,50,'utf-8'),
            'share_logo'=>$this->image?Systems::image_format($this->image):asset('logo.png'),
        ];
    }


    /**
     * 状态转中文
     * @param $status
     * @return string
     */
    public static function statusToChinese($status){
        switch($status){
            case Shop::STATUS_AVAILABLE:
                return '可用';
                break;
            case Shop::STATUS_UNAVAILABLE:
                return '禁用';
                break;
            default:
                return '未定义';
                break;
        }
    }

    const STATUS_AVAILABLE=1;   //可用
    const STATUS_UNAVAILABLE=0; //禁用
}
