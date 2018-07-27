<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ArticleComments extends Model
{
    protected $table = 'article_comments';

    protected $guarded = [];

    /**
     * 文章
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article(){
        return $this->belongsTo(Articles::class,'article_id');
    }

    /**
     * 用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * 时间格式化
     * @return false|string
     */
    public function time_format(){
        $date = $this->comment_time;
        $todaystart = strtotime(date('Y-m-d'.'00:00:00',time()));
        $todayend = strtotime(date('Y-m-d'.'00:00:00',time()+3600*24));
        $thisYearStart = strtotime(date('Y-'.'01-01 00:00:00',time()));
        $time = strtotime($date);
        if($todaystart <= $time && $time < $todayend){
            return date('H:i',$time);
        }else if($thisYearStart <= $time && $time < $todaystart){
            return date('m-d',$time);
        }else if($thisYearStart > $time){
            return date('Y-m-d',$time);
        }
    }
}
