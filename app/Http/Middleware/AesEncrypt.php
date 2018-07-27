<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2017/12/18
 * Time: 15:37
 */

namespace App\Http\Middleware;

use App\Http\Helpers\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AesEncrypt
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $param = json_decode($this->decrypt($request->p),true);
        $token = json_decode($this->decrypt($request->t),true);
        if(count($param) <= 0) $param = [];
        foreach ($param as $key => $value){
            $request->offsetSet($key,$value);
        }
        if(count($token) <= 0) $token = [];
        foreach ($token as $key => $value){
            $request->offsetSet('token_'.$key,$value);
        }
//        Log::info($request->url());
//        Log::info(json_encode($param));
        return $next($request);
    }

    /**
     * 加密key
     * @param string $k
     * @return string
     */
    public function getKey($k = '')
    {
        $k = $k ? $k : "youyou";
        $key = hash('md5', $k);
        $key = substr($key, 0, 16);
        return $key;
    }

    /**
     * 加密
     * @param $content
     * @param string $key
     * @return string
     */
    public function encrypt($content,$key = "")
    {
        $key =  $this->getKey($key);
        return  $this->AES_ecb128_encrypt($content,$key);
    }

    /**
     * 解密
     * @param $content
     * @param string $key
     * @return string
     */
    public function decrypt($content,$key = "")
    {
        $key =  $this->getKey($key);
        return $this->AES_ecb128_decrypt($content,$key);
    }


    /**
     * pkcs7补码
     * @param string $string  明文
     * @param int $blocksize Blocksize , 以 byte 为单位
     * @return String
     */
    private function addPkcs7Padding($string, $blocksize = 16) {
        $len = strlen($string); //取得字符串长度
        $pad = $blocksize - ($len % $blocksize); //取得补码的长度
        $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
        return $string;
    }

    /**
     * 除去pkcs7 padding
     *
     * @param String 解密后的结果
     *
     * @return String
     */
    private function stripPkcs7Padding($string){
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);

        if(preg_match("/$slastc{".$slast."}/", $string)){
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }




    /**
     * 填充
     * @param $text
     * @param $padLen
     * @return string
     */
    public function pad2Length($text, $padLen){
        $len = strlen($text)%$padLen;
        if ($len == 0)
        {
            return $text;
        }
        $res = $text;
        $span = $padLen-$len;
        for($i = 0; $i < $span; $i++){
            $res .= chr(0);
        }
        return $res;
    }

    /**
     * aes 128 ecb 加密
     * @param $content
     * @param $key
     * @return string
     */
    private  function AES_ecb128_encrypt($content,$key){
        //$content = $this->pad2Length($content,32);
        $content = $this->addPkcs7Padding($content);
        $cipher_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$key,$content,MCRYPT_MODE_ECB);
        # 对密文进行 base64 编码
        //$cipher_text_hex = bin2hex($cipher_text);
        $cipher_text_hex = base64_encode($cipher_text);
        return $cipher_text_hex;
    }

    /**
     * aes 128 ecb 解密
     * @param $content
     * @param $key
     * @return string
     */
    private function AES_ecb128_decrypt($content,$key){
        //$cipher_text_dec = hex2bin($content);
        $cipher_text_dec = base64_decode($content);
        $cipher_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$key,$cipher_text_dec,MCRYPT_MODE_ECB);
        //return rtrim($cipher_text,"\0");
        return $this->stripPkcs7Padding($cipher_text);
    }



    public function encrypt_aes_base64($content,$key = "")
    {
        $key =  $this->getKey($key);
        return  $this->AES_ecb128_encrypt_base64($content,$key);
    }

    private  function AES_ecb128_encrypt_base64($content,$key){
        $content = $this->pad2Length($content,16);
        $cipher_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$key,$content,MCRYPT_MODE_ECB);
        # 对密文进行 base64 编码
        $cipher_text_base64 = base64_encode($cipher_text);
        return $cipher_text_base64;
    }


    public function decrypt_aes_base64($content,$key = "")
    {
        $key =  $this->getKey($key);
        return $this->AES_ecb128_decrypt_base64($content,$key);
    }

    private function AES_ecb128_decrypt_base64($cipher_text_base64,$key){
        $cipher_text_dec = base64_decode($cipher_text_base64);
        $cipher_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$key,$cipher_text_dec,MCRYPT_MODE_ECB);
        return rtrim($cipher_text,"\0");
    }
}