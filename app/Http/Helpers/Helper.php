<?php

namespace App\Http\Helpers;

use App\Http\Middleware\AesEncrypt;
use Illuminate\Http\Request;

class Helper
{
    static public function call_self_service($url, $data)
    {
        try {
            $proxy = Request::create($url, 'POST', [], [],
                [], ["HTTP_HOST"=>env("API_DOMAIN")]);
            // 暂时用这个传参方式
            $_POST = $data;

            $response = \Route::dispatch($proxy);
            $ret = $response->getContent();
            $aes = new AesEncrypt();
            $ret = $aes->decrypt($ret);
            $parseRet = json_decode($ret, true);

            return $parseRet;
        } catch (\Exception $e) {
            return false;
        }
    }
}