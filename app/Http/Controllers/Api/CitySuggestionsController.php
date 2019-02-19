<?php
declare (strict_types = 1);

namespace App\Http\Controllers\Api;

use App\CityCoordinates\CityCoordinatesProviderInterface;
use App\Exceptions\JsonHttpException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CitySuggestionsController extends Controller
{
    public function getCitySuggestions(Request$request, CityCoordinatesProviderInterface $coordinatesProvider)
    {
        if (($strText = $request->get('text', null)) === null)
            throw new JsonHttpException(422, 'Missing required parameter "text".');

        try {
            $arSuggestions = $coordinatesProvider->getCitySuggestions($strText);
        }
        catch (\Exception $e){
            throw new JsonHttpException(520, $e->getMessage());
        }

        return response()->json($arSuggestions, 200, ['Content-Type' => 'application/json']);
    }
}
