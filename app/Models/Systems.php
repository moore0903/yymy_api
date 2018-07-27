<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2017/12/16
 * Time: 16:31
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Systems extends Model
{
    protected $table='api_log';

    /**
     * 图片压缩
     * @param $path 图片路径
     * @param $dir 图片目录
     * @param float $ratio 压缩比
     * @return string 返回压缩后图片路径
     */
    public static function image_save($path,$dir,$ratio = 0.7){
        $img = Image::make(asset($path));
        $path_exp = explode('/',$path);
        $path_exp = explode('.',$path_exp[count($path_exp)-1]);
        $height = $img->height();
        $width = $img->width();
        $save_paths = 'upload/'.$dir.'/'.date('Ymd');
        $save_paths = explode('/',$save_paths);
        $dir_path = '';
        foreach($save_paths as $save_path){
            $dir_path .= $save_path.'/';
            if(!is_dir($dir_path)){
                mkdir($dir_path);
            }
        }
        $result_path = $dir_path.$path_exp[0].'.jpeg';
        $img->resize($width * $ratio, $height * $ratio)->save($result_path);
        return '/upload/'.$dir.'/'.date('Ymd').'/'.$path_exp[0].'.jpeg';
    }

    /**
     * 图片格式化
     * @param $image
     * @return string
     */
    public static function image_format($image){
        if (!preg_match('/(http:\/\/)|(https:\/\/)/i', $image)) {
            return env('QINIU_URL').'/'.$image;
        }
        return $image;
    }


    public static $sqlArray = [];

    public static $user = [];


    const UNIQUE_BABY_HEALTH = 'yy.home.baby.health';  //栏目唯一标示
    const UNIQUE_BABY_STORY = 'yy.home.baby.story';
    const UNIQUE_BABY_GAME = 'yy.home.baby.game';
    const UNIQUE_BABY_KNOWLEDGE = 'yy.home.baby.knowledge';
    const UNIQUE_BABY_ENCYCLOPEDIA_ROOT = 'yy.home.baby.encyclopedia.root';


    const STATUS_YES = 1;  //状态  可用
    const STATUS_NO = 0;  //状态 不可用

}