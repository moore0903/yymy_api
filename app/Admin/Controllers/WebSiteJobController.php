<?php

namespace App\Admin\Controllers;

use App\Models\WebSiteJob;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class WebSiteJobController extends Controller
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

            $content->header('招聘管理');

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

            $content->header('招聘管理');

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

            $content->header('招聘管理');

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
        return Admin::grid(WebSiteJob::class, function (Grid $grid) {
            $grid->model()->orderBy('sort')->orderByDesc('created_at');

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ];
            $grid->id('ID')->sortable();
            $grid->position('职位')->editable();
            $grid->salary('薪资')->editable();
            $grid->experience('经验')->editable();
            $grid->address('地点')->editable();
            $grid->tag('标签')->editable();

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
        return Admin::form(WebSiteJob::class, function (Form $form) {
            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->text('position','职位');
            $form->text('salary','薪资');
            $form->text('experience','经验');
            $form->text('num','人数');
            $form->text('address','地点');
            $form->text('tag','标签')->help('多个标签以,号隔开');
            $form->textarea('duty','职责')->rows(10);
            $form->textarea('need','需求')->rows(10);
            $form->switch('status', '是否显示')->states($states);
            $form->number('sort', '排序号');
        });
    }
}
