<?php

namespace App\Admin\Controllers;

use App\Models\Ads;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AdsController extends Controller
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

            $content->header('广告位管理');

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

            $content->header('广告位管理');

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

            $content->header('广告位管理');

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
        return Admin::grid(Ads::class, function (Grid $grid) {

            $grid->model()->orderBy('sort')->orderByDesc('created_at');

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ];

            $grid->id('ID')->sortable();

            $grid->column('url','URL');

            $grid->ad_table_type('跳转类型')->select([
                'article' => '文章',
                'activity' => '活动',
                'shop' => '门店',
                'other' => '外链'
            ]);

            $grid->ad_table_id('跳转ID')->editable();

            $grid->thumb('缩略图')->image('',100,100);


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
        return Admin::form(Ads::class, function (Form $form) {

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->text('url','URL')->help('如果外链则必填');
            $form->image('thumb','缩略图')->uniqueName();

            $form->select('ad_table_type','跳转类型')->options([
                'article' => '文章',
                'activity' => '活动',
                'shop' => '门店',
                'other' => '外链'
            ]);
            $form->text('ad_table_id','跳转ID')->help('如果外链则填0');

            $form->switch('status', '是否显示')->states($states);
            $form->number('sort', '排序号');
        });
    }
}
