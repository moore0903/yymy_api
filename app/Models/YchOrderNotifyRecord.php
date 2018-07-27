<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YchOrderNotifyRecord extends Model
{
    protected $table='ych_order_notify_record';

    protected $guarded=[];

    static public function record($orderNumber, $params, $payType)
    {
        return YchOrderNotifyRecord::create([
            "order_number" => $orderNumber,
            "params" => json_encode($params, 0, 2),
            "pay_type" => $payType,
        ]);
    }
}
