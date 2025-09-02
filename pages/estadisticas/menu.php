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
                            <div id="inputs" class="modal-body">
                                <fieldset>
                                    <legend>Fecha de compra/venta</legend>
                                    <input type="date" value="" id="dateNewBalance">
                                </fieldset>
                                <fieldset>
                                    <legend>Monto</legend>
                                    <input type="number" id="valueNewBalance" value="0" autocomplete="off">
                                    <br>
                                    <span id="monto-formateado"></span>
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
                                    <legend>Exportar/Importar</legend>
                                    <div class="csvContainer">
                                        <img src="public\icons8-excel-50.svg" alt="Exportar a .CSV" width="20px">
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
                <script type="module">
                    import Swal from 'sweetalert2/dist/sweetalert2.js';
                    import 'sweetalert2/src/sweetalert2.scss';
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
                    // Sanitizar fecha (YYYY-MM-DD)
                    function sanitizeDate(input) {
                        const regex = /^\d{4}-\d{2}-\d{2}$/;
                        return regex.test(input) ? input : null;
                    }

                    // Sanitizar número
                    function sanitizeNumber(input) {
                        const num = parseFloat(input.toString().replace(/[^\d.-]/g, ''));
                        return isNaN(num) ? null : num;
                    }

                    // Sanitizar texto de tipo select (ej: COMPRA, VENTA)
                    function sanitizeText(input) {
                        const opciones = ["COMPRA", "VENTA"];
                        return opciones.includes(input.toUpperCase()) ? input.toUpperCase() : null;
                    }
                    function saveToTable(fecha, monto, tipo) {
                        // console.log(" SuFecha:"+fecha+" SuMonto:"+monto+" SuTipo:"+tipo);
                        const date = fecha;
                        const value = monto;
                        const type = tipo.toUpperCase();
                        const data = {
                            monto: value,
                            fecha: date,
                            tipo: type
                        }
                        $.ajax({
                            type: "GET",
                            url: "/saveNewBalances",
                            data: data,
                            dataType: "json",
                            success: function (response) {
                                let timerInterval;
                                Swal.fire({
                                    position: "bottom-end",
                                    title: response,
                                    timer: 1500,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        const timer = Swal.getPopup().querySelector("b");
                                        timerInterval = setInterval(() => { }, 100);
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval);
                                    }
                                }).then((result) => {
                                    /* Read more about handling dismissals below */
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                        console.log("I was closed by the timer");
                                    }
                                });
                            }
                        });
                        loadTable(); //Tras insertar, solicita tabla con nuevos registros
                    }
                    function loadTable() { // Carga La tabla con todos los datos en DB (transacciones)
                        const table = $("#tableOldBalances")[0];
                        // console.log("Loading Table wth new data");
                        $.ajax({
                            type: "GET",
                            url: "/getOldBalances",
                            dataType: "json",
                            success: function (response) {
                                const $tbody = $("#tableOldBalances");
                                $tbody.empty(); // limpiar contenido previo
                                const data = response;
                                $.each(data, function (i, row) {
                                    const tr = $("<tr>");
                                    tr.append($("<td>").text(row.fecha));
                                    tr.append($("<td>").text(new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(row.monto)));
                                    tr.append($("<td>").text(row.tipo));
                                    $tbody.append(tr);
                                });
                            },
                            error: function (xhr, status, error) {
                                console.error("Error al cargar balances antiguos: " + status + " - " + error);
                            }
                        });
                    }
                    $(function () {
                        const ymd = hoyLocalYYYYMMDD();
                        // console.log("Hora actual: "+parseDateLocal(ymd) + "----" + ymd);
                        $('#dateNewBalance').val(ymd);
                        $("#btnAdd").click(function () { loadTable(); });
                        $('#valueNewBalance').keyup(function (e) {
                            var defaultNumber = $('#valueNewBalance').val();
                            $('#monto-formateado').text(new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(defaultNumber));
                        });

                        $('#confirmNewBalance').on('click', function (e) {
                            e.preventDefault();
                            const date = sanitizeDate($('#dateNewBalance').val());
                            const value = sanitizeNumber($('#valueNewBalance').val());
                            const type = sanitizeText($('input[name="optNewBalance"]:checked').val());
                            // console.log("New balance to add: " + date + " | " + value + " | " + type);
                            // #TODO: Añadir botón de importar desde archivo CSV
                            // #TODO: Añadir exportador de tabla a CSV
                            if (value === 0) {
                                let timerInterval;
                                Swal.fire({
                                    position: "bottom-end",
                                    title: "Complete el monto primero",
                                    timer: 1500,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        const timer = Swal.getPopup().querySelector("b");
                                        timerInterval = setInterval(() => { }, 100);
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval);
                                    }
                                }).then((result) => {
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                        // console.log("I was closed by the timer");
                                    }
                                });
                            } else {
                                saveToTable(date, value, type);
                            }
                            emptyOutInputs(false, true, false);
                        });
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
                    function emptyOutInputs(d, n, s) { // n=numero (monto), d=fecha, s=selección (compra/venta)
                        n ? $('#valueNewBalance').val('0') : null; // statement IF minified, después del ":" es el ELSE
                        d ? $('#dateNewBalance').val(hoyLocalYYYYMMDD()) : null; // statement IF minified, después del ":" es el ELSE
                        s ? $('input[name="optNewBalance"][value="Compra"]').prop('checked', true) : null; // statement IF minified, después del ":" es el ELSE
                    }
                    // When the user clicks on <span> (x), close the modal
                    span.onclick = function () {
                        modal.style.display = "none";
                        emptyOutInputs(true, true, true);
                    }

                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function (event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                            emptyOutInputs(true, true, true);
                        }
                    }

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