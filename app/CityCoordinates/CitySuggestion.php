<?php
declare(strict_types=1);

namespace App\CityCoordinates;

class CitySuggestionStruct{
    /** @var string */
    public $strText;
    /** @var string $strCityName */
    public $strCityName;
    /** @var string */
    public $strSuggestionId;

    /**
     * CitySuggestion constructor.
     * @param string $strText
     * @param string $strCityName
     * @param string $strSuggestionId
     */
    public function __construct(string $strText, string $strCityName, string $strSuggestionId)
    {
        $this->strText = $strText;
        $this->strCityName = $strCityName;
        $this->strSuggestionId = $strSuggestionId;
    }
}
