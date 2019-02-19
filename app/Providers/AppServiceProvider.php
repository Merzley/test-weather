<?php

namespace App\Providers;

use App\CityCoordinates\CityCoordinatesProviderDaData;
use App\CityCoordinates\CityCoordinatesProviderInterface;
use App\Weather\WeatherProviderInterface;
use App\Weather\WeatherProviderOpenWeatherMap;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        WeatherProviderInterface::class => WeatherProviderOpenWeatherMap::class,
        CityCoordinatesProviderInterface::class => CityCoordinatesProviderDaData::class
    ];
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
        //
    }
}
