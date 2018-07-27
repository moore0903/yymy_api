<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    protected $table = 'articles';

    protected $guarded = [];

    /**
     * 栏目
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function catalog(){
        return $this->belongsTo(Catalogs::class,'catalog_id');
    }

    /**
     * 文章评论
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(){
        return $this->hasMany(ArticleComments::class,'article_id');
    }

    /**
     * 用户收藏文章 中间表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collects(){
        return $this->belongsToMany(User::class,'user_collect_article','article_id','user_id')->withTimestamps();
    }

    /**
     * 文章web URL
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function web_url(){
        return env('WEB_HOST').'/article_info?id='.$this->id;
    }

    /**
     * 分享内容
     * @return array
     */
    public function share(){
        return [
            'share_url' => env('WEB_HOST').'/article_share?id='.$this->id,
            'share_title' => $this->title,
            'share_desc'=>mb_substr(strip_tags(htmlspecialchars_decode($this->content)),0,50,'utf-8'),
            'share_logo'=>$this->thumb?Systems::image_format($this->thumb):asset('logo.png'),
        ];
    }



    /**
     * 状态转中文
     * @param $status
     * @return string
     */
    public static function statusToChinese($status){
        switch($status){
            case Articles::STATUS_AVAILABLE:
                return '可用';
                break;
            case Articles::STATUS_UNAVAILABLE:
                return '禁用';
                break;
            default:
                return '未定义';
                break;
        }
    }


    const STATUS_AVAILABLE =1;  //可用
    const STATUS_UNAVAILABLE=0; //禁用

}
