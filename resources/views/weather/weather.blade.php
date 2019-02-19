<?php
/** @var string strDefaultUnits */
/** @var string strCurrentLocale */
/** @var string jsonLocaleStrings */
?>

@extends('base')

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ mix('css/weather.css') }}">
@endsection
@section('js')
    @parent
    <script src="https://api-maps.yandex.ru/2.1/?apikey=388cf923-dd77-48a2-85d1-6d7165288d9c&lang=ru_RU" type="text/javascript"></script>
    <script src="{{ mix('js/weather.js') }}"></script>
@endsection

@section('body')
<script type="text/javascript">
    window.weather = {
        strDefaultUnits: '{{ $strDefaultUnits }}',
        strLocale: '{{ $strCurrentLocale }}',
        objLocaleStrings: JSON.parse('{!! $jsonLocaleStrings !!}')
    };
</script>

<div id="js-preloader" class="preloader">
    @include('weather.preloader')
</div>

<div class="weather">
    <div class="weather-top">
        <div id='js-select-city-block' class="weather-top-select-city hidden">
            @include('weather.select_city')
        </div>
        <div id='js-city-data-block' class="weather-top-selected-city">
            <div id='js-city-name' class="weather-top-cityname">
            </div>
            <div>
                <span id='js-city-select-button' class="weather-top-control">@lang('weather.change_city')</span>
                <span id='js-current-location-button' class="weather-top-control location">@lang('weather.current_location')</span>
            </div>
        </div>
        <div class="weather-top-units-switch">
            <div class="weather-top-unit
                        js-units-button
                        {{ ($strDefaultUnits === \App\Weather\WeatherProviderInterface::UNITS_METRIC) ? 'active-unit' : '' }}
                       "
                 data-units="{{ \App\Weather\WeatherProviderInterface::UNITS_METRIC }}"
            >
                C
            </div>
            <div class="weather-top-unit
                        js-units-button
                        {{ ($strDefaultUnits === \App\Weather\WeatherProviderInterface::UNITS_IMPERIAL) ? 'active-unit' : '' }}
                       "
                 data-units="{{ \App\Weather\WeatherProviderInterface::UNITS_IMPERIAL }}"
            >
                F
            </div>
        </div>
    </div>
    <div class="weather-center">
        <div class="weather-center-data">
            <div class="weather-center-data-temperature">
                <span id="js-weather-temperature"></span><sup>o</sup>
            </div>
        </div>
        <div id='js-weather-description' class="weather-center-description">
        </div>
    </div>
    <div class="weather-bottom">
        <div>
            <div class="weather-bottom-title">@lang('weather.wind')</div>
            <div class="weather-bottom-data">
                {{--TODO: напрвление ветра--}}
                <span id='js-weather-wind-speed' class="weather-bottom-data-bold"></span> <span id="js-speed-unit"></span>
            </div>
        </div>
        <div>
            <div class="weather-bottom-title">@lang('weather.pressure')</div>
            <div class="weather-bottom-data">
                <span id='js-weather-pressure' class="weather-bottom-data-bold"></span> @lang('weather.pressure_unit')
            </div>
        </div>
        <div>
            <div class="weather-bottom-title">@lang('weather.humidity')</div>
            <div class="weather-bottom-data">
                <span id='js-weather-humidity' class="weather-bottom-data-bold"></span>%
            </div>
        </div>
    </div>
</div>
@endsection
