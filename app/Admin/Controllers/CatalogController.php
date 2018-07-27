<?php

namespace App\Admin\Controllers;

use App\Models\Catalogs;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CatalogController extends Controller
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

            $content->header('栏目管理');

            $content->body(Catalogs::tree(function($tree){
                $tree->branch(function ($branch) {
                    $src = $branch['thumb'] ;
                    if (!preg_match('/(http:\/\/)|(https:\/\/)/i', $src)) {
                        $src = env('QINIU_URL').'/'.$src;
                    }
                    $logo = "<img src='$src' style='max-width:30px;max-height:30px' class='img'/>";

                    return "{$branch['id']} - {$branch['title']} $logo";
                });
            }));
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

            $content->header('栏目管理');

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

            $content->header('栏目管理');

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
        return Admin::grid(Catalogs::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->column('username', '用户名');

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Catalogs::class, function (Form $form) {

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->select('parent_id','上级栏目')->options(Catalogs::selectOptions());
            $form->text('title','标题');
            $form->text('color','颜色');
            $form->image('thumb','缩略图')->uniqueName();
            $form->switch('status', '是否显示')->states($states);
            $form->number('sort', '排序号');
        });
    }
}
