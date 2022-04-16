<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\CustomValidators;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->validator->resolver(function($translator, $data, $rules, $messages)
        {
            return new CustomValidators($translator, $data, $rules, $messages);
        });
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
