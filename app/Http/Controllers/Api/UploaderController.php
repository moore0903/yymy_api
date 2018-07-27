<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2017/11/20
 * Time: 16:30
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\ApiController;
use App\Http\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploaderController extends ApiController
{
    /**
     * 上传图片接口
     * @param Request $request
     * @return array|mixed
     */
    public function imageUpload(Request $request){
        $quality = is_numeric($request->quality) ? $request->quality : 100;
        $scale = 0.7;   //缩放比例
        if(!$request->hasFile('image')) return $this->error('您没有选择上传文件','-50001');
        $path = [];
        foreach($request->file('image') as $item){
            $allowed_extensions = ["png", "jpg", "gif"];
            if ($item->getClientOriginalExtension() && !in_array($item->getClientOriginalExtension(), $allowed_extensions)) {
                return $this->error('上传格式必须为png,jpg,gif','-50002');
            }

            $tempName = "/tmp/".uniqid();
            $img = Image::make($item)->save($tempName, $quality);
            $img = Image::make($img)->resize($img->getWidth() * $scale, $img->getHeight() * $scale)->save($tempName);

            $date = date('Ymd');
            $disk = Storage::disk('qiniu');

            $filePath = $date."/".$item->hashName();
            $ret = $disk->put($filePath, $img->encoded);

            unlink($tempName);

            if ($ret) {
                $path[] = env("QINIU_DOMAIN")."/".$filePath;
            } else {
                return $this->error('上传图片错误','-50099');
            }
        }
        $path = implode(',',$path);
        return $this->success(['path'=>$path]);
    }

}