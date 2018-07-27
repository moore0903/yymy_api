<?php

namespace App\Providers;

use App\Http\Models\OneCard;
use App\Observers\OneCardObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies=[
      'App\Model'=>'App\Policies\ModelPolicy',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->registerPolicies();
        Passport::routes();

//        Relation::morphMap([
//            'article' => 'App\Models\Articles',
//            'activity' => 'App\Models\Activity',
//            'shop' => 'App\Models\Shop',
//            'other' => ''
//        ]);

//        Passport::tokensExpireIn();
//        Passport::refreshTokensExpireIn();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
