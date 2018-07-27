<?php

use Illuminate\Database\Seeder;

class CatalogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ["体育","语文","语文","电影","动漫","语文","世界","语文","历史","地理","世界","艺术","体育","历史","健康","电视","理化","天文","语文","饮食","日常","演艺","常识","设计","音乐","历史","时尚","军事","音乐","电影","历史","历史","电视","军事","历史","常识","文学","历史","地理","文学","历史","外语","语文","历史","常识","电视","天文","文化","体育","地理","外语","电影","经济","文学","文化","历史","语文","语文","历史","音乐","数学","语文","健康","动漫","地理","常识","常识","理化","文化","历史","文学","天文","地理","地理","文学","常识","饮食","语文","世界","地理","历史","语文","理化","历史","常识","常识","生物","语文","语文","生物","数学","历史","动漫","艺术","历史","历史","天文","语文","常识","音乐"];


        for ($i = 1; $i < 7; $i++) {
            \App\Models\Catalogs::create([
                'parent_id' => 0,
                'title' => $data[array_rand($data)],
                'thumb' => 'https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=4c81bd29a1773912c4268267c022e125/cf1b9d16fdfaaf51495c7a2b845494eef11f7ae3.jpg',
                'url' => 'https://www.whzhuoban.com',
                'status' => 1
            ]);
        }
    }
}
