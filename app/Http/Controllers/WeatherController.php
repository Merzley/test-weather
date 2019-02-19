<?php

namespace App\Http\Controllers;

use App\Weather\WeatherProviderInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;

class WeatherController extends Controller
{
    private const DEFAULT_UNITS = WeatherProviderInterface::UNITS_METRIC;

    /**
     * @param Factory $viewFactory
     * @param Application $app
     * @param string $locale
     * @return \Illuminate\Contracts\View\View
     */
    public function showSelectLocationPage(Factory $viewFactory, Application $app, string $locale = null)
    {
        if ($locale != null)
            $app->setLocale($locale);

        return $viewFactory->make('weather.weather', [
            'strDefaultUnits' => self::DEFAULT_UNITS,
            'strCurrentLocale' => $app->getLocale(),
            'jsonLocaleStrings' => json_encode(__('weather_js'))
        ]);
    }
}
