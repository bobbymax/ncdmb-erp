<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            config(['site' => Setting::all(['key', 'value'])->keyBy('key')->transform(function ($setting) {
                    return $setting->value;
                })->toArray()
            ]);
        }

//        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
