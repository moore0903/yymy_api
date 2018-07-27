<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2017/12/13
 * Time: 18:15
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Helpers\AliyunOpenAPIPush;
use App\Models\Activity;
use App\Models\Ads;
use App\Models\AppUpdateMessage;
use App\Models\ArticleComments;
use App\Models\Articles;
use App\Models\Catalogs;
use App\Models\Cooperation;
use App\Models\Feedback;
use App\Models\Search;
use App\Models\Shop;
use App\Models\Systems;
use App\Permission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HomeController extends ApiController
{

    /**
     * 首页接口
     * @return mixed
     */
    public function get_index_list(){
        $ads = Ads::where('status',Systems::STATUS_YES)->orderBy('sort')->orderByDesc('created_at')->limit(4)->get(); //TODO 需求是三张,应前端要求,暂时修改为四张

        $ads = $ads->map(function($item){
            $data = $item->get_result_data();
            unset($item);
            return $data;
        });

        $recommend = Articles::join('catalogs','catalogs.id','=','articles.catalog_id')
            ->select('articles.id','articles.title','articles.thumb',DB::raw('catalogs.title as catalog_title'),DB::raw('catalogs.color as catalog_color'))
            ->where('articles.status',Systems::STATUS_YES)->where('articles.index_rec_status',Systems::STATUS_YES)
            ->orderBy('articles.sort')->orderByDesc('articles.created_at')->limit(10)->get();

        $recommend = $recommend->map(function($item){
            $item->web_url = $item->web_url();
            $item->share = $item->share();
            $item->thumb = Systems::image_format($item->thumb);
            return $item;
        });

        return $this->success([
            'banner'=>$ads,
            'recommend'=>$recommend
        ]);
    }

    /**
     * 首页接口 v101
     * @return mixed
     */
    public function get_index_list_v101(){
        $ads = Ads::where('status',Systems::STATUS_YES)->orderBy('sort')->orderByDesc('created_at')->limit(4)->get();

        $ads = $ads->map(function($item){
            $data = $item->get_result_data();
            unset($item);
            return $data;
        });

        $recommend_article = Articles::join('catalogs','catalogs.id','=','articles.catalog_id')
            ->select('articles.id','articles.title','articles.thumb',DB::raw('catalogs.title as catalog_title'),DB::raw('catalogs.color as catalog_color'),'content')
            ->where('articles.status',Systems::STATUS_YES)->where('articles.index_rec_status',Systems::STATUS_YES)
            ->orderBy('articles.sort')->orderByDesc('articles.created_at')->limit(10)->get();

        $recommend_article = $recommend_article->map(function($item){
            $item->web_url = $item->web_url();
            $item->share = $item->share();
            $item->thumb = Systems::image_format($item->thumb);
            unset($item->content);
            return $item;
        });

        $recommend_activity = Activity::select('id','title',DB::raw('image as thumb'),'online_time','offline_time','address','content')
            ->where('index_rec_status',Systems::STATUS_YES)
            ->where('status',Systems::STATUS_YES)
            ->orderBy('sort')->orderByDesc('created_at')->get();

        $recommend_activity = $recommend_activity->map(function($item){
            $item->thumb = Systems::image_format($item->thumb);
            $item->type = $item->activity_time();
            $item->web_url = $item->web_url();
            $item->share = $item->share();
            unset($item->content);
            return $item;
        });

        return $this->success([
            'banner'=>$ads,
            'recommend_article'=>$recommend_article,
            'recommend_activity' => $recommend_activity
        ]);
    }

    /**
     * 获取栏目列表
     * @return mixed
     */
    public function get_catalog_list(){
        $catalogs = Catalogs::select('id','title','thumb','url')
            ->where('status',Systems::STATUS_YES)
            ->orderBy('sort')->orderByDesc('created_at')->get();

        $catalogs = $catalogs->map(function($item){
            $item->thumb = Systems::image_format($item->thumb);
            return $item;
        });

        return $this->success([
            'list'=>$catalogs
        ]);
    }

    /**
     * 根据栏目id获取文章
     * @param Request $request
     * @return mixed
     */
    public function get_article_list(Request $request){
        $catalog_id = (int)$request->catalog_id;

        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $catalog = Catalogs::find($catalog_id);

        $articles = Articles::select('id','title','thumb','content')
            ->where('status',Systems::STATUS_YES)->where('catalog_id',$catalog_id)
            ->orderBy('sort')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $articles = $articles->map(function($item)use($catalog){
            $item->web_url = $item->web_url();
            $item->share = $item->share();
            $item->catalog_title = $catalog->title;
            $item->thumb = Systems::image_format($item->thumb);
            unset($item->content);
            return $item;
        });

        return $this->success([
            'list' => $articles
        ]);
    }

    /**
     * APP 中调用接口详情
     * @param Request $request
     * @return mixed
     */
    public function get_article_api_info(Request $request){
        $article = Articles::select('id','title','thumb','catalog_id','content')->where('status',Systems::STATUS_YES)->find((int)$request->id);
        if(empty($article)) return $this->error('文章不可用','-10002');
        $article->catalog_title = $article->catalog->title;
        $article->web_url = $article->web_url();
        $article->share = $article->share();
        $article->thumb = Systems::image_format($article->thumb);
        unset($article->catalog_id,$article->catalog,$article->content);
        return $this->success(['info' => $article]);
    }

    /**
     * 根据文章ID获取文章详情
     * @param Request $request
     * @return mixed
     */
    public function get_article_info(Request $request){
        $article = Articles::where('status',Systems::STATUS_YES)->find((int)$request->id);
        if(empty($article)) return $this->error('文章不可用','-10002');  // TODO 错误代码
        $article->catalog_title = $article->catalog->title;
        $article->created_at_time = date('Y-m-d',strtotime($article->created_at));
        return $this->success([
            'info' => $article
        ]);
    }

    /**
     * 获取文章评论
     * @param Request $request
     * @return mixed
     */
    public function get_article_comments(Request $request){
        $user = $request->user('api');

        $article_id = (int)$request->article_id;
        $collect_state = 0;

        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $article = Articles::find($article_id);

        if(empty($article) || $article->status == Systems::STATUS_NO){
            return $this->error('文章ID不能为空！','-30001');
        }

        $article_comments = ArticleComments::join('users','users.id','=','article_comments.user_id')
            ->select('article_comments.content','article_comments.comment_time','article_comments.user_id')
            ->where('article_id',$article_id)->where('article_comments.status',Systems::STATUS_YES)->with('user')
            ->orderByDesc('article_comments.created_at')->offset($offset)->limit($pagesize)->get();
        $article_comments = $article_comments->map(function($item){
            $item->user_name = $item->user->user_name;
            $item->user_avatar = $item->user->avatar;
            $item->comment_time = $item->time_format();
            unset($item->user,$item->user_id);
            return $item;
        });

        if(isset($user)){
            $collect_state = $user->collects()->where('article_id',$article_id)->count();
        }

        return $this->success([
            'list' => $article_comments,
            'collect_state' => $collect_state,
            'share' => $article->share(),
            'count' => ArticleComments::where('article_id',$request->article_id)->where('status',Systems::STATUS_YES)->count()
        ]);
    }

    /**
     * 提交意见反馈
     * @param Request $request
     * @return mixed
     */
    public function submit_feedback(Request $request){
        $content = $request->contents;
        $user = $request->user('api');
        if(empty($content)) return $this->error('反馈内容不能为空','-10002');  // TODO 错误代码
        Feedback::create([
            'user_id' => isset($user)?$user->id:0,
            'content' => $content
        ]);
        return $this->message('反馈成功');
    }

    /**
     * 提交商务合作
     * @param Request $request
     * @return mixed
     */
    public function submit_cooperation(Request $request){
        $user = $request->user('api');
        if(empty($request->name)) return $this->error('联系人不能为空','-10002');  // TODO 错误代码
        if(empty($request->phone)) return $this->error('联系电话不能为空','-10002');  // TODO 错误代码
        Cooperation::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'remark' => $request->remark,
            'user_id' => isset($user)?$user->id:0
        ]);
        return $this->message('申请成功');
    }


    /**
     * 文章搜索
     * @param Request $request
     * @return mixed
     */
    public function article_search(Request $request){
        $content = $request->contents;

        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $articles = Articles::select('id','title','thumb','content')
            ->where('title','like','%'.$content.'%')->where('status',Systems::STATUS_YES)
            ->orderBy('sort')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $articles = $articles->map(function($item){
            $item->thumb = Systems::image_format($item->thumb);
            $item->web_url = $item->web_url();
            $item->share = $item->share();
            unset($item->content);
            return $item;
        });

        return $this->success([
            'list' => $articles
        ]);

    }

    /**
     * 热门搜索
     * @param Request $request
     * @return mixed
     */
    public function get_hot_search(Request $request){
        $pagesize = isset($request->pagesize)?$request->pagesize:10;

        $seach = Search::select('search')->orderBy('sort')->orderByDesc('created_at')->limit($pagesize)->get();

        return $this->success([
            'list' => $seach
        ]);
    }

    /**
     * App更新
     * @param Request $request
     * @return mixed
     */
    public function app_update(Request $request){
        $type = AppUpdateMessage::get_device_type();
        $appUpdate = AppUpdateMessage::select('version_code','version_name','download_url','upload_file','update_title','update_message')->where('app_id',$request->type??$type)->where('status',Systems::STATUS_YES)->orderByDesc('version_code')->first();
        if(empty($appUpdate))
            return $this->success(['info'=>[]]);
        if(empty($appUpdate->download_url)){
            $appUpdate->download_url = env('QINIU_URL').'/'.$appUpdate->upload_file;
        }
        unset($appUpdate->upload_file);
        return $this->success(['info'=>$appUpdate]);
    }

}




















