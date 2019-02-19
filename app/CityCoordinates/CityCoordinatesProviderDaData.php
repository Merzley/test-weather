<?php
declare(strict_types=1);

namespace App\CityCoordinates;

use Illuminate\Cache\CacheManager;

class CityCoordinatesProviderDaData implements CityCoordinatesProviderInterface{
    private const DEFAULT_CACHE_TIME = 1000;
    private const SUGGESTIONS_ENTRY_POINT = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
    private const COORDINATES_ENTRY_POINT = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/address';
    private const SUGGESTIONS_CACHE_ID_PREFIX = 'city_suggestions.dadata.';
    private const COORDINATES_CACHE_ID_PREFIX = 'city_coordinates.dadata.';

    /** @var string */
    private $strAuthToken;

    /** @var integer */
    private $nCacheTime;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * CityCoordinatesProviderDaData constructor.
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        $this->strAuthToken = config('services.dadata.auth_token');
        $this->nCacheTime = config('services.dadata.cache_time', self::DEFAULT_CACHE_TIME);
    }

    private function makeSuggestionsCacheId(string $strText): string{
        return self::SUGGESTIONS_CACHE_ID_PREFIX.$strText;
    }

    private function makeCoordinatesCacheId(string $strSuggestionId): string{
        return self::COORDINATES_CACHE_ID_PREFIX.$strSuggestionId;
    }

    /**
     * @param string $strEntryPoint
     * @param array $arRequestParams
     * @return string
     * @throws \Exception
     */
    private function commonDadataRequest(string $strEntryPoint, array $arRequestParams): string
    {
        $strRequestPayload = json_encode($arRequestParams);

        $curl = curl_init($strEntryPoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $strRequestPayload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token '.$this->strAuthToken
        ]);

        $strJsonResult = curl_exec($curl);

        if (($responseCode = (curl_getinfo($curl, CURLINFO_RESPONSE_CODE)) !== 200))
            throw new \Exception("The external service has returned not successful response code ($responseCode). $strJsonResult");

        return $strJsonResult;
    }

    /**
     * @param string $text
     * @return string
     * @throws \Exception
     */
    private function requestSuggestions(string $text): string{
        $arRequestParams = [
            'query' => $text,
            'from_bound' => [
                'value' => 'city',
            ],
            'to_bound' => [
                'value' => 'settlement'
            ],
        ];

        return $this->commonDadataRequest(self::SUGGESTIONS_ENTRY_POINT, $arRequestParams);
    }

    /**
     * @param string $strSuggestionId
     * @return string
     * @throws \Exception
     */
    private function requestCoordinates(string $strSuggestionId): string
    {
        $arRequestParams = [
            'query' => $strSuggestionId
        ];

        return $this->commonDadataRequest(self::COORDINATES_ENTRY_POINT, $arRequestParams);
    }

    private function validateSuggestion(\stdClass $stdSuggestion): bool
    {
        return (
            (property_exists($stdSuggestion, 'data')) &&
            (property_exists($stdSuggestion, 'value')) &&
            (property_exists($stdSuggestion->data, 'settlement_fias_id')) &&
            (property_exists($stdSuggestion->data, 'city_fias_id')) &&
            (property_exists($stdSuggestion->data, 'city')) &&
            (property_exists($stdSuggestion->data, 'settlement'))
        );
    }

    private function validateCoordinates(\stdClass $stdCoordinates): bool
    {
        return (
            (property_exists($stdCoordinates, 'suggestions')) &&
            (is_array($stdCoordinates->suggestions)) &&
            (isset($stdCoordinates->suggestions[0])) &&
            (property_exists($stdCoordinates->suggestions[0], 'data')) &&
            (property_exists($stdCoordinates->suggestions[0]->data, 'geo_lat')) &&
            (property_exists($stdCoordinates->suggestions[0]->data, 'geo_lon')) &&
            (is_numeric($stdCoordinates->suggestions[0]->data->geo_lat)) &&
            (is_numeric($stdCoordinates->suggestions[0]->data->geo_lon))
        );
    }

    /**
     * @param string $strText
     * @return CitySuggestionStruct[]
     * @throws \Exception
     */
    public function getCitySuggestions(string $strText): array
    {
        $arSuggestions = $this->cacheManager->remember(
            $this->makeSuggestionsCacheId($strText),
            $this->nCacheTime,
            function () use ($strText){
                $strJson = $this->requestSuggestions($strText);
                if (($arSuggestions = json_decode($strJson)) === null)
                    throw new \Exception("Can't decode external server response. $strJson");
                return $arSuggestions;
            }
        );

        $arResult = [];
        if (!property_exists($arSuggestions, 'suggestions'))
            throw new \Exception('Unknown external server response structure. '.json_encode($arSuggestions));

        foreach ($arSuggestions->suggestions as $stdSuggestion){
            if (!$this->validateSuggestion($stdSuggestion))
                throw new \Exception('Unknown external server response structure. '.json_encode($arSuggestions));

            if ($stdSuggestion->data->settlement_fias_id)
                $id = $stdSuggestion->data->settlement_fias_id;
            else if ($stdSuggestion->data->city_fias_id)
                $id = $stdSuggestion->data->city_fias_id;
            else
                continue;

            if ($stdSuggestion->data->city)
                $cityName = $stdSuggestion->data->city;
            else if ($stdSuggestion->data->settlement)
                $cityName = $stdSuggestion->data->settlement;
            else
                $cityName = $stdSuggestion->value;

            $arResult[] = new CitySuggestionStruct(
                $stdSuggestion->value,
                $cityName,
                $id
            );
        }

        return $arResult;
    }

    /**
     * @param string $strSuggestionId
     * @return CityCoordinatesStruct
     * @throws \Exception
     */
    public function getCoordinates(string $strSuggestionId): CityCoordinatesStruct
    {
        $stdCoordinates = $this->cacheManager->remember(
            $this->makeCoordinatesCacheId($strSuggestionId),
            $this->nCacheTime,
            function () use ($strSuggestionId){
                $strJson = $this->requestCoordinates($strSuggestionId);
                if (($stdCoordinates = json_decode($strJson)) === null)
                    throw new \Exception("Can't decode external server response. $strJson");
                return $stdCoordinates;
            }
        );

        if (!$this->validateCoordinates($stdCoordinates))
            throw new \Exception('Unknown external server response structure. '.json_encode($stdCoordinates));

        return new CityCoordinatesStruct(
            floatval($stdCoordinates->suggestions[0]->data->geo_lat),
            floatval($stdCoordinates->suggestions[0]->data->geo_lon)
        );
    }
}
