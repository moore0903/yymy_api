<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalogs extends Model
{
    use ModelTree,AdminBuilder,SoftDeletes;

    protected $table = 'catalogs';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setOrderColumn('sort');
    }

    public static $colors_select = [
        '黄色' => '#FFF68F',
        '深色' => '#5E5E5E',
        '原谅绿' => '#4EEE94',
        '天蓝色' => '#00FFFF',
        '深红色' => '#8B2500',
        '红色' => '#CD0000',
    ];


    /**
     * 取出所有分类
     * @param int $parent_id
     * @param int $level
     * @return array
     */
//    public static function catalog($parent_id=0,$level=0){
//        $data=Catalogs::where('parent_id',$parent_id)->get();
//        static $catalog=[];
//        foreach($data->toArray() as $v){
//            if($v['parent_id']==$parent_id){
//                $v['level']=$level;
//                $catalog[]=$v;
//                $sub=Catalogs::catalog($v['parent_id'],$level+1);
//
//                if(isset($sub)){
//                    foreach ($sub as $k){
//                        array_push($catalog,$k);
//                    }
//                }
//                dump($catalog);die;
//            }
//        }
//        return $catalog;
//    }


    /**
     * 取出所有分类
     * @param int $parent_id
     * @param int $series
     * @return array|null
     */
    public static function catalog($parent_id = 0,$series = 0){
        $result = null;
        $catalogs = Catalogs::where('parent_id',$parent_id)->get();
        foreach($catalogs as $catalog){
            $result[] = $catalog;
            $sub = Catalogs::catalog($catalog->id,$series+1);
            if(isset($sub)){
                foreach ($sub as $item){
                    array_push($result,$item);
                }
            }
        }
        if(!empty($result)){
            return $result;
        }
    }


//    /**
//     * 递归替代解决方案 - 返回下级栏目
//     * @param $catalog_id
//     * @return array|null
//     * 技术部-闫凯 10:13:48 http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
//     */
//    public static function get_sub_id($catalog_id,$level = 3){
//        $select = ['t1.id as lev1'];
//        $from = ' FROM `catalogs` AS t1';
//        for ($i = 0; $i < $level; $i++){
//            $select[] = 't'.($i+2).'.id as lev'.($i+2);
//            $from .= ' LEFT JOIN `catalogs` AS t'.($i+2).' ON t'.($i+1).'.id = t'.($i+2).'.parent_id';
//        }
//        $sql = 'SELECT '.implode(',',$select).$from.' WHERE t1.id = ?';
//        $catalogs = DB::select($sql,[$catalog_id]);
//        $catalogs = collect($catalogs);
//        $result = [];
//        for ($i = 0; $i < $level; $i++){
//            $result = array_merge($result,array_unique(array_filter($catalogs->pluck('lev'.($i+1))->all())));
//        }
//        return $result;
//
//    }


    /**
     * 状态转中文
     * @param $status
     * @return string
     */
    public static function statusToChinese($status){
        switch($status){
            case Articles::STATUS_AVAILABLE:
                return '可用';
                break;
            case Articles::STATUS_UNAVAILABLE:
                return '禁用';
                break;
            default:
                return '未定义';
                break;
        }
    }


    const STATUS_AVAILABLE =1;  //可用
    const STATUS_UNAVAILABLE=0; //禁用
}
