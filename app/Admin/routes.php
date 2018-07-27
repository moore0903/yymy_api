<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    /**
     * APP 管理
     */
    $router->resource('catalog', CatalogController::class);
    $router->resource('article', ArticleController::class);
    $router->resource('shop', ShopController::class);
    $router->resource('activity', ActivityController::class);
    $router->resource('user', UserController::class);
    $router->resource('cooperation', CooperationController::class);
    $router->resource('feedback', FeedbackController::class);
    $router->resource('search', SearchController::class);
    $router->resource('ads', AdsController::class);
    $router->resource('app_update', AppUpdateController::class);
    $router->resource('article_comment', ArticleCommentController::class);
    $router->resource('aliyun_push', AliyunPushController::class);

    $router->get('api/get_city', 'ShopController@get_city');
    $router->get('api/get_town', 'ShopController@get_town');

    $router->post('editorUpload', 'UploadController@editorUpload');

    /**
     * 网站管理
     */
    $router->resource('web_site_banner', WebSiteBannerController::class);
    $router->resource('web_site_activity', WebSiteActivityController::class);
    $router->resource('web_site_cooperation', WebSiteCooperationController::class);
    $router->resource('web_site_job', WebSiteJobController::class);


});
