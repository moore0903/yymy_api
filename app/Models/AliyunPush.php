<?php

namespace App\Models;

use App\Http\Helpers\AliyunOpenAPIPush;
use Illuminate\Database\Eloquent\Model;

class AliyunPush extends Model
{
    protected $table='aliyun_push';

    protected $guarded=[];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if(empty($model->push_time)){
                $model->push_time = date('Y-m-d H:i:s',time());
            }
        });

        static::created(function($model){
            try {
                $aliyun_push = new AliyunOpenAPIPush($model);
                $aliyun_push->push();
                $model->save_status(2);
            }
            catch (\Exception $exception){
                logger($exception->getMessage());
                $model->save_status(4);
            }

        });
    }

    public function save_status($status){
        $this->push_status = $status;
        $this->save();
    }
}
