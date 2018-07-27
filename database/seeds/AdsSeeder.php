<?php

use Illuminate\Database\Seeder;

class AdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = ['article', 'activity', 'shop', 'other'];

        $thumb_array = [
            'http://pic4.nipic.com/20091113/2847083_105626034638_2.jpg',
            'http://f2.topitme.com/2/6a/bc/113109954583dbc6a2o.jpg',
            'http://www.taopic.com/uploads/allimg/140320/235013-14032020515270.jpg',
            'http://a3.topitme.com/1/21/79/1128833621e7779211o.jpg',
            'http://fd.topitme.com/d/a8/1d/11315383988791da8do.jpg'
        ];

        //
        for ($i = 0; $i < 20; $i++) {
            \App\Models\Ads::insert([
                'url' => 'http://www.baidu.com',
                'ad_table_id' => rand(1, 99),
                'ad_table_type' => array_random($array),
                'thumb' => array_random($thumb_array),
                'sort' => rand(1, 99),
                'status' => rand(0, 1)
            ]);
        }

    }
}
