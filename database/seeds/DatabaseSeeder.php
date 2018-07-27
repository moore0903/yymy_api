<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ActivityTableSeeder::class);
        $this->call(AdsSeeder::class);
        $this->call(ArticlesTableSeeder::class);
        $this->call(CatalogsTableSeeder::class);
        $this->call(CmsUserTableSeeder::class);
        $this->call(CollectSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(CooperationSeeder::class);
        $this->call(FeedbackSeeder::class);
        $this->call(ShopTableSeeder::class);
        $this->call(UserTableSeeder::class);
    }
}
