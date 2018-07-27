<?php

use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i=0;$i<99;$i++){
            \App\Models\Feedback::insert([
                'user_id'=>rand(1,20),
                'content'=>str_random(15),
                'created_at'=>date('Y-m-d H:i:s',time()-rand(86400,259200))
            ]);
        }
    }
}
