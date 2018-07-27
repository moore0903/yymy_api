<?php

use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
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
            \App\Models\ArticleComments::insert([
                'user_id'=>rand(1,10),
                'article_id'=>rand(1,20),
                'comment_time'=>date('Y-m-d H:i:s',time()+rand(1,9999)),
                'content'=>str_random(18),
                'status'=>rand(0,1),
            ]);
        }
    }
}
