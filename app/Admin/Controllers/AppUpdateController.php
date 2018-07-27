<?php

namespace App\Admin\Controllers;

use App\Models\AppUpdateMessage;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AppUpdateController extends Controller
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

            $content->header('APP更新');

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

            $content->header('APP更新');

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

            $content->header('APP更新');

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
        return Admin::grid(AppUpdateMessage::class, function (Grid $grid) {
            $grid->model()->orderByDesc('created_at');

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ];

            $grid->id('ID')->sortable();

            $grid->column('设备')->display(function(){
                return AppUpdateMessage::$app_string[$this->app_id];
            });

            $grid->version_code('版本号');

            $grid->update_title('升级简要');

            $grid->column('下载地址')->display(function(){
                $download = $this->download_url;
                if(empty($download)){
                    $download = env('QINIU_URL').'/'.$this->upload_file;
                }
                return "<a href='".$download."' target='_blank'>".$download."</a>";
            });

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
        return Admin::form(AppUpdateMessage::class, function (Form $form) {
            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->select('app_id','设备')->options([
                '1' => 'android',
                '2' => 'ios'
            ]);
            $form->text('app_code','APP 包名');
            $form->text('app_name','应用名称');
            $form->text('version_code','版本号');
            $form->text('version_name','版本名');
            $form->text('download_url','更新地址')->help('android可不填写');
            $form->file('upload_file', '上传文件')->rules('mimes:zip')->help('IOS可不用上传文件');
            $form->text('update_title','升级简要');
            $form->textarea('update_message','升级说明')->rows(10);
            $form->switch('status', '是否可用')->states($states);


        });
    }
}
