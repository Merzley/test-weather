<?php
declare (strict_types=1);

namespace App\Weather;

interface WeatherProviderInterface{
    public const UNITS_METRIC = 'metric';
    public const UNITS_IMPERIAL = 'imperial';

    public function getWeatherByCoordinates(float $fLatitude, float $fLongitude, string $strUnits, string $strLocale): WeatherStruct;
    public function isValidUnits(string $strUnits): bool;
}
