<?php
declare(strict_types=1);

namespace App\CityCoordinates;

interface CityCoordinatesProviderInterface{
    /**
     * @param string $strText
     * @return CitySuggestionStruct[]
     * @throws \Exception
     */
    public function getCitySuggestions(string $strText): array;

    /**
     * @param string $strSuggestionId
     * @return CityCoordinatesStruct
     */
    public function getCoordinates(string $strSuggestionId): CityCoordinatesStruct;
}
