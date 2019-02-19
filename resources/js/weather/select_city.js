const ApiRequests = new (require('./api_requests').ApiRequests)();

let domCityInput = document.getElementById('js-select-city-input');
let domDropDown = document.getElementById('js-suggestions');
let domDropDownList = document.getElementById('js-suggestions-list');

const suggestionRequestDelay = 300;
let lastSuggestionsRequestTimer = null;

let needHideDropdown = true;

let eventListeners = [];

export function registerSelectCityEvenListener(callable){
    eventListeners.push(callable);
}

function dispatchEvent(objSuggestion){
    eventListeners.forEach((callable) => {
        callable(objSuggestion)
    })
}

function requestSuggestions(){
    let strText = domCityInput.value;

    return ApiRequests.getCitySuggestions(strText)
        .then((arSuggestions) => {
            clearDropDownItems();
            drawDropDownItems(arSuggestions);
            setDropDownVisible(true);
        })
}

function setDropDownVisible(bIsVisible){
    if (bIsVisible)
        domDropDown.classList.remove('hidden');
    else
        domDropDown.classList.add('hidden');
}

function clearDropDownItems(){
    while (domDropDownList.firstChild)
        domDropDownList.removeChild(domDropDownList.firstChild);
}

function drawDropDownItems(arSuggestions){
    arSuggestions.forEach((objSuggestion) => {
        let newDomSuggestion = document.createElement('div');
        let newDomSuggestionText = document.createElement('span');

        newDomSuggestion.classList.add('suggestions-suggestion');
        newDomSuggestion.objSuggestion = objSuggestion;
        newDomSuggestion.addEventListener('click', suggestionOnClick);
        newDomSuggestionText.textContent = objSuggestion.strText;

        newDomSuggestion.appendChild(newDomSuggestionText);
        domDropDownList.appendChild(newDomSuggestion);

        setDropDownVisible(true);
    })
}

function suggestionOnClick(){
    dispatchEvent(this.objSuggestion);
    setDropDownVisible(false);
    clearDropDownItems();
    domCityInput.value = '';
}

function cityInputOnInput(){
    if (lastSuggestionsRequestTimer != null)
        clearTimeout(lastSuggestionsRequestTimer);

    lastSuggestionsRequestTimer = setTimeout(() => {
        requestSuggestions()
        .then(() => {
            lastSuggestionsRequestTimer = null;
        })
    }, suggestionRequestDelay);
}

domCityInput.addEventListener('input', cityInputOnInput);
domCityInput.addEventListener('blur', () =>{
    if (needHideDropdown)
        setDropDownVisible(false);
});
domCityInput.addEventListener('focus', () => {
    setDropDownVisible(true);
});

domDropDown.addEventListener('mouseover', () => {
    needHideDropdown = false;
});
domDropDown.addEventListener('mouseout', () => {
    needHideDropdown = true;
});
