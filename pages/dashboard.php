<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'AppGro-Menú');
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerDashboard">
            <div id="alertBox" class="alertBox">
                <?php
                if (!empty($_SESSION['error'])):
                    alertBox($_SESSION['error'], null);
                    unset($_SESSION['error']);

                elseif (!empty($_SESSION['success'])):
                    alertBox(null, $_SESSION['success']);
                    unset($_SESSION['success']);
                endif;
                ?>
            </div>
            <br>
            <a id="top"></a>
            <div id="containerTiempo" class="main_containerDashboardTiempo">
                <div class="with-perspective">
                    <div class="has-gradient-tracker weather-container">
                        <div class="weather-container">
                            <div class="movement-listener"></div>
                            <h2>Estado del Clima</h2>
                            <div id="weatherInfo" class="weather-info">
                                <!-- Aquí se mostrará la información del clima -->
                                <!-- #TODO: Hacer un fondo dinámico en relación al estado actual del clima. captura estado: var conditionText = data.current.condition.text y asigna fondo del  -->
                                <?php
                                weatherApi();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main_containerEstadisticas">

                <div class="stats-container">
                    <h1 class="stats-title">Estadísticas Productivas</h1>
                    <!-- Filtros generales -->
                    <div class="stats-filtros">
                        <label>Desde: <input type="date" id="filtro_desde" class="stats-select" /></label>
                        <label>Hasta: <input type="date" id="filtro_hasta" class="stats-select" /></label>
                        <label>Grupo:
                            <select id="filtro_agrupado" class="stats-select">
                                <option value="mes">Mensual</option>
                                <option value="dia">Diario</option>
                                <option value="anio">Anual</option>
                            </select>
                        </label>
                    </div>

                    <!-- Selector de pestañas -->
                    <div class="stats-tabs">
                        <button class="stats-tab-btn active" data-target="ventas">Ventas</button>
                        <button class="stats-tab-btn" data-target="compras">Compras</button>
                        <button class="stats-tab-btn" data-target="balance">Balance</button>
                        <button class="stats-tab-btn" data-target="ganado">Altas Ganado</button>
                        <button class="stats-tab-btn" data-target="cultivos">Altas Cultivos</button>
                    </div>

                    <!-- Contenedores de gráficos -->
                    <div class="stats-graphs">
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <div class="stats-chart-container active" id="tab-ventas">
                            <canvas id="grafico_ventas" class="stats-canvas"></canvas>
                        </div>
                        <div class="stats-chart-container" id="tab-compras">
                            <canvas id="grafico_compras" class="stats-canvas"></canvas>
                        </div>
                        <div class="stats-chart-container" id="tab-balance">
                            <canvas id="grafico_balance" class="stats-canvas"></canvas>
                        </div>
                        <div class="stats-chart-container" id="tab-ganado">
                            <canvas id="grafico_ganado" class="stats-canvas"></canvas>
                        </div>
                        <div class="stats-chart-container" id="tab-cultivos">
                            <canvas id="grafico_cultivos" class="stats-canvas"></canvas>
                        </div>
                    </div>
                </div>
                <script src="src/scripts/dashboard.js"></script>
                <script type="module">
                    import Swal from 'sweetalert2/dist/sweetalert2.js'
                    import 'sweetalert2/src/sweetalert2.scss'
                    $(function () {
                        var showOneTime = true;
                        $(window).resize(function () {
                            // your code 
                            var browserZoomLevel = Math.round(window.devicePixelRatio * 100);
                            console.log("Browser Zoom Level: " + browserZoomLevel + "%");
                            if (browserZoomLevel != 100) {
                                if (!showOneTime) return;
                                showOneTime = false;
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...El zoom del navegador no es 100%.",
                                    text: "Por favor, ajuste el zoom a 100% para una mejor experiencia.",
                                    footer: '<a href="#">Why do I have this issue?</a>'
                                });
                            }
                        });
                    });
                </script>
            </div>
            <div class="mith-perspective">
                <div class="has-gradient-tracker stats-graphs">
                    <div class="balance-container" id="balanceMenu"></div>
                </div>
            </div>
            <script>
                $('#balanceMenu').load('/pages/estadisticas/balanceMenu.html');
            </script>
        </div>
        <a class="btn-to-top" href="#top">Back to top</a>
    </div>
    <script>
        $(function () {
            gradientTracker({
                selector: '.stats-graphs',
                color1: '',
                color2: '',
                maxTilt: 20
            });
            gradientTracker({
                selector: '.weather-container',
                color1: '#007950',
                color2: '#00787a',
                maxTilt: 14
            });
        });
    </script>
</main>