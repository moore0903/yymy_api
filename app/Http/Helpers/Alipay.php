<?php

namespace App\Http\Helpers;

use Omnipay\Omnipay;

class Alipay
{
    private $gateway = null;

    public function __construct()
    {
        $gateway = Omnipay::create('Alipay_AopApp');
        $gateway->setSignType('RSA2'); //RSA/RSA2
        $gateway->setAppId(env('ALI_APP_ID'));
        $gateway->setPrivateKey(env('ALI_PRIVATE_KEY'));
        $gateway->setAlipayPublicKey(env('ALI_PUBLIC_KEY'));
        $gateway->setNotifyUrl('http://'.env('API_DOMAIN').'/ych_mall/alipay_notify');
        $this->gateway = $gateway;
    }

    public function purchase($orderNumber, $money, $goodsName = "充值")
    {
        $request = $this->gateway->purchase();
        $request->setBizContent([
            'subject'      => $goodsName,
            'out_trade_no' => $orderNumber,
            'total_amount' => $money,
            'product_code' => 'QUICK_MSECURITY_PAY',
        ]);

        /**
         * @var AopTradeAppPayResponse $response
         */
        $response = $request->send();
        if (!$response->isSuccessful()) {
            return false;
        }

        $orderString = $response->getOrderString();
        return ["string" => $orderString];
    }

    public function notify()
    {
        $request = $this->gateway->completePurchase();
        $request->setParams(request()->all());
        try {
            $response = $request->send();

            if($response->isPaid()){
                return true;
            }else{
               return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}