<?php
declare (strict_types = 1);

namespace App\Http\Controllers\Api;

use App\CityCoordinates\CityCoordinatesProviderInterface;
use App\Exceptions\JsonHttpException;
use App\Weather\WeatherProviderInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WeatherController extends Controller
{
    public function getWeatherBySuggestionId(Request $request, CityCoordinatesProviderInterface $coordinatesProvider, WeatherProviderInterface $weatherProvider){
        if (
            (($strSuggestionId = $request->get('suggestion_id', null)) === null) ||
            (($strUnits = $request->get('units', null)) === null) ||
            (($strLocale = $request->get('locale', null)) === null)
        )
        {
            throw new JsonHttpException(422, 'Missing required parameter "suggestion_id" or "units" or "locale".');
        }

        if (!$weatherProvider->isValidUnits($strUnits))
            throw new JsonHttpException(422, 'Unknown "units" parameter value.');

        try {
            $objCoordinates = $coordinatesProvider->getCoordinates($strSuggestionId);
            $objWeather = $weatherProvider->getWeatherByCoordinates($objCoordinates->fLatitude, $objCoordinates->fLongitude, $strUnits, $strLocale);
        }
        catch (\Exception $e)
        {
            throw new JsonHttpException(520, $e->getMessage());
        }

        return response()->json($objWeather, 200, ['Content-Type' => 'application/json']);
    }
}
