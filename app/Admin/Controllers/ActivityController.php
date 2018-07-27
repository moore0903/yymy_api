<?php

namespace App\Admin\Controllers;

use App\Models\Activity;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ActivityController extends Controller
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

            $content->header('活动管理');

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

            $content->header('活动管理');

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

            $content->header('活动管理');

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
        return Admin::grid(Activity::class, function (Grid $grid) {

            $grid->model()->orderBy('sort')->orderByDesc('created_at');

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ];


            $grid->id('ID')->sortable();

            $grid->title('活动主题')->editable();
            $grid->image('封面图片')->image('',100,100);
            $grid->column('activity_time','活动时间')->display(function () {
                return $this->online_time . '到' . $this->offline_time;
            });

            $grid->address('活动地点')->editable();
            $grid->index_rec_status('是否首页推荐')->sortable()->switch($states);
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
        return Admin::form(Activity::class, function (Form $form) {

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->text('title','标题');
            $form->image('image','缩略图')->uniqueName();
            $form->datetime('online_time', '上线时间')->format('YYYY-MM-DD HH:mm:ss');
            $form->datetime('offline_time', '下线时间')->format('YYYY-MM-DD HH:mm:ss');
            $form->text('address','活动地点');
            $form->ueditor('content', '内容');
            $form->textarea('prize','奖品');
            $form->switch('index_rec_status', '是否首页推荐')->states($states);
            $form->switch('status', '是否显示')->states($states);
            $form->number('sort', '排序号');

            $form->saved(function (Form $form) {
                $file_path = public_path('page-cache/web/activity_info/'.$form->model()->id.'.html');
                @unlink($file_path);
            });
        });
    }
}
