<?php
declare(strict_types = 1);

namespace App\CityCoordinates;

class CityCoordinatesStruct{
    /** @var float */
    public $fLatitude;
    /** @var float */
    public $fLongitude;

    /**
     * CityCoordinatesStruct constructor.
     * @param float $fLatitude
     * @param float $fLongitude
     */
    public function __construct(float $fLatitude, float $fLongitude)
    {
        $this->fLatitude = $fLatitude;
        $this->fLongitude = $fLongitude;
    }
}
