<?php

use Illuminate\Database\Seeder;

class CollectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i=0;$i<100;$i++){
            \DB::table('user_collect_article')->insert([
                'user_id'=>rand(1,50),
                'article_id'=>rand(1,99),
            ]);
        }
    }
}
