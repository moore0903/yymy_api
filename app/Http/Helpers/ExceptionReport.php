<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2017/10/17
 * Time: 11:47
 */
namespace App\Http\Helpers;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class ExceptionReport
{
    use ApiResponse;

    /**
     * @var Exception
     */
    public $exception;
    /**
     * @var Request
     */
    public $request;

    /**
     * @var
     */
    protected $report;

    /**
     * ExceptionReport constructor.
     * @param Request $request
     * @param Exception $exception
     */
    function __construct(Request $request, Exception $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @var array
     */
    public $doReport = [
        AuthenticationException::class => ['身份验证失败','-10000'],
        OAuthServerException::class=> ['身份验证失败','-10000'],
        ModelNotFoundException::class => ['该模型未找到','-10000'],
        NoGatewayAvailableException::class => ['网关不可用','-10000'],
        \PDOException::class => ['请核对入参','-10000'],
        FatalThrowableError::class => ['请传入正确参数','-10000']
    ];

    /**
     * @return bool
     */
    public function shouldReturn(){

        if (! ($this->request->wantsJson() || $this->request->ajax())){
            return false;
        }

        foreach (array_keys($this->doReport) as $report){

            if ($this->exception instanceof $report){

                $this->report = $report;
                return true;
            }
        }

        return false;

    }

    /**
     * @param Exception $e
     * @return static
     */
    public static function make(Exception $e){

        return new static(\request(),$e);
    }

    /**
     * @return mixed
     */
    public function report(){

        $message = $this->doReport[$this->report];

        return $this->failed($message[0],$message[1]);

    }

}