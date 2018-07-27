<?php

namespace App\Admin\Controllers;

use App\Models\Shop;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
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

            $content->header('门店管理');

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

            $content->header('门店管理');

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

            $content->header('门店管理');

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
        return Admin::grid(Shop::class, function (Grid $grid) {

            $grid->model()->orderBy('sort')->orderByDesc('created_at');

            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
            ];

            $grid->id('ID')->sortable();

            $grid->name('店铺名称')->editable();
            $grid->image('封面图')->image('',100,100);
            $grid->address('店铺地址')->editable();
            $grid->average_price('人均消费')->editable()->display(function($price){
                return "$price 元/人";
            });
            $grid->phone('店铺电话')->editable();

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
        return Admin::form(Shop::class, function (Form $form) {
            $states = [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ];

            $form->text('name','店铺名称');
            $form->image('image','封面图')->uniqueName();
            $form->image('inner_image','内页图')->uniqueName();
            $form->select('province_code','省')->options(function(){
                $provinces = DB::table('t_address_province')->select('code','name')->get();
                foreach ($provinces as $province){
                    $data[$province->code] = $province->name;
                }
                return $data;
            })->load('city_code', '/admin/api/get_city');
            $form->select('city_code','市')->options(function($code){
                $citys = DB::table('t_address_city')->where('code',$code)->first();
                if(!empty($citys)){
                    $result = DB::table('t_address_city')->select('code','name')->where('provinceCode',$citys->provinceCode)->get();
                    foreach ($result as $item){
                        $data[$item->code] = $item->name;
                    }
                    return $data;
                }
            })->load('town_code', '/admin/api/get_town');
            $form->select('town_code','区')->options(function($code){
                $towns = DB::table('t_address_town')->where('code',$code)->first();
                if(!empty($towns)){
                    $result = DB::table('t_address_town')->select('code','name')->where('cityCode',$towns->cityCode)->get();
                    foreach ($result as $item){
                        $data[$item->code] = $item->name;
                    }
                    return $data;
                }
            });
            $form->text('address','地址');
            $form->text('lon','经度');
            $form->text('lat','纬度');
            $form->text('average_price','人均消费')->help('每人/元');
            $form->text('phone', '电话');
            $form->text('notice_age','适玩年龄');
            $form->text('notice_time','营业时间');
            $form->textarea('notice_rule','注意事项');
            $form->ueditor('detail', '内容');
            $form->switch('status', '是否显示')->states($states);
            $form->number('sort', '排序号');

            $form->saving(function (Form $form) {
                $form->model()->province = DB::table('t_address_province')->where('code',$form->province_code)->value('name');
                $form->model()->city = DB::table('t_address_city')->where('code',$form->city_code)->value('name');
                $form->model()->town = DB::table('t_address_town')->where('code',$form->town_code)->value('name');
            });

            $form->saved(function (Form $form) {
                $file_path = public_path('page-cache/web/shop_detail/'.$form->model()->id.'.html');
                @unlink($file_path);
            });
        });
    }

    /**
     * ajax 获取市
     * @param Request $request
     * @return mixed
     */
    public function get_city(Request $request){
        $provinceId = $request->q;
        return DB::table('t_address_city')->where('provinceCode',$provinceId)->select(DB::raw('code as id'),DB::raw('name as text'))->get();
    }

    /**
     * ajax 获取区
     * @param Request $request
     * @return mixed
     */
    public function get_town(Request $request){
        $townId = $request->q;
        return DB::table('t_address_town')->where('cityCode',$townId)->select(DB::raw('code as id'),DB::raw('name as text'))->get();
    }
}
