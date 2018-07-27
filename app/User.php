<?php

namespace App;

use App\Models\ArticleComments;
use App\Models\Articles;
use App\Models\System;
use App\Models\ThirdUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Illuminate\Http\Request;
use App\Http\Helpers\Helper;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $guarded=[];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($username) {
        return $this->where('name', $username)->first();
    }

//    public function getNameAttribute($value){
//        return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$value);
//    }

    public function getUserNameAttribute($value){
        if(empty($value)){
            if(!empty($this->third_name)){
                return $this->third_name;
            }else{
                return preg_replace('/(1[35789]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$this->name);
            }
        }
        return $value;
    }

    public function getAvatarAttribute($value){
        if($value == '' || $value == '/nopic.jpg'){
            if($this->third_avatar != ''){
                return $this->third_avatar;
            }
        }
        $avatar = empty($value)?'/nopic.jpg':$value;
        if (!preg_match('/(http:\/\/)|(https:\/\/)/i', $avatar)) {
            $avatar = asset($avatar);
        }
        return $avatar;
    }

    /**
     * 用户收藏文章 中间表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collects(){
        return $this->belongsToMany(Articles::class,'user_collect_article','user_id','article_id')->withTimestamps();
    }

    /**
     * 第三方登录表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thirdUser(){
        return $this->hasMany(ThirdUser::class,'user_id');
    }


    /**
     * 新生成token并保持旧token记录不超过5条
     * @param $user
     * @return mixed
     */
    public static function create_token($user){
        $token = $user->createToken('access token')->accessToken;
        $query=\DB::table('oauth_access_tokens');
        if($query->where('user_id',$user->id)->count()>5){
            $query->where('user_id',$user->id)->orderBy('created_at','asc')->limit(1)->delete();
        }
        return $token;
    }


    /**
     * 根据手机号发送短信
     * @param $phone
     * @return int
     */
    public static function send_sms($phone,$method){
        $rand = rand('1000','9999');
        $easySms = new EasySms(config('sendSMS'));
        $hasSucceed = false;
        try{
            $easySms->send($phone,[
                'template' => 'SMS_139232230',
                'data' => [
                    'code' => $rand
                ],
            ]);

            Redis::set($method.'|'.$phone,$rand);
            Redis::expire($method.'|'.$phone,1800);
        } catch (NoGatewayAvailableException $e){
            $results = $e->results;
            $result = $results['aliyun']['exception'];
            info($result->raw);
            $message = $result->raw["Message"];
            logger($message);

            if (strpos($message, "分钟级流控")) {
                $errmsg = "用户每分钟只能发送1次，请稍后重试";
            } else if (strpos($message, "小时级流控")) {
                $errmsg = "用户每小时只能发送5次，请稍后重试";
            } else if (strpos($message, "天级流控")) {
                $errmsg = "用户每天只能发送10次";
            } else {
                $errmsg = "发送错误";
            }
            throw new \Exception($errmsg);

        }
    }


    /**
     * 验证验证码
     * @param $phone 手机号
     * @param $code 验证码
     * @return string
     */
    public static function validate_code($phone,$code,$method){
        $redis_phone_code = Redis::get($method.'|'.$phone);

        if(empty($redis_phone_code))
            return '验证码已过期,请重新发送';

        if($redis_phone_code !== $code)
            return '验证码错误，请重新输入';

        Redis::del($method.'|'.$phone);
    }

    /**
     * 获取用户的距离今天的年月
     * @return array
     */
    public function get_aged(){
        if($this->user_type == BabyGrowthProcess::TYPE_GESTATION){
            $age_day = $this->time_tran($this->pregnancy);
            $age_m = floor($age_day/7);
            $age_y = 0;
        }elseif($this->user_type == BabyGrowthProcess::TYPE_BABY){
            $age_day = $this->time_tran($this->baby_birthday);
            $age_y = floor(($age_day/30)/12);
            $age_m = floor(($age_day/30)%12);
        }
        return ['age_y'=>$age_y,'age_m'=>$age_m];
    }


    /**
     * 计算距离现多少天
     * @param $show_time
     * @return float
     */
    private static function time_tran($show_time){
        header("Content-type: text/html; charset=utf8");
        date_default_timezone_set("Asia/Shanghai");   //设置时区
        $dur = time() - $show_time;
        return floor($dur / 86400);
    }

    /**
     * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
     * @param $string
     * @return string
     */
    public static function substr_cut($string){
        $strlen     = mb_strlen($string, 'utf-8');
        $firstStr     = mb_substr($string, 0, 3, 'utf-8');
        $lastStr     = mb_substr($string, -1, 1, 'utf-8');
        if(strlen($string) < 3) return $string;
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($string, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }


    /**
     * 图片压缩
     * @param $time
     * @param $image
     * @param float $percent
     * @return mixed
     */
    public static function compress($time,$image,$percent=0.5){
        $image = Image::make(asset('public/storage/upload/'.$time.'/'.$image));
        $height = $image->height();
        $width = $image->width();
        $img = Image::cache(function($item)use($image,$height,$width,$percent){
            $item->make($image)->resize($width*$percent,$height*$percent);
        },10,true);
        return $img->response('jpg');
    }


    /**
     * 用户信息的返回
     * @param User $user
     * @param $token
     * @return array
     */
    public static function userInfoReturn(User $user, $token = '', $is_binding = null){
        $data = [
            'user_name' => $user->user_name??$user->name,
            'phone' => $user->name,
            'avatar' => $user->avatar,
            'baby_birthday' => strtotime($user->baby_birthday??date('Y-m-d',time())),
            'baby_sex' => $user->baby_sex,
            'register_type' => $user->register_type,
            'has_bound_ych' => $user->ych_id ? 1 : 0,
            'has_send_coin' => $user->has_send_coin,
            'leguer_info' => null,
        ];
        if($token) $data['access_token'] = 'Bearer '.$token;
        if($is_binding !== null) $data['is_binding'] = $is_binding;

        if ($data["has_bound_ych"]) {
            $info = Helper::call_self_service("get_leaguer_info_inside", ["ych_id" => $user->ych_id]);
            if ($info && isset($info["status"]) && !($info["status"] < 0) ) {
                $data["leguer_info"] = $info["data"]["info"];
            }
        }

        return $data;
    }


    /**
     * 文章浏览记录
     * @param User $user
     * @param $article_id
     */
    public static function article_browse_log(User $user,$article_id){
        if(DB::table('article_browse_log')->where('user_id',$user->id)->where('article_id',$article_id)->count() > 0){
            DB::table('article_browse_log')->where('user_id',$user->id)->where('article_id',$article_id)->delete();
        }
        if($user->articleBrowseLog->count() >= 100){
            $log = $user->articleBrowseLog()->orderBy('article_browse_log.id','asc')->first();
            DB::table('article_browse_log')->where('id',$log->pivot->id)->delete();
        }
        $user->articleBrowseLog()->attach($article_id);
    }


    /**
     * 注册类型转换
     * @return string
     */
    public function register_type_string(){
        $string = '';
        switch ($this->register_type){
            case 1:
                $string = '验证码注册';
                break;
            case 2:
                $string = '第三方注册';
                break;
        }
        return $string;
    }

    /**
     * 宝宝转换
     * @return string
     */
    public static function baby_sex_string($sex){
        $string = '';
        switch ($sex){
            case 1:
                $string = '男';
                break;
            case 2:
                $string = '女';
                break;
            default:
                $string = '未知';
                break;
        }
        return $string;
    }



    /**
     * 状态转中文
     * @param $stat
     * @return string
     */
    public static function statusString($stat) {
        switch($stat) {
            case User::TYPE_LOGIN:
                return 'phone_code_login';
                break;
            case User::TYPE_BIND:
                return 'binding_phone';
                break;
            case User::TYPE_MODIFY_BIND:
                return 'modify_phone';
                break;
            default:
                return '未知错误';
                break;
        }
    }


    const TYPE_LOGIN = 1;         //手机号登录
    const TYPE_BIND = 2;          //绑定手机
    const TYPE_MODIFY_BIND = 3;   //修改手机号


    /**
     * 注册类型转中文
     * @param $type
     * @return string
     */
    public static function typeToChinese($type){
        switch($type){
            case User::TYPE_CODE:
                return '验证码注册';
                break;
            case User::TYPE_THIRD:
                return '第三方注册';
                break;
            default:
                return '未定义';
                break;
        }
    }

    const TYPE_CODE=1;      //验证码
    const TYPE_THIRD=2;     //第三方

    /**
     * 检测用户是否有ych账号并自动绑定
     * @param $user
     */
    public static function checkAndBindYch($user){
        if (!$user->name || $user->ych_id)  return null;

        $ret = Helper::call_self_service("ych_mall/get_leaguer_by_phone", ["phone"=>$user->name]);
        if ($ret && $ret["code"] > 0) {
              $user->ych_id = $ret["data"]["info"]["LeaguerID"];
              $user->save();
          }
    }


}
