<?php

namespace App\Admin\Controllers;

use App\Models\AliyunPush;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AliyunPushController extends Controller
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

            $content->header('推送管理');
            $content->description('推送管理');

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

            $content->header('推送管理');
            $content->description('推送管理');

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

            $content->header('推送管理');
            $content->description('推送管理');

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
        return Admin::grid(AliyunPush::class, function (Grid $grid) {

            $grid->disableActions();

            $grid->model()->orderByDesc('push_time')->orderByDesc('created_at');

            $grid->id('ID')->sortable();

            $grid->push_title('推送标题');
            $grid->push_body('推送内容');
            $grid->push_time('推送时间');
            $grid->push_device_type('推送设备类型')->display(function($push_device_type){
                switch ($push_device_type){
                    case 1:
                        return '所有设备';
                        break;
                    case 2:
                        return 'IOS';
                        break;
                    case 3:
                        return 'ANDROID';
                        break;
                }
            });

            $grid->push_open_type('推送后动作')->display(function($push_open_type){
                switch ($push_open_type){
                    case 1:
                        return '打开应用';
                        break;
                    case 2:
                        return '版本更新';
                        break;
                    case 3:
                        return '文章分类';
                        break;
                    case 4:
                        return '活动分类';
                        break;
                }
            });
            $grid->push_open_activity('推送后打开的页面ID');
            $grid->push_status('发送状态')->display(function($push_status){
                switch ($push_status){
                    case 1:
                        return '未发送';
                        break;
                    case 2:
                        return '已发送';
                        break;
                    case 3:
                        return '已取消';
                        break;
                    case 4:
                        return '失败';
                        break;
                    default:
                        return '其它';
                        break;
                }
            });


            $grid->created_at('添加时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(AliyunPush::class, function (Form $form) {
            $form->text('push_title', '推送标题')->attribute('maxlength','16');
            $form->textarea('push_body', '推送内容')->attribute('maxlength','128');
            $form->datetime('push_time','推送时间')->help('立即发送则将文本框为空');
            $form->select('push_device_type', '推送设备类型')->options([
                1 => 'all',
                2 => 'ios',
                3 => 'android',
            ])->default(1);
            $form->select('push_open_type', '推送后动作')->options([
                1 => '打开应用',
                2 => '版本更新',
                3 => '文章分类',
                4 => '活动分类'
            ])->default(1);
            $form->text('push_open_activity', '推送后打开的页面ID')->help('打开应用可不填写此字段');
        });
    }
}
