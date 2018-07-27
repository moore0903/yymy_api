<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2017/10/16
 * Time: 17:49
 */

namespace App\Http\Helpers;
use App\Http\Middleware\AesEncrypt;

use App\Models\ApiLog;
use App\Models\Systems;
use Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;


trait ApiResponse
{
    /**
     * @var int
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {

        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = [])
    {
        $name=\Route::currentRouteName();
//        if($name!=='login'){
            ApiLog::create([
                'url' => Request::url(),
                'body' => json_encode(Request::all()),
                'sql' => json_encode(Systems::$sqlArray),
                'response_body' => json_encode($data)
            ]);
//        }

        $aesEncrypt = new AesEncrypt();
        $data = $aesEncrypt->encrypt(json_encode($data));
        return Response::json($data,FoundationResponse::HTTP_OK,$header);
    }

    /**
     * @param $status
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($status, array $data, $code = null){

        if ($code){
            $this->setStatusCode($code);
        }

        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];
        $data = array_merge($status,$data);
        return $this->respond($data);

    }

    /**
     * @param $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error'){

        return $this->setStatusCode($code)->message($message,$status);
    }

    /**
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function message($message, $status = "0"){

        return $this->status($status,[
            'msg' => $message
        ]);
    }

    /**
     * 返回错误信息
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function error($message,$code = '400',$status = '0'){
        return $this->status($status,[
            'msg' => $message
        ],$code);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "Internal Error!"){

        return $this->failed($message,FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function created($message = "created")
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);

    }

    /**
     * @param $data
     * @param string $status
     * @return mixed
     */
    public function success($data, $status = "0"){

        return $this->status($status,compact('data'));
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message,Foundationresponse::HTTP_NOT_FOUND);
    }

}