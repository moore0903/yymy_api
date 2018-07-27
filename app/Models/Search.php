<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{

    protected $table = 'search';

    protected $guarded = [];

    /**
     * 删除 7 天前的搜索记录
     */
    public static function schedule(){
        Search::where('created_at','<',date('Y-m-d H:i:s',time()-(60*24*7)))->delete();
    }
}
