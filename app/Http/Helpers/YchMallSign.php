<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/6/7
 * Time: 16:24
 */

namespace App\Http\Helpers;

use GuzzleHttp\Client;

trait YchMallSign
{

    protected function setData($_data = [], $getSignCallback=null)
    {
        $data = [
            "AppID" => env('YCH_MALL_APP_ID'),
            'BussinessID' => env('YCH_MALL_BUSSINESS_ID'),
            "TS" => $this->getTimestamp(13)
        ];
        if (count($_data) > 0)
            $data = array_merge($data, $_data);

        return array_merge($data, ['Sign' => is_callable($getSignCallback) ? call_user_func($getSignCallback, $data) :
            $this->getSign($data)]);

    }

    /**
     * 返回参数
     * @param $data
     * @return array
     */
    protected function getSign($data)
    {
        $result = ksort($data, SORT_STRING);//排序
        $arrayKey = array_keys($data);//取KEY
        if ($result) {
            $strMd5 = env('YCH_MALL_SECRET_KEY');//拼接结果
            $index = 0;
            foreach ($data as $value) {//拼接VALUE
                $strMd5 = $strMd5 . strtolower($arrayKey[$index]);
                $strMd5 = $strMd5 . $value;
                $index++;
            }
            $strMd5 = $strMd5 . env('YCH_MALL_SECRET_KEY');//拼接结果
            return strtoupper(md5($strMd5));//MD5加密结果大写
        }
        return "";
    }

    /**
     * 获取订单的签名
     * @param $data
     * @return string
     */
    protected function getOrderSign($data)
    {
        $arrayKey = array_keys($data);
        $result = natcasesort($arrayKey);
        if(isset($arrayKey)){

            $strMd5 = env('YCH_MALL_SECRET_KEY');//拼接结果
            foreach($arrayKey as $value){//拼接VALUE
                $strMd5 = $strMd5.strtolower($value);
                if(is_array($data[$value])){
                    $itmejson = '[';
                    foreach($data[$value] as $item){
                        if(strlen($itmejson) != 1){
                            $itmejson = $itmejson.',';
                        }

                        $itmejson = $itmejson . '{"GoodsID":"'.$item['GoodsID'].'","GoodsName":"'.$item['GoodsName'].'","GoodsPrice":'.$item['GoodsPrice'].',"Amount":'.$item['Amount'].',"Summary":"'.$item['Summary'].'"}';
                    }
                    $itmejson=$itmejson.']';
                    $strMd5 = $strMd5.$itmejson;
                }else{
                    $strMd5 = $strMd5.trim($data[$value]);
                }
            }
            $strMd5 = $strMd5.env('YCH_MALL_SECRET_KEY');//拼接结果
            return  strtoupper(md5($strMd5));//MD5加密结果大写
        }
        return "";
    }

    /**
     * 返回时间戳
     * @param bool $digits
     * @return int|string
     */
    private function getTimestamp($digits = false)
    {
        $digits = $digits > 10 ? $digits : 10;
        $digits = $digits - 10;
        if ((!$digits) || ($digits == 10)) {
            return time();
        } else {
            return number_format(microtime(true), $digits, '', '');
        }
    }

    /**
     * 请求
     * @param $url
     * @param $data
     * @param string $method
     * @param string $dataType
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request($url, $data, $method="post", $dataType="form_params")
    {
        $client = new Client();
        try {
            $res = $client->request($method, $url, [
                $dataType => $data,
                'connect_timeout' => 5
            ]);
        } catch (\Exception $e) {
            info("position:{$url},data:".var_export($data,true).",message:".$e->getMessage());
            return false;
        }

        $body = $res->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            info("position:{$url},data:{$body},message:响应无法解析");
        }

        return $data;
    }
}