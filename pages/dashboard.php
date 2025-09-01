<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'AppGro-Menú');
require dirname(__DIR__, 2) . '\system\resources\database.php';
$sql = "SELECT nombre FROM Usuarios WHERE id BETWEEN :min AND :max";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'min' => 1,
    'max' => 5,
]);
$usuarios = $stmt->fetchAll(); // array de filas
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

            <?php
            if (!empty($_SESSION['user_id'])):
                echo "<a class='absolute font-[monospace] [text-shadow:0px_0px_3px_#00000078] font-medium' >" . $_SESSION['user_id'] . "Bienvenido usuario: " . $_SESSION['user_name'];
                // unset($_SESSION['user_id']); //ELIMINA CONTENIDO (PODRIA SERVIR PARA CERRAR SESIÓN)
                // $_SESSION = [];  // Limpia el arreglo de sesión
            endif;
            ?>
            <?php
            if (!isset($_SESSION['contador'])) {
                $_SESSION['contador'] = 1;
            } else {
                $_SESSION['contador']++;
            }
            echo "Has visitado esta página " . $_SESSION['contador'] . " veces. </a>";
            ?>
            <br>
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
            </div>
            <!-- MAPA -->
            <div hidden class="main_containerDashboardMapa">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
                <link rel="stylesheet" href="src\styles\gridstack.css" />
                <script type="module" src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons/ionicons.esm.js"></script>
                <script nomodule="" src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons/ionicons.js"></script>
                <link href="node_modules/gridstack/dist/gridstack.min.css" rel="stylesheet" />
                <script src="../node_modules/gridstack/dist/gridstack-all.js"></script>
                <style type="text/css">
                    .grid-stack-item-removing {
                        opacity: 0.8;
                        filter: blur(5px);
                    }
                    
                    .sidepanel-item {
                        background-color: #18bc9c;
                        text-align: center;
                        padding: 5px;
                        margin-bottom: 15px;
                    }
                    
                    #trash {
                        background-color: rgba(255, 0, 0, 0.4);
                    }
                    
                    ion-icon {
                        font-size: 300%;
                    }
                </style>
                <h1>Advanced Demo</h1>
                <div class="row">
                    <div class="sidepanel col-md-2 d-none d-md-block">
                        <div id="trash" class="sidepanel-item">
                            <ion-icon name="trash"></ion-icon>
                            <div>Drop here to remove!</div>
                        </div>
                        <div class="grid-stack-item sidepanel-item">
                            <ion-icon name="add-circle"></ion-icon>
                            <div>Drag me in the dashboard!</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-10">
                        <div class="grid-stack"></div>
                    </div>
                </div>
                <script type="module">
                    import Swal from 'sweetalert2/dist/sweetalert2.js'
                    import 'sweetalert2/src/sweetalert2.scss'
                    $(function () {
                        $(window).resize(function () {
                            // your code 
                            var browserZoomLevel = Math.round(window.devicePixelRatio * 100);
                            console.log("Browser Zoom Level: " + browserZoomLevel + "%");
                            if (browserZoomLevel != 100) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...El zoom del navegador no es 100%.",
                                    text: "Por favor, ajuste el zoom a 100% para una mejor experiencia.",
                                    footer: '<a href="#">Why do I have this issue?</a>'
                                });
                            }
                        });
                        // NOTE: REAL apps would sanitize-html or DOMPurify before blinding setting innerHTML. see #2736
                        GridStack.renderCB = function (el, w) {
                            el.innerHTML = w.content;
                        };
                        let children = [
                            { x: 0, y: 0, w: 4, h: 2, content: '<input type="text">' },
                            { x: 4, y: 0, w: 4, h: 4, locked: true, content: 'locked: can\'t be pushed by others, only user!<br><ion-icon name="ios-lock"></ion-icon>' },
                            { x: 8, y: 0, w: 2, h: 2, minW: 2, noResize: true, content: '<p class="card-text text-center" style="margin-bottom: 0">Drag me!<p class="card-text text-center"style="margin-bottom: 0"><ion-icon name="hand"></ion-icon><p class="card-text text-center" style="margin-bottom: 0">...but don\'t resize me!' },
                            { x: 0, y: 2, w: 2, h: 2, content: '5' },
                            { x: 2, y: 2, w: 2, h: 4, content: '6' },
                            { x: 0, y: 4, w: 2, h: 2, content: '8' },
                            { x: 4, y: 4, w: 4, h: 2, content: '9' },
                            { x: 8, y: 4, w: 2, h: 2, content: '10' },
                        ];
                        let insert = [{ h: 2, content: 'new item' }];
                        
                        let grid = GridStack.init({
                            cellHeight: 70,
                            acceptWidgets: true,
                            removable: '#trash', // drag-out delete class
                            children
                        });
                        GridStack.setupDragIn('.sidepanel>.grid-stack-item', undefined, insert);
                        
                        grid.on('added removed change', function (e, items) {
                            let str = '';
                            items.forEach(function (item) { str += ' (x,y)=' + item.x + ',' + item.y; });
                            console.log(e.type + ' ' + items.length + ' items:' + str);
                        });
                    });
                </script>
            </div>
            <!--FIN DE MAPA -->
        <div class="mith-perspective">
            <div class="has-gradient-tracker stats-graphs">
                <div class="balance-container" id="balanceMenu"></div>
            </div>
        </div>
        <script> // Carga el HTML
            $('#balanceMenu').load('/pages/estadisticas/balanceMenu.html');
        </script>
        </div>
        <!-- <script>
            $(".btn-to-top").click(() => {
                $("html, body").animate({ scrollTop: 0 });
                });
            </script>
            <div class="btn-to-top">Back To Top</div> -->
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