<?php

function weatherApi(): void
{
?>
<script>
    $(function() {
        $('#btnClima').on('click', function() {
            $('#displayClima').html('<p>Obteniendo ubicación…</p>');

            if (!navigator.geolocation) {
                $('#displayClima').html('<p>Geolocalización no soportada.</p>');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;
                    var apiKey = "<?php echo $_ENV['APIKEY_WEATHER'];?>";
                    var urlClima = 'https://api.weatherapi.com/v1/current.json?key=' + apiKey + '&q=' + lat + ',' + lon + '&lang=es';
                    $.getJSON(urlClima, function(data) {
                            // —————— LOCATION ——————
                            var locationName = data.location.name; // "Buenos Aires"
                            var locationRegion = data.location.region; // "Distrito Federal"
                            var locationCountry = data.location.country; // "Argentina"
                            var locationLat = data.location.lat; // -34.588
                            var locationLon = data.location.lon; // -58.673
                            var locationTzId = data.location.tz_id; // "America/Argentina/Buenos_Aires"
                            var locationLocaltimeEpoch = data.location.localtime_epoch; // 1748969288
                            var locationLocaltime = data.location.localtime; // "2025-06-03 13:48"
                            // —————— CURRENT ——————
                            var currentLastUpdatedEpoch = data.current.last_updated_epoch; // 1748969100
                            var currentLastUpdated = data.current.last_updated; // "2025-06-03 13:45"
                            var currentTempC = data.current.temp_c; // 17.1
                            var currentTempF = data.current.temp_f; // 62.8
                            var currentIsDay = data.current.is_day; // 1
                            // CONDITION (subobjeto)
                            var conditionText = data.current.condition.text; // "Soleado"
                            var conditionIcon = 'https:' + data.current.condition.icon; // "//cdn.weatherapi.com/…png"
                            var conditionCode = data.current.condition.code; // 1000
                            var currentWindMph = data.current.wind_mph; // 4.3
                            var currentWindKph = data.current.wind_kph; // 6.8
                            var currentWindDegree = data.current.wind_degree; // 15
                            var currentWindDir = data.current.wind_dir; // "NNE"
                            var currentPressureMb = data.current.pressure_mb; // 1016
                            var currentPressureIn = data.current.pressure_in; // 30
                            var currentPrecipMm = data.current.precip_mm; // 0
                            var currentPrecipIn = data.current.precip_in; // 0
                            var currentHumidity = data.current.humidity; // 55
                            var currentCloud = data.current.cloud; // 0
                            var currentFeelsLikeC = data.current.feelslike_c; // 17.1
                            var currentFeelsLikeF = data.current.feelslike_f; // 62.8
                            var currentWindchillC = data.current.windchill_c; // 14.3
                            var currentWindchillF = data.current.windchill_f; // 57.8
                            var currentHeatindexC = data.current.heatindex_c; // 14.4
                            var currentHeatindexF = data.current.heatindex_f; // 57.9
                            var currentDewpointC = data.current.dewpoint_c; // 6.7
                            var currentDewpointF = data.current.dewpoint_f; // 44
                            var currentVisKm = data.current.vis_km; // 10
                            var currentVisMiles = data.current.vis_miles; // 6
                            var currentUv = data.current.uv; // 2.2
                            var currentGustMph = data.current.gust_mph; // 5.4
                            var currentGustKph = data.current.gust_kph; // 8.7
                            $('#displayClima').html(
                                '<h4>Clima en ' + locationName + '<br>' + locationRegion + '</h4>' +
                                '<p><img src="' + conditionIcon + '" alt="' + conditionText + '" /> <strong>' + conditionText + '</strong></p>' +
                                '<ul style="list-style:none; padding:0; margin:0;">' +
                                '<li>Horario: <strong>' + locationLocaltime + '</strong></li>' +
                                '<li>Temperatura: <strong>' + currentTempC + ' °C</strong></li>' +
                                '<li>Humedad: <strong>' + currentHumidity + '%</strong></li>' +
                                '<li>Viento: <strong>' + currentWindKph + ' km/h (' + currentWindDir + ')</strong></li>' +
                                '<li>Visibilidad: <strong>' + currentVisKm + ' km</strong></li>' +
                                '<li>Sensación: <strong>' + currentFeelsLikeC + ' °C</strong></li>' +
                                '<li>Punto de rocío: <strong>' + currentDewpointC + ' °C</strong></li>' +
                                '<li>Índice UV: <strong>' + currentUv + '</strong></li>' +
                                '<li>Ráfagas: <strong>' + currentGustKph + ' km/h</strong></li>' +
                                '</ul>'
                            );
                        })
                        .fail(function() {
                            $('#displayClima').html('<p>Error al consultar WeatherAPI.</p>');
                        });
                },
                function() {
                    $('#displayClima').html('<p>No se pudo obtener la ubicación.</p>');
                }, {
                    timeout: 10000
                }
            );
        });
    });
</script>
    <style>
        .buttonMostrarClima {
            appearance: none;
            background-color: #2ea44f;
            border: 1px solid rgba(27, 31, 35, 0.15);
            border-radius: 6px;
            box-shadow: rgba(27, 31, 35, 0.1) 0 1px 0;
            box-sizing: border-box;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            font-family:
                -apple-system, system-ui, 'Segoe UI', Helvetica, Arial,
                sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji';
            font-size: 14px;
            font-weight: 600;
            line-height: 20px;
            padding: 6px 16px;
            position: relative;
            text-align: center;
            text-decoration: none;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: middle;
            white-space: nowrap;
        }

        .buttonMostrarClima:focus:not(:focus-visible):not(.focus-visible) {
            box-shadow: none;
            outline: none;
        }

        .buttonMostrarClima:hover {
            background-color: #2c974b;
        }

        .buttonMostrarClima:focus {
            box-shadow: rgba(46, 164, 79, 0.4) 0 0 0 3px;
            outline: none;
        }

        .buttonMostrarClima:disabled {
            background-color: #94d3a2;
            border-color: rgba(27, 31, 35, 0.1);
            color: rgba(255, 255, 255, 0.8);
            cursor: default;
        }

        .buttonMostrarClima:active {
            background-color: #298e46;
            box-shadow: rgba(20, 70, 32, 0.2) 0 1px 0 inset;
        }
    </style>

        <input type="hidden" name="climaJson" id="climaJson">
        <input type="button" value="CargarClima" id="btnClima" class="buttonMostrarClima">
        <div id="demo"></div>
        <div id="displayClima"></div><a href="https://www.weatherapi.com/" title="Free Weather API"><img src='//cdn.weatherapi.com/v4/images/weatherapi_logo.png' alt="Weather data by WeatherAPI.com" border="0"></a>
        

<?php
}
?>