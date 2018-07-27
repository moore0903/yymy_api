<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i=0;$i<50;$i++){
            \App\User::create([
                'name'=>str_random(8),
                'email'=>str_random(6).'@'.str_random(6),
                'password'=>bcrypt(str_random(5)),
                'baby_sex'=>rand(1,2),
                'register_type'=>rand(1,2),
                'user_name'=>str_random(),
                'avatar'=>'http://img.zcool.cn/community/010a1b554c01d1000001bf72a68b37.jpg@1280w_1l_2o_100sh.webp',
                'baby_birthday'=>date('Ymd',time()),
                'third_name'=>str_random(8),
                'third_avatar'=>'http://img.zcool.cn/community/0163f2554c01d1000001bf72314ab2.jpg@1280w_1l_2o_100sh.webp',
            ]);
        }
    }
}
