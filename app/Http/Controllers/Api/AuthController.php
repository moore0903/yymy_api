<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2017/12/20
 * Time: 14:31
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\ApiController;
use App\Models\ThirdUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    /**
     * 用户登录/注册
     * @param Request $request->username,password
     * @return mixed
     */
    public function login(Request $request){
        /**
         * 供ios上线提供测试账号
         */
        if($request->phone != '18872212201'){
            $msg = User::validate_code($request->phone,$request->code,User::statusString(User::TYPE_LOGIN));
            if($msg)
                return $this->error($msg,'-10004');
        }

        $user = User::where('name',$request->phone)->first();
        if(!isset($user)){
            $user = User::create([
                'name' => $request->phone,
                'email' => $request->phone.'@'.$request->phone,
                'password' => bcrypt(str_random(8)),
                'baby_sex' => '0',
                'register_type' => 1
            ]);
        }

        $token = User::create_token($user);
        User::checkAndBindYch($user);
        return $this->success(['info'=>User::userInfoReturn($user,$token)]);
    }

    /**
     * 各模块发送验证码统一接口
     * @param Request $request
     * @return mixed
     */
    public function send_sms(Request $request){
        $phone=$request->phone;
        $method=$request->type;

        if(empty($phone)){
            return $this->error('请填写手机号码','-10001');
        }

        if($method == User::TYPE_MODIFY_BIND){
            $user = User::where('name',$phone)->first();
            if(isset($user))
                return $this->error('该手机号已注册','-10002');
        }

        try {
            $result=User::send_sms($phone,User::statusString($method));
            return $this->message('发送成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * 第三方登录或注册
     * @param Request $request
     * @return mixed
     */
    public function auth_handle(Request $request){
        if(empty($request->standard_id)) return $this->error('请传入第三方登录的唯一id','-10006');
        $thirdUser = ThirdUser::where('standard_id','=',$request->standard_id)->where('platform','=',$request->platform)->first();
        $is_binding = 0;
        if(isset($thirdUser)){
            $user = User::find($thirdUser->user_id);
            if(!isset($user)){
                $is_binding = 1;
            }
        }else{
            $is_binding = 1;
        }
        if($is_binding == 0){
            /**
             * 第三方登录时,将头像和昵称放到用户表中
             */
            $user->third_name = $thirdUser->nick_name;
            $user->third_avatar = $thirdUser->avatar;
            $user->save();

            $access_token = User::create_token($user);

            User::checkAndBindYch($user);
            return $this->success(['info'=>User::userInfoReturn($user,$access_token,$is_binding)]);
        }
        return $this->success(['info'=>[
            'is_binding'=>$is_binding,
        ]]);
    }


    /**
     * 绑定手机号
     * @param Request $request
     * @return mixed
     * @param phone int 手机号码
     * @param code int 验证码
     */
    public function binding_phone(Request $request){
        $msg = User::validate_code($request->phone,$request->code,User::statusString(User::TYPE_BIND));
        if($msg) return $this->error($msg,'-10004');
        if(empty($request->standard_id)) return $this->error('请传入第三方登录的唯一id','-10006');
        $thirdUser = ThirdUser::where('standard_id','=',$request->standard_id)->where('platform','=',$request->platform)->first();
        if(isset($thirdUser)){
            $user = User::find($thirdUser->user_id);
            $user->name = $request->phone;
            $user->third_name = $request->nick_name;
            $user->third_avatar = $request->avatar;
            $user->save();
            $thirdUser->user_id = $user->id;
            $thirdUser->nick_name = $request->nick_name;
            $thirdUser->name = $request->name??'';
            $thirdUser->avatar = $request->avatar;
            $thirdUser->extdata = json_encode($request->extdata);
            $thirdUser->save();
        }else{
            $user = User::where('name',$request->phone)->first();
            if(!isset($user)){
                $user = User::create([
                    'name' => $request->phone,
                    'user_name' => '',
                    'email' => $request->platform.$request->nick_name,
                    'password' => bcrypt(str_random(8)),
                    'avatar' => '',
                    'baby_birthday' => '0',
                    'baby_sex' => '0',
                    'register_type' => 2,
                    'third_name' => $request->nick_name,
                    'third_avatar' => $request->avatar
                ]);
            }else{
                $user->third_name = $request->nick_name;
                $user->third_avatar = $request->avatar;
                $user->save();
            }
            ThirdUser::create([
                'user_id' => $user->id,
                'standard_id' => $request->standard_id,
                'platform' => $request->platform,
                'nick_name' => $request->nick_name,
                'name' => $request->name??'',
                'avatar' => $request->avatar,
                'extdata' => json_encode($request->extdata),
            ]);
        }
        $access_token = User::create_token($user);
        User::checkAndBindYch($user);
        return $this->success(['info'=>User::userInfoReturn($user,$access_token)]);
    }









}