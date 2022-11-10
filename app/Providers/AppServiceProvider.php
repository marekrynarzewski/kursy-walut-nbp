<?php

namespace App\Providers;

use App\Abstraction\ModelSaverInterface;
use App\Services\DefaultModelSaver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ModelSaverInterface::class, DefaultModelSaver::class);
    }
}
