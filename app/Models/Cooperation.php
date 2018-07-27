<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cooperation extends Model
{

    protected $table='cooperation';

    protected $guarded=[];


    public function user(){
        return $this->belongsTo(\App\User::class,'user_id','id');
    }
}
