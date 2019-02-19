<?php
declare(strict_types=1);

namespace App\Weather;

use Illuminate\Cache\CacheManager;

class WeatherProviderOpenWeatherMap implements WeatherProviderInterface
{
    private const WEATHER_ENTRY_POINT = 'https://api.openweathermap.org/data/2.5/weather';
    private const DEFAULT_CACHE_TIME = 120;
    private const CACHE_ID_PREFIX = 'weather_';

    /** @var string */
    private $strAppId;

    /** @var integer */
    private $nCacheTime;

    /** @var CacheManager */
    private $cacheManager;

    /**
     * WeatherProviderOpenWeatherMap constructor.
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        $this->strAppId = config('services.openweathermap.app_id');
        $this->nCacheTime = config('services.openweathermap.cache_time', self::DEFAULT_CACHE_TIME);
    }

    private function makeCacheId(float $fLatitude, float $fLongitude, string $strUnits, string $strLocale): string{
        return self::CACHE_ID_PREFIX.$fLatitude.'_'.$fLongitude.'_'.$strUnits.'_'.$strLocale;
    }

    /**
     * @param float $fLatitude
     * @param float $fLongitude
     * @param string $strUnits
     * @param string $strLocale
     * @return string
     * @throws \Exception
     */
    private function requestWeatherJson(float $fLatitude, float $fLongitude, string $strUnits, string $strLocale): string
    {
        $arRequestParams = [
            'appid' => $this->strAppId,
            'lat' => $fLatitude,
            'lon' => $fLongitude,
            'lang' => $strLocale,
            'units' => $strUnits
        ];

        $strUrl = self::WEATHER_ENTRY_POINT.'?'.http_build_query($arRequestParams);

        $curl = curl_init($strUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $strJsonResult = curl_exec($curl);

        if (($responseCode = (curl_getinfo($curl, CURLINFO_RESPONSE_CODE)) !== 200))
            throw new \Exception("The external service has returned not successful response code ($responseCode). $strJsonResult");

        return $strJsonResult;
    }

    private function validateWeather(\stdClass $stdWeather): bool
    {
        return (
            (property_exists($stdWeather, 'main')) &&
            (property_exists($stdWeather->main, 'temp')) &&
            (property_exists($stdWeather->main, 'pressure')) &&
            (property_exists($stdWeather->main, 'humidity')) &&
            (property_exists($stdWeather, 'wind')) &&
            (property_exists($stdWeather->wind, 'speed')) &&
            (property_exists($stdWeather->wind, 'deg')) &&
            (property_exists($stdWeather, 'weather')) &&
            (is_array($stdWeather->weather)) &&
            (isset($stdWeather->weather[0])) &&
            (property_exists($stdWeather->weather[0], 'description'))
        );
    }

    /**
     * @param float $fLatitude
     * @param float $fLongitude
     * @param string $strUnits
     * @param string $strLocale
     * @return WeatherStruct
     * @throws \Exception
     */
    public function getWeatherByCoordinates(float $fLatitude, float $fLongitude, string $strUnits, string $strLocale): WeatherStruct
    {
        if (!$this->isValidUnits($strUnits))
            throw new \Exception('Unknown "units" parameter value');


        $stdWeather = $this->cacheManager->remember(
            $this->makeCacheId($fLatitude, $fLongitude, $strUnits, $strLocale),
            $this->nCacheTime,
            function () use ($fLatitude, $fLongitude, $strUnits, $strLocale){
                $strJson = $this->requestWeatherJson($fLatitude, $fLongitude, $strUnits, $strLocale);
                if (($stdWeather = json_decode($strJson)) === null)
                    throw new \Exception("Can't decode external server response. $strJson");
                return $stdWeather;
            }
        );

        if (!$this->validateWeather($stdWeather))
            throw new \Exception('Unknown external server response structure. '.json_encode($stdWeather));

        return new WeatherStruct(
            floatval($stdWeather->main->temp),
            $stdWeather->weather[0]->description,
            floatval($stdWeather->wind->speed),
            intval($stdWeather->wind->deg),
            intval($stdWeather->main->pressure),
            intval($stdWeather->main->humidity)
        );
    }

    public function isValidUnits(string $strUnits): bool
    {
        return (
            ($strUnits === self::UNITS_METRIC) ||
            ($strUnits === self::UNITS_IMPERIAL)
        );
    }
}
