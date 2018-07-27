<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AppUpdateMessage;
use App\Models\Articles;
use App\Models\Shop;
use App\Models\Systems;
use Illuminate\Http\Request;
use Intervention\Image\Image;

class WebController extends Controller
{
    /**
     * 文章详情页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function article_info($id){
        $article = Articles::with('catalog')->find($id);
        return view('web/article_info',[
            'article' => $article
        ]);
    }

    public function article_share($id){
        $article = Articles::with('catalog')->find($id);
        return view('web/article_share',[
            'article' => $article
        ]);
    }

    /**
     * 活动详情页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function activity_info($id){
        $activity = Activity::find($id);
        return view('web/activity_info',[
            'activity' => $activity
        ]);
    }

    /**
     * 门店详情
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shop_detail($id){
        $shop = Shop::find($id);
        return view('web/shop_detail',[
            'shop' => $shop
        ]);
    }

    /**
     * 前端app下载
     */
    public function download(){
        $type = AppUpdateMessage::get_device_type();
        $appUpdate = AppUpdateMessage::where('app_id',$type)->where('status',Systems::STATUS_YES)->orderByDesc('version_code')->first();
        if(empty($appUpdate->download_url)){
            $appUpdate->download_url = env('QINIU_URL').'/'.$appUpdate->upload_file;
        }
        header('HTTP/1.1 301 Moved Permanently');
        header('Location:'.$appUpdate->download_url);
        exit;
    }

    public function imagecache($width, $dir, $filename){
        info('123456');
        $img = Image::make(public_path('neditor/php/upload/image/').$dir.'/'.$filename);
        $img_width = $img->width();
        $img_height = $img->height();
        $ratio = $width / $img_width;
        return $img->fit($img_width * $ratio,$img_height * $img_height);
    }
}
