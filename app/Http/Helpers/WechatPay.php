<?php

namespace App\Http\Helpers;

use Omnipay\Omnipay;

class WechatPay
{
    private $gateway = null;

    public function __construct()
    {
        $gateway    = Omnipay::create('WechatPay_App');
        $gateway->setAppId(env('WECHAT_APP_ID'));
        $gateway->setMchId(env('WECHAT_MCH_ID'));
        $gateway->setApiKey(env('WECHAT_APP_KEY'));
        $gateway->setNotifyUrl('http://'.env('API_DOMAIN').'/ych_mall/wechatpay_notify');
        $this->gateway = $gateway;
    }

    public function purchase($orderNumber, $money, $goodsName = "充值")
    {
        $order = [
            'body'              => $goodsName,
            'out_trade_no'      => $orderNumber,
            'total_fee'         => $money * 100,
            'spbill_create_ip'  => request()->getClientIp(),
            'fee_type'          => 'CNY',
        ];

        try {
            $request  = $this->gateway->purchase($order);
            $response = $request->send();

            if ($response->isSuccessful()) {
                return $response->getAppOrderData(); //For WechatPay_App
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function notify()
    {
        $response = $this->gateway->completePurchase([
            'request_params' => file_get_contents('php://input')
        ])->send();

        if ($response->isPaid()) {
            return true;
        }else{
            return false;
        }
    }
}