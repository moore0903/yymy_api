<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Shop;
use App\Models\Systems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends ApiController
{
    /**
     * 获取门店列表
     * @param Request $request
     * @return mixed
     */
    public function get_shop_list(Request $request){
        $page = $request->page > 1 ? $request->page : 1;
        $pagesize = isset($request->pagesize)?$request->pagesize:10;
        $offset = ($page-1)*$pagesize;

        $shops = Shop::where('status',Systems::STATUS_YES)
            ->orderBy('sort')->orderByDesc('created_at')->offset($offset)->limit($pagesize)->get();

        $shops = $shops->map(function($item){
            $item->detail_web_url = $item->detail_web_url();
            $item->share = $item->share();
            $item->image = Systems::image_format($item->image);
            $item->inner_image = Systems::image_format($item->inner_image);
            unset($item->province_code,$item->city_code,$item->town_code,$item->status,$item->sort,$item->deleted_at,$item->created_at,$item->updated_at,$item->detail);
            return $item;
        });

        return $this->success([
            'list' => $shops
        ]);
    }

    /**
     * 获取门店详情
     * @param Request $request
     * @return mixed
     */
    public function get_shop_info(Request $request){
        $shop = Shop::where('status',Systems::STATUS_YES)->find($request->id);

        if(empty($shop)){
            return $this->error('门店不可用！','-20002');
        }
        $shop->detail_web_url = $shop->detail_web_url();
        $shop->share = $shop->share();

        $shop->image = Systems::image_format($shop->image);
        $shop->inner_image = Systems::image_format($shop->inner_image);
        unset($shop->province_code,$shop->city_code,$shop->town_code,$shop->status,$shop->sort,$shop->deleted_at,$shop->created_at,$shop->updated_at);

        return $this->success([
            'info' => $shop
        ]);
    }
}
