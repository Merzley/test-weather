<?php
declare(strict_types=1);

namespace App\Weather;

class WeatherStruct{
    /** @var float|null */
    public $fTemperature;
    /** @var string|null */
    public $strDescription;
    /** @var float|null */
    public $fWindSpeed;
    /** @var int|null */
    public $nWindDirection;
    /** @var int|null */
    public $nPressure;
    /** @var int|null */
    public $nHumidity;

    /**
     * WeatherStruct constructor.
     * @param float|null $fTemperature
     * @param string|null $strDescription
     * @param float|null $fWindSpeed
     * @param int|null $nWindDirection
     * @param int|null $nPressure
     * @param int|null $nHumidity
     */
    public function __construct(?float $fTemperature, ?string $strDescription, ?float $fWindSpeed, ?int $nWindDirection, ?int $nPressure, ?int $nHumidity)
    {
        $this->fTemperature = $fTemperature;
        $this->strDescription = $strDescription;
        $this->fWindSpeed = $fWindSpeed;
        $this->nWindDirection = $nWindDirection;
        $this->nPressure = $nPressure;
        $this->nHumidity = $nHumidity;
    }


}
