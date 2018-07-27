<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUpdateMessage extends Model
{

    protected $table='app_update_message';

    protected $guarded=[];


    /**
     * 获取当前请求设备
     * @return int|string
     */
    public static function get_device_type(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type = '1';
        if(strpos($agent,'iphone') || strpos($agent,'ipad')){
            $type = 2;
        }else if(strpos($agent,'android')){
            $type = 1;
        }
        return $type;
    }

    /**
     * app 类型
     * @var array
     */
    public static $app_string = [
        1 => 'android',
        2 => 'ios'
    ];
}
