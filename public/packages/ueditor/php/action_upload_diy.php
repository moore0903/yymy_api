<?php
require "../../../../vendor/autoload.php";

use App\Http\Middleware\AesEncrypt;
use Dotenv\Dotenv;

$fileInfo = $_FILES[$CONFIG["imageFieldName"]];

$tempFilePath = "/tmp/".$fileInfo["name"];
$ret = move_uploaded_file($fileInfo["tmp_name"], $tempFilePath);
if (!$ret) var_dump("false");

//读取提交地址
$dotenv = new Dotenv("../../../../");
$dotenv->load();
$serverUrl = "http://".getenv("API_DOMAIN")."/image_upload";

$curl = curl_init();
curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
$data = array($CONFIG["imageFieldName"].'[]' => new \CURLFile(realpath($tempFilePath)));
curl_setopt($curl, CURLOPT_URL, $serverUrl);
curl_setopt($curl, CURLOPT_POST, 1 );
curl_setopt($curl, CURLOPT_HTTPHEADER, ["Accept: application/json, text/plain, */*"] );
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_USERAGENT,"TEST");
$result = curl_exec($curl);

//删除文件
unlink($tempFilePath);

$aes = new AesEncrypt();
$ret = $aes->decrypt($result);
$ret = json_decode($ret, true);

$url = "";
$state = "上传错误";
if ($ret) {
    if ($ret["status"] < 0) {
        $state = $ret["message"];
    } else {
        $state = "SUCCESS";
        $url = $ret["data"]["path"];
    }
}

$data =  [
    "state" => $state,
    "url" => $url,
    "title" => "",
    "original" => "",
    "type" => "",
    "size" => ""
];

return json_encode($data);
