<?php
require('system/main.php');
require dirname(__DIR__, 3) . '\system\resources\database.php';
sessionCheck();
$layout = new HTML(title: 'Menú de Estadísticas');
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerMenuEstadisticas">
                <div class="mith-perspective">
                    <div class="has-gradient-tracker balance-container">
                        <div id="balanceMenu"></div>
                    </div>
                </div>
                <script> // Carga el HTML
                    $('#balanceMenu').load('/pages/estadisticas/balanceMenu.html');
                </script>
            <h1 class="menu-title">Menú de Estadísticas</h1>
            <p>Seleccione una categoría para ver las estadísticas correspondientes.</p>
            <ul class="menu-list">
                <li><a href="/estadisticasResumen" class="menu-link">Administrar Balance</a></li>
                <li><a href="/grupos_ganado" class="menu-link">Administrar Ganado</a></li>
                <li><a href="/cultivos" class="menu-link">Administrar Cultivos</a></li>
            </ul>
        </div>
    </div>
</main>
<script>
    $(function () {
        gradientTracker({
            selector: '.balance-container', // selector del contenedor
            color1: '#9b9b9ba8', // color del centro del gradiente
            color2: '#00000000', // color externo del gradiente
            maxTilt: 15, // inclinación máxima en grados
            resetOnLeave: true, // si es true, la tarjeta vuelve a la posición inicial al salir el cursor
        });
    });
</script>