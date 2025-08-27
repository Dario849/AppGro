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
                    <button id="btnAdd"><img width="20px" src="plus-solid-full.svg" alt="Agregar" class="stats-tab-icon"
                            title="Agregar" data-target="agregar" /></button>

                    <!-- The Modal -->
                    <div id="myModal" class="modal">

                        <!-- Modal content -->
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <br>
                            <p>Some text in the Modal..</p>
                            <div id="inputs" class="modal-body">
                                <fieldset>
                                    <legend>Fecha de compra/venta</legend>
                                    <input type="date" name="dateNewBalance" value="" id="dateNewBalance">
                                </fieldset>
                                <fieldset>
                                    <legend>Monto</legend>
                                    <input type="number" name="valueNewBalance" value="0" id="valueNewBalance">
                                </fieldset>
                                <fieldset>
                                    <legend>Tipo de nuevo balance</legend>
                                    <!-- From Uiverse.io by andrew-demchenk0 -->
                                    <div class="opcionBalance">
                                        <div class="option">
                                            <input class="input" type="radio" id="optNewCompra" name="optNewBalance"
                                                value="Compra" checked="">
                                            <div class="btn">
                                                <span class="span">Compra</span>
                                            </div>
                                        </div>
                                        <div class="option">
                                            <input class="input" type="radio" id="optNewVenta" name="optNewBalance"
                                                value="Venta">
                                            <div class="btn">
                                                <span class="span">Venta</span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <button id="confirmNewBalance" class="submit-button">
                                        &crarr; Añadir
                                    </button>
                                </fieldset>
                            </div>
                            <div class="table-container">
                                <table>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Valor</th>
                                        <th>Tipo</th>
                                    </tr>
                                    <tbody id="tableOldBalances">
                                        <!-- Mockup data -->
                                        <tr>
                                            <td>2023-01-01</td>
                                            <td>1000</td>
                                            <td>Venta</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-02</td>
                                            <td>500</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-03</td>
                                            <td>750</td>
                                            <td>Venta</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                        <tr>
                                            <td>2023-01-04</td>
                                            <td>300</td>
                                            <td>Compra</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
                <script>
                    function hoyLocalYYYYMMDD() {
                        const n = new Date(); // Hora local
                        const yyyy = n.getFullYear(); // extrae year
                        const mm = String(n.getMonth() + 1).padStart(2, '0'); // Extrae month y lo ajusta a 2 dígitos
                        const dd = String(n.getDate()).padStart(2, '0'); // Extrae day y lo ajusta a 2 dígitos
                        return `${yyyy}-${mm}-${dd}`; // Formato YYYY-MM-DD
                    }

                    function parseDateLocal(ymd) { // yyyy-mm-dd → Date local 00:00
                        const [y, m, d] = ymd.split('-').map(Number); // extrae partes y convierte a números
                        // console.log("parseDateLocal: y="+y+" m="+m+" d="+d);
                        return new Date(y, m - 1, d); // Solicita fecha parseada con datos locales
                    }

                    $(function () {
                        const ymd = hoyLocalYYYYMMDD();
                        // console.log("Hora actual: "+parseDateLocal(ymd) + "----" + ymd);
                        $('#dateNewBalance').val(ymd);
                    });
                    // Get the modal
                    var modal = document.getElementById("myModal");

                    // Get the button that opens the modal
                    var btn = document.getElementById("btnAdd");

                    // Get the <span> element that closes the modal
                    var span = document.getElementsByClassName("close")[0];

                    // When the user clicks the button, open the modal 
                    btn.onclick = function () {
                        modal.style.display = "block";
                    }
                    function emptyOutInputs(d,n,s) { // n=numero (monto), d=fecha, s=selección (compra/venta)
                        n ? $('#valueNewBalance').val('0'): null; // statement IF minified, después del ":" es el ELSE
                        d ? $('#dateNewBalance').val(hoyLocalYYYYMMDD()): null; // statement IF minified, después del ":" es el ELSE
                        s ? $('input[name="optNewBalance"][value="Compra"]').prop('checked', true): null; // statement IF minified, después del ":" es el ELSE
                    }
                    // When the user clicks on <span> (x), close the modal
                    span.onclick = function () {
                        modal.style.display = "none";
                        emptyOutInputs(d=true,n=true,s=true);
                    }

                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function (event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                            emptyOutInputs(d=true,n=true,s=true);
                        }
                    }
                    $('#confirmNewBalance').on('click', function (e) {
                        e.preventDefault();
                        const date = $('#dateNewBalance').val();
                        const value = parseFloat($('#valueNewBalance').val());
                        const type = $('input[name="optNewBalance"]:checked').val();
                        console.log("New balance to add: " + date + " | " + value + " | " + type);
                        // #TODO: Añadir llamado AJAX para enviar los datos al servidor y actualizar la base de datos
                        // #TODO: Añadir botón de importar desde archivo CSV
                        // #TODO: Validar datos antes de enviar
                        // #TODO: Actualizar tabla de balances antiguos
                        // #TODO: Añadir exportador de tabla a CSV
                        // modal.style.display = "none";
                        emptyOutInputs(d=false,n=true,s=false);
                    });

                </script>
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