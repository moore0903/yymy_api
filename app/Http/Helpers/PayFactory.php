<?php

namespace App\Http\Helpers;

class PayFactory
{
    static private $instances = [];

    static public function getInstance($type)
    {
        if (!isset(self::$instances[$type])) {
            $instance = null;
            switch ($type) {
                case 1:
                    $instance = new Alipay;
                    break;
                case 2:
                    $instance = new WechatPay;
                    break;
                default:
                    return null;
            }
            self::$instances[$type] = $instance;
        }

        return self::$instances[$type];
    }
}