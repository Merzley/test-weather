<input id='js-select-city-input'
       class="select-city-input"
       type="text"
       placeholder="@lang('weather.select_city')"
>

<div id='js-suggestions' class="suggestions-wrapper hidden">
    <div class="suggestions-suggestions">
        <div class="suggestions-hint">
            @lang('weather.select_one_or_continue_input')
        </div>
        <div id='js-suggestions-list' class="suggestions-list">
        </div>
    </div>
</div>
