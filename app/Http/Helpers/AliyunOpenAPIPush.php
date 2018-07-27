<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/7/12
 * Time: 16:03
 */

namespace App\Http\Helpers;


use App\Models\AliyunPush;
use Push\Request\V20160801 as Push;

class AliyunOpenAPIPush
{
    private $config;

    private $profile;

    private $request;

    private $aliyunPush;

    private $ext;

    public function __construct(AliyunPush $aliyunPush)
    {
        $this->config = [
            'access_key_id' => env('ALIYUN_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
            'android_app_key' => '24955610',
            'ios_app_key' => '24955611'
        ];

        $this->aliyunPush = $aliyunPush;

        include_once 'aliyun-openapi-php-sdk-master/aliyun-php-sdk-core/Config.php';
        include_once 'aliyun-openapi-php-sdk-master/aliyun-php-sdk-push/Push/Request/V20160801/PushRequest.php';

        $this->profile = \DefaultProfile::getProfile('cn-hangzhou', $this->config['access_key_id'], $this->config['access_key_secret']);
        $this->request = new Push\PushRequest();

        $this->request->setTarget('ALL');
        $this->request->setTargetValue('ALL');
        $this->request->setPushType('NOTICE');
        switch ($this->aliyunPush->push_device_type){
            case 1:
                $this->request->setDeviceType('ALL');
                break;
            case 2:
                $this->request->setDeviceType('iOS');
                break;
            case 3:
                $this->request->setDeviceType('ANDROID');
                break;
        }

        $this->ext = json_encode([
            'type' => $this->aliyunPush->push_open_type,
            'id' => $this->aliyunPush->push_open_activity,
            'web_url' => call_user_func(function(){
                if($this->aliyunPush->push_open_type == 3){
                    return env('WEB_HOST').'/article_info?id='.$this->aliyunPush->push_open_activity;
                }elseif ($this->aliyunPush->push_open_type == 4){
                    return env('WEB_HOST').'/activity?id='.$this->aliyunPush->push_open_activity;
                }
            })
        ]);

        $this->request->setTitle($this->aliyunPush->push_title);
        $this->request->setBody($this->aliyunPush->push_body);
        $this->request->setStoreOffline(true);
        if($this->aliyunPush->push_time)
            $this->request->setPushTime(gmdate('Y-m-d\TH:i:s\Z',strtotime($this->aliyunPush->push_time)));
    }

    public function push(){
        if($this->aliyunPush->push_device_type == 1){
            $this->android_push();
            $this->ios_push();
        }elseif ($this->aliyunPush->push_device_type == 2){
            $this->ios_push();
        }elseif ($this->aliyunPush->push_device_type == 3){
            $this->android_push();
        }
    }

    private function android_push(){
        $this->request->setAppKey($this->config['android_app_key']);
        if($this->aliyunPush->push_open_type != 1){
            $this->request->setAndroidOpenType('ACTIVITY');
            $this->request->setAndroidPopupActivity($this->aliyunPush->push_open_activity);
            $this->request->setAndroidPopupTitle($this->aliyunPush->push_title);
            $this->request->setAndroidPopupBody($this->aliyunPush->push_body);
        }
        $this->request->setAndroidExtParameters($this->ext);
        $this->request->setAndroidNotificationChannel('1');

        $client = new \DefaultAcsClient($this->profile);
        $client->getAcsResponse($this->request);
    }

    private function ios_push(){
        $this->request->setAppKey($this->config['ios_app_key']);
//        $this->request->setiOSApnsEnv('DEV');
        $this->request->setiOSExtParameters($this->ext);

        $client = new \DefaultAcsClient($this->profile);
        $client->getAcsResponse($this->request);
    }
}