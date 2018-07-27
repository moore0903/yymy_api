<?php

use Illuminate\Database\Seeder;

class CmsUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\AppCmsUser::create([
            'username' => 'root',
            'password' => bcrypt('root'),
            'phone' => '13800013800',
            'status' => 1
        ]);
    }
}
