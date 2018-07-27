<?php

use Illuminate\Database\Seeder;

class CooperationSeeder extends Seeder
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
            \App\Models\Cooperation::insert([
                'user_id'=>rand(1,20),
                'name'=>str_random(15),
                'phone'=>str_random(11),
                'remark'=>str_random(15),
                'created_at'=>date('Y-m-d H:i:s',time()-rand(86400,259200))
            ]);
        }
    }
}
