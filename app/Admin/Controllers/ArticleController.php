<?php

namespace App\Admin\Controllers;

use App\Models\Articles;

use App\Models\Catalogs;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ArticleController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('文章管理');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('文章管理');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('文章管理');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Articles::class, function (Grid $grid) {

            $grid->model()->withCount('comments')->orderBy('sort')->orderByDesc('created_at');

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ];

            $grid->id('ID')->sortable();
            $grid->catalog_id('栏目')->select(Catalogs::selectOptions());
            $grid->title('标题')->editable();
            $grid->thumb('缩略图')->image('',100,100);
            $grid->column('评论条数')->display(function(){
                return $this->comments_count;
            });

            $grid->index_rec_status('首页推荐')->switch($states);

            $grid->sort('排序号')->sortable()->editable();
            $grid->status('是否可用')->sortable()->switch($states);

            $grid->created_at('创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Articles::class, function (Form $form) {

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->select('catalog_id','所属栏目')->options(Catalogs::selectOptions());
            $form->text('title','标题');
            $form->image('thumb','缩略图')->uniqueName();
            $form->ueditor('content', '内容');
            $form->switch('index_rec_status', '首页推荐')->states($states);
            $form->switch('status', '是否显示')->states($states);
            $form->number('sort', '排序号');


            $form->saved(function (Form $form) {
                $file_path = public_path('page-cache/web/article_info/'.$form->model()->id.'.html');
                @unlink($file_path);
                $file_path = public_path('page-cache/web/article_share/'.$form->model()->id.'.html');
                @unlink($file_path);
            });
        });
    }
}
