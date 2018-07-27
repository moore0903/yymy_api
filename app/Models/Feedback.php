<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{

    protected $table='feedback';

    protected $guarded=[];

    use SoftDeletes;

    public function user(){
        return $this->belongsTo(\App\User::class,'user_id','id');
    }
}
