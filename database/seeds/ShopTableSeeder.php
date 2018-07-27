<?php

use Illuminate\Database\Seeder;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //
        for($i=0;$i<15;$i++){
            \App\Models\Shop::create([
                'name'=>str_random(10),
                'image'=>'http://pic27.photophoto.cn/20130622/0036036876233936_b.jpg',
                'inner_image'=>'http://pic27.photophoto.cn/20130622/0036036876233936_b.jpg,http://img05.tooopen.com/images/20140327/sy_57661992573.jpg',
                'province'=>str_random(3),
                'city'=>str_random(4),
                'town'=>str_random(5),
                'address'=>str_random(15),
                'average_price'=>rand(1,99),
                'phone'=>str_random(11),
                'detail'=>'","神农氏尝百草，神农氏是谁？", "被誉为「旋律大师」的是？","在数学公式中，我们一般用哪个英文字母标注图形的高？","我国第一部「语录体」著作是？","中国历史上哪个朝代「厚葬」成风？","1984年开播的《黑猫警长》是由以下哪个制片厂发行的？","「香格里拉」是一部美国小说中世外桃源的名字，它在哪里？","自然现象「海市蜃楼」在哪个季节最容易发生？","2017年「闰月」是哪个月？","味精的主要成分是？",  "长篇小说《藏地密码》的作者是？","「水能载舟，亦能覆舟」是谁所说？","「西楚霸王」指的是哪个历史人物？","我国第一次载人航天活动中，航天员杨利伟乘坐的的航天飞船是？","下列哪个国家是从马来西亚分离出来的？',
                'status'=>rand(0,1),
                'sort'=>rand(1,9),
                'notice_age'=>str_random(5),
                'notice_time'=>str_random(5),
                'notice_rule'=>str_random(5),
            ]);
        }
    }
}
