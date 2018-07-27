<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(
    ['namespace'=>'Api','middleware'=>'aesEncrypt'],
    function(){
        Route::group(   //登录状态
            ['middleware'=>'auth:api'],
            function(){
                Route::group(['prefix' => 'user'],function(){
                    Route::post('get_user_info','UserController@get_user_info'); // 拉取个人信息
                    Route::post('modify_info','UserController@modify_info'); // 修改用户资料
                    Route::post('modify_phone','UserController@modify_phone'); // 修改绑定手机号

                    Route::post('submit_article_comment','UserController@submit_article_comment'); // 提交文章评论
                    Route::post('get_my_article_comment','UserController@get_my_article_comment'); // 获取我的评论列表

                    Route::post('article_collect','UserController@article_collect'); // 文章收藏
                    Route::post('get_my_article_collects','UserController@get_my_article_collects'); // 获取我的收藏列表
                    Route::post('del_collect','UserController@del_collect'); // 删除我的收藏
                });

                Route::group(['prefix' => 'ych_mall'], function(){
                    Route::post('get_leaguer_info','YchMallController@get_leaguer_info');  //获取用户信息
                    Route::post('send_join_veri_code','YchMallController@send_join_veri_code');  //发送绑卡短信
                    Route::post('leaguer_apply','YchMallController@leaguer_apply');  //会员入会

                    Route::group(["middleware"=>"checkBindYchCard"], function () {
                        Route::post('get_goods_list','YchMallController@get_goods_list');  //获取商品列表
                        Route::post('get_leaguer_play_log','YchMallController@get_leaguer_play_log');  //获取用户消费记录
                        Route::post('get_scheme','YchMallController@get_scheme');  //获取扫描机器信息
                        Route::post('scan_code','YchMallController@scan_code');  //会员扫码
                        Route::post('get_remote_trans','YchMallController@get_remote_trans');  //根据交易ID获取扫码结果
                        Route::post('create_order','YchMallController@create_order');  //创建订单
                        Route::post('get_order_page_list','YchMallController@get_order_page_list');  //获取订单列表
                        Route::post('get_lgpagtit_details','YchMallController@get_lgpagtit_details');  //套票信息
                        Route::post('get_lgcoupon_remain','YchMallController@get_lgcoupon_remain');  //获取优惠券可用数量
                        Route::post('get_leaguer_coupon_list','YchMallController@get_leaguer_coupon_list');  //获取优惠券列表
                        Route::post('get_leaguer_pagtit_list','YchMallController@get_leaguer_pagtit_list');  //获取套票列表
                        Route::post('get_pagtit_detail','YchMallController@get_pagtit_detail');  //获取套票详情
                        Route::post('send_coin','YchMallController@send_coin');  //赠送代币
                    });

                });
            }
        );
        Route::any('alipay_notify', 'YchMallController@alipay_notify')->prefix("ych_mall");
        Route::any('wechatpay_notify', 'YchMallController@wechatpay_notify')->prefix("ych_mall");
        Route::post('get_leaguer_by_phone', 'YchMallController@get_leaguer_by_phone')->prefix("ych_mall");
        Route::post('get_leaguer_info_inside','YchMallController@get_leaguer_info_inside');  //获取用户信息



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////以下为未登录状态路由




        Route::group(  //未登录状态
            ['middleware'=>'api'],
            function(){
                Route::group(['prefix' => 'user'],function(){
                    Route::post('login','AuthController@login');    //登录
                    Route::post('send_sms','AuthController@send_sms');    //统一发送验证码
                    Route::post('auth_handle','AuthController@auth_handle');  //第三方登录
                    Route::post('binding_phone','AuthController@binding_phone'); //第三方登录绑定手机号
                });

                Route::post('app_update','HomeController@app_update');  //app更新

                Route::post('image_upload','UploaderController@imageUpload');    //图片上传接口

                Route::group(['prefix' => 'home'],function(){
                    Route::post('get_index_list','HomeController@get_index_list');    //首页接口
                    Route::post('get_catalog_list','HomeController@get_catalog_list');    //获取栏目列表
                    Route::post('get_article_list','HomeController@get_article_list');    //根据栏目ID获取文章列表
                    Route::post('get_article_info','HomeController@get_article_info');    //根据文章ID获取文章详情
                    Route::post('get_article_api_info','HomeController@get_article_api_info');    //根据文章ID获取文章详情    API
                    Route::post('article_search','HomeController@article_search');    //文章搜索
                    Route::post('get_hot_search','HomeController@get_hot_search');    //热门搜索

                    Route::post('submit_feedback','HomeController@submit_feedback');  //提交意见反馈
                    Route::post('submit_cooperation','HomeController@submit_cooperation');  //提交商务合作

                    Route::post('get_article_comments','HomeController@get_article_comments');  //获取文章评论

                    /**
                     * V1.0.1版本
                     */
                    Route::post('v101/get_index_list','HomeController@get_index_list_v101');    //首页接口
                });

                Route::group(['prefix' => 'activity'],function(){
                    Route::post('get_activity_list','ActivityController@get_activity_list');   //获取活动列表
                    Route::post('get_activity_info','ActivityController@get_activity_info');   //获取活动详情
                    Route::post('get_activity_api_info','ActivityController@get_activity_api_info');   //获取活动详情   API
                });

                Route::group(['prefix' => 'shop'],function(){
                    Route::post('get_shop_list','ShopController@get_shop_list');   //获取门店列表
                    Route::post('get_shop_info','ShopController@get_shop_info');   //获取门店详情
                });

                Route::group(['prefix' => 'web'],function(){
                    //Route::middleware('page-cache')->get('article_info/{id}','WebController@article_info');
//                    Route::middleware('page-cache')->get('article_share/{id}','WebController@article_share');
//                    Route::middleware('page-cache')->get('activity_info/{id}','WebController@activity_info');
//                    Route::middleware('page-cache')->get('shop_detail/{id}','WebController@shop_detail');

                    Route::get('article_info/{id}','WebController@article_info');
                    Route::get('article_share/{id}','WebController@article_share');
                    Route::get('activity_info/{id}','WebController@activity_info');
                    Route::get('shop_detail/{id}','WebController@shop_detail');
                });

                Route::group(['prefix' => 'web_site'],function(){
                    Route::post('get_banner','WebSiteController@get_banner');   //获取banner
                    Route::post('get_home_activites','WebSiteController@get_home_activites');   //获取首页推荐活动
                    Route::post('get_activites','WebSiteController@get_activites');   //获取活动列表
                    Route::post('get_activity_info','WebSiteController@get_activity_info');   //获取活动详情
                    Route::post('submit_cooperation','WebSiteController@submit_cooperation');   //提交商务合作
                    Route::post('get_jobs','WebSiteController@get_jobs');   //获取招聘信息
                    Route::post('get_job_info','WebSiteController@get_job_info');   //获取招聘详情
                });
            }
        );
    }
);

