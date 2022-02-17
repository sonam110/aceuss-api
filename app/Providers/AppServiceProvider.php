<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);
        $this->timezone();
    }
    private function timezone()
    {
        if (is_object(auth()->guard('api')->user())) {
            if (is_object(auth()->guard('api')->user()->country_id)) {
                $countryCode = auth()->guard('api')->user()->country_id->country_code;
                $timezone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countryCode);
                config(['app.timezone' => $timezone[0]]);
                date_default_timezone_set($timezone[0]);
                return $timezone[0];
            }
        }
    }
}
