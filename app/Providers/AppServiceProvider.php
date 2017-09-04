<?php

namespace App\Providers;
use Auth0\Login\Contract\Auth0UserRepository as Auth0UserRepositoryContract; 
use Auth0\Login\Repository\Auth0UserRepository as Auth0UserRepository;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() 
    { 
        $this->app->bind( Auth0UserRepositoryContract::class, Auth0UserRepository::class ); 
    }
}