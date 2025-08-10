<?php
require('system/main.php');
require dirname(__DIR__, 3) . '\system\resources\database.php';
sessionCheck();
$layout = new HTML(title: 'Estadísticas Productivas');
?>

<main class="main__content">
    <div class="main_container">
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
        </div>
    </div>
    </div>
</main>