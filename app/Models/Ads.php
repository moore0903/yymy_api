<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $table='ads';

    protected $guarded=[];

    /**
     * 首页广告接口数据的格式化
     * @return array
     */
    public function get_result_data(){
        $data = [
            'id' => $this->type == 'other' ? '0' :$this->ad_table_id,
            'url' => $this->url,
            'thumb' => Systems::image_format($this->thumb),
            'type' => $this->ad_table_type,
        ];
        switch ($this->ad_table_type){
            case 'article':
                $table_result = Articles::find($this->ad_table_id);
                $data['url'] = $table_result->web_url();
                break;
            case 'activity':
                $table_result = Activity::find($this->ad_table_id);
                $data['url'] = $table_result->web_url();
                break;
            case 'shop':
                $table_result = Shop::find($this->ad_table_id);
                break;
            case 'other':
                $share = [
                    'share_url' => $this->url,
                    'share_title' => '【茁伴乐园】',
                    'share_desc' => '【茁伴乐园】',
                    'share_logo' => Systems::image_format($this->thumb),
                ];
                break;
        }
        $data['share'] = isset($table_result) ? $table_result->share() : $share;
        unset($table_result,$share);
        return $data;
    }
}
