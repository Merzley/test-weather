/**
 * @typedef SuggestionType
 * @type {object}
 * @property {string} strCityName
 * @property {string} strSuggestionId
 * @property {string} strText
 */

/**
 * @typedef WeatherType
 * @type {object}
 * @property {(number|null)} fTemperature
 * @property {(string|null)} strDescription
 * @property {(number|null)} fWindSpeed
 * @property {(number|null)} nWindDirection
 * @property {(number|null)} nPressure
 * @property {(number|null)} nHumidity
 */

export function ApiRequests() {
    function makeGetParamsString(arParams){
        let arResult = [];

        for (let index in arParams) if (arParams.hasOwnProperty(index)){
            arResult.push(encodeURIComponent(index)+'='+encodeURIComponent(arParams[index]));
        }

        return arResult.join('&');
    }

    function apiGetRequest(strUrl, arParams) {
        return new Promise((resolve, reject) => {
            let strGetParams = makeGetParamsString(arParams);

            let xhr = new XMLHttpRequest();
            xhr.open('GET', strUrl+'?'+strGetParams, true);
            xhr.send();
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;

                if (xhr.status !== 200) {
                    reject(xhr.responseText);
                }

                let result = JSON.parse(xhr.responseText);

                resolve(result);
            }
        })
    }

    this.getCitySuggestions = (strText) => {
        let arParams = {text: strText};
        return apiGetRequest('/api/city-suggestions', arParams);
    };

    this.getWeather = (strSuggestionId, strUnits, strLocale) => {
        let arParams = {
            suggestion_id: strSuggestionId,
            units: strUnits,
            locale: strLocale
        };
        return apiGetRequest('/api/weather', arParams);
    };

    this.getGeolocationAddress = () => {
        return ymaps.geolocation.get({
            provider: 'auto',
            autoReverseGeocode: true
        })
        .then((result) => {
            let strAddress = result.geoObjects.get(0).properties.get('text');
            return this.getCitySuggestions(strAddress);
        })
        .then((arSuggestions) => {
            return arSuggestions[0];
        });
    };

    return this;
}
