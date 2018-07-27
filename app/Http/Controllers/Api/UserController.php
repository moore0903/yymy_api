<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2017/10/16
 * Time: 14:33
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AppCMS\ArticleController;
use App\Models\ArticleComments;
use App\Models\Articles;
use App\Models\System;
use App\Models\Systems;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends ApiController
{

    public function get_user_info(Request $request){
        $user = $request->user();
        return $this->success(['info'=>User::userInfoReturn($user)]);
    }

    /**
     * 修改用户资料
     * @param Request $request
     * @return mixed
     */
    public function modify_info(Request $request){
        $user = $request->user();
        $user = User::select('*')->where(['id'=>$user->id])->first();
        if(!empty($request->avatar))
            $user->avatar = $request->avatar;

        if(!empty($request->user_name))
            $user->user_name = $request->user_name;

        if(!empty($request->baby_birthday))
            $user->baby_birthday = date('Y-m-d',$request->baby_birthday);

        if(!empty($request->baby_sex))
            $user->baby_sex = $request->baby_sex;

        $user->save();

        return $this->success(['info'=>User::userInfoReturn($user)]);
    }

    /**
     * 绑定修改后的手机号
     * @param Request $request
     * @return mixed
     */
    public function modify_phone(Request $request){
        $user = $request->user();

        $old_user = User::where('name',$request->new_phone)->first();
        if(isset($old_user))
            return $this->error('该手机号已注册','-10002');

        $msg = User::validate_code($request->new_phone,$request->code,User::statusString(User::TYPE_MODIFY_BIND));
        if($msg)
            return $this->error($msg,'-10004');


        $user->name = $request->new_phone;
        $user->save();

        return $this->success(['info'=>User::userInfoReturn($user)]);
    }

    /**
     * 提交文章评论
     * @param Request $request
     * @return mixed
     */
    public function submit_article_comment(Request $request){
        $user = $request->user();
        if(empty($request->article_id)) return $this->error('文章ID不能为空','-40001');
        if(empty($request->contents)) return $this->error('评论内容不能为空','-10002');  // TODO 错误代码
        try{
            $article_comment = ArticleComments::create([
                'user_id' => $user->id,
                'article_id' => $request->article_id,
                'content' => $request->contents,
                'comment_time' => date('Y-m-d H:i:s',time()),
                'status' => 1
            ]);
        }catch (\PDOException $e){
            if($e->getCode() == '23000'){
                return $this->error('文章ID不可用','-30004');
            }
        }
        unset($article_comment->id,$article_comment->user_id,$article_comment->article_id,$article_comment->status,$article_comment->updated_at,$article_comment->created_at);
        $article_comment->comment_time = $article_comment->time_format();
        $article_comment->user_name = $user->user_name;
        $article_comment->user_avatar = $user->avatar;
        return $this->success([
            'info' => $article_comment,
            'count' => ArticleComments::where('article_id',$request->article_id)->where('status',Systems::STATUS_YES)->count()
        ]);
    }

    /**
     * 获取我的评论列表
     * @param Request $request
     * @return mixed
     */
    public function get_my_article_comment(Request $request){
        $user = $request->user();

        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $article_comments = ArticleComments::join('articles','articles.id','=','article_comments.article_id')
            ->join('catalogs','catalogs.id','=','articles.catalog_id')
            ->select('article_comments.content','article_comments.comment_time','article_comments.article_id',DB::raw('articles.title as article_title'),'articles.catalog_id',DB::raw('catalogs.title as catalog_title'),DB::raw('articles.thumb as article_thumb'))
            ->where('article_comments.user_id',$user->id)->where('article_comments.status',Systems::STATUS_YES)->with('article.comments')
            ->orderByDesc('article_comments.created_at')->offset($offset)->limit($pagesize)->get();

        $article_comments = $article_comments->map(function($item){
            $item->article_comment_count = $item->article->comments->count();
            $item->comment_time = $item->time_format();
            $item->article_web_url = $item->article->web_url();
            $item->article_thumb = Systems::image_format($item->article_thumb);
            $item->share = $item->article->share();
            unset($item->article,$item->catalog_id);
            return $item;
        });

        return $this->success([
            'list' => $article_comments,
            'count' => ArticleComments::where('user_id',$user->id)->where('status',Systems::STATUS_YES)->count()
        ]);
    }

    /**
     * 文章收藏
     * @param Request $request
     * @return mixed
     */
    public function article_collect(Request $request){
        $user = $request->user();
        try{
            $user->collects()->toggle([$request->article_id]);
        }catch (\PDOException $e){
            if($e->getCode() == '23000'){
                return $this->error('文章ID不可用','-30004');
            }
        }
        return $this->message('操作成功');
    }

    /**
     * 获取我的收藏列表
     * @param Request $request
     * @return mixed
     */
    public function get_my_article_collects(Request $request){
        $user = $request->user();

        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $article_collects = $user->collects()->with('catalog')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $data = [];
        $article_collects->map(function($item) use(&$data){
            $data[] = [
                'id' => $item->id,
                'title' => $item->title,
                'thumb' => Systems::image_format($item->thumb),
                'catalog_title' => $item->catalog->title,
                'web_url' => $item->web_url(),
                'share' => $item->share()
            ];
            return $data;
        });

        unset($article_collects);

        return $this->success([
            'list' => $data
        ]);
    }

    /**
     * 删除我的收藏
     * @param Request $request
     * @return mixed
     */
    public function del_collect(Request $request){
        $user=$request->user();

        $user->collects()->detach(explode(',',$request->article_ids));

        return $this->message('操作成功');
    }


}
