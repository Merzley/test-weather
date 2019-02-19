import '../base';
import './preloader';
import './select_city'

const ApiRequests = new (require('./api_requests').ApiRequests)();
const registerSelectCityEvenListener = require('./select_city').registerSelectCityEvenListener;

const domPreloader = document.getElementById('js-preloader');

const domCityName = document.getElementById('js-city-name');

const domWeatherDescription = document.getElementById('js-weather-description');
const domWeatherTemperature = document.getElementById('js-weather-temperature');
const domWeatherWindSpeed = document.getElementById('js-weather-wind-speed');
const domWeatherPressure = document.getElementById('js-weather-pressure');
const domWeatherHumidity = document.getElementById('js-weather-humidity');

const domCurrentLocationButton = document.getElementById('js-current-location-button');
const domCitySelectButton = document.getElementById('js-city-select-button');

const domCitySelectBlock = document.getElementById('js-select-city-block');
const domCityDataBlock = document.getElementById('js-city-data-block');

const strCurrentLocale = window.weather.strLocale;
const arDomUnitsButtons = document.querySelectorAll('.js-units-button');
const domSpeedUnitString = document.getElementById('js-speed-unit');

const objLocaleStrings = window.weather.objLocaleStrings;
let strCurrentSelectedUnits = window.weather.strDefaultUnits;
let objCurrentSelectedSuggestion = null;

function fillWeatherByGeolocation(){
    setPreloaderVisible(true);

    let objFoundSuggestion;

    ApiRequests.getGeolocationAddress()
    .then((objSuggestion) => {
        objFoundSuggestion = objSuggestion;
        return ApiRequests.getWeather(objSuggestion.strSuggestionId, strCurrentSelectedUnits, strCurrentLocale);
    })
    .then((objWeatherData) => {
        fillWeather(objWeatherData, objFoundSuggestion);

        showCityData();
        setPreloaderVisible(false);
    });
}

function fillWeatherBySuggestion(objSuggestion){
    setPreloaderVisible(true);

    ApiRequests.getWeather(objSuggestion.strSuggestionId, strCurrentSelectedUnits, strCurrentLocale)
    .then((objWeatherData) => {
        fillWeather(objWeatherData, objSuggestion);

        showCityData();
        setPreloaderVisible(false);
    })
}

function fillWeather(objWeatherData, objSuggestion){
    domCityName.textContent = objSuggestion.strCityName;

    objCurrentSelectedSuggestion = objSuggestion;

    domWeatherTemperature.textContent = objWeatherData.fTemperature;
    domWeatherDescription.textContent = objWeatherData.strDescription;
    domWeatherWindSpeed.textContent = objWeatherData.fWindSpeed;
    domWeatherPressure.textContent = objWeatherData.nPressure;
    domWeatherHumidity.textContent = objWeatherData.nHumidity;
}

function setPreloaderVisible(bIsVisible){
    if (bIsVisible)
        domPreloader.classList.remove('hidden');
    else
        domPreloader.classList.add('hidden');
}

function showCitySelect(){
    domCitySelectBlock.classList.remove('hidden');
    domCityDataBlock.classList.add('hidden');
}

function showCityData(){
    domCityDataBlock.classList.remove('hidden');
    domCitySelectBlock.classList.add('hidden');
}

function setActiveUnitsButton(domSelectedButton){
    arDomUnitsButtons.forEach((domButton) => {
        domButton.classList.remove('active-unit')
    });

    domSelectedButton.classList.add('active-unit');
}

function unitsButtonOnClick(){
    setPreloaderVisible(false);
    setActiveUnitsButton(this);
    strCurrentSelectedUnits = this.dataset.units;
    setAppropriateUnitsStrings();
    fillWeatherBySuggestion(objCurrentSelectedSuggestion);
}

function setAppropriateUnitsStrings(){
    domSpeedUnitString.textContent = objLocaleStrings[strCurrentSelectedUnits].speed;
}

arDomUnitsButtons.forEach((domButton) => {
    domButton.addEventListener('click', unitsButtonOnClick);
});
domCurrentLocationButton.addEventListener('click', fillWeatherByGeolocation);
domCitySelectButton.addEventListener('click', showCitySelect);
registerSelectCityEvenListener(fillWeatherBySuggestion);
ymaps.ready(function(){
    fillWeatherByGeolocation();
});
setAppropriateUnitsStrings();
