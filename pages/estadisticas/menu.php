<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Estadísticas Productivas', uid: $_SESSION['user_id']);
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
                                    <b><span id="monto-formateado"></span></b>
                                </fieldset>
                                <fieldset>
                                    <legend>Tipo de nuevo balance</legend>
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
                                    <legend>Tipo de producto</legend>
                                    <div class="opcionProducto">
                                        <div class="option">
                                            <input class="input" type="radio" id="optNewGanado" name="opcionProducto"
                                                value="Ganado" checked="">
                                            <div class="btn">
                                                <span class="span">Ganado</span>
                                            </div>
                                        </div>
                                        <div class="option">
                                            <input class="input" type="radio" id="optNewCultivo" name="opcionProducto"
                                                value="Cultivo">
                                            <div class="btn">
                                                <span class="span">Cultivo</span>
                                            </div>
                                        </div>
                                        <div class="option">
                                            <input class="input" type="radio" id="optNewHerramienta"
                                                name="opcionProducto" value="Herramienta">
                                            <div class="btn">
                                                <span class="span">Herramienta</span>
                                            </div>
                                        </div>
                                        <div class="option">
                                            <input class="input" type="radio" id="optNewOtro" name="opcionProducto"
                                                value="Otro">
                                            <div class="btn">
                                                <span class="span">Otro</span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend>Cantidad</legend>
                                    <input type="number" id="valueNewCantidad" value="0" autocomplete="off">
                                </fieldset>
                                <fieldset>
                                    <legend>Descripción de producto</legend>
                                    <textarea placeholder="Ingrese posible descripción del producto"
                                        class="textareaComentario" id="optNewComentario"></textarea>
                                </fieldset>
                                <fieldset>
                                    <legend>Exportar/Importar</legend>
                                    <div class="csvContainer">
                                        <button id="csvBtn">
                                            <svg class="icon" style="color:black;">
                                                <use href="excel_logo.svg#icon-libro"></use>
                                            </svg>
                                        </button>
                                        <div id="csvModal" class="modal">

                                            <!-- Modal content -->
                                            <div id="csvmodal-content" class="csvmodal-content">
                                                <span class="csvClose">&times;</span>
                                                <p>Seleccione que acción desea realizar</p>
                                                <fieldset>
                                                    <legend>Exportar datos actuales</legend>
                                                    <button id="btnXlsx" class="container-btn-file">
                                                        <svg class="icon">
                                                            <use href="excel_logo.svg#icon-libro"></use>
                                                        </svg>
                                                        Descargar archivo
                                                    </button>
                                                    <br>
                                                    <button id="btnCsv" class="container-btn-file">
                                                        <svg class="icon">
                                                            <use href="ext_csv_filetype_icon_176252.svg"></use>
                                                        </svg>
                                                        Descargar archivo
                                                    </button>
                                                    <script>
                                                        async function fetchTransacciones() {
                                                            const resp = await fetch('/transacciones');
                                                            const json = await resp.json();
                                                            if (json.status !== 'success') throw new Error(json.message);
                                                            return json.data;
                                                        }

                                                        function exportCsv(rows) {
                                                            // convertir a hoja
                                                            const ws = XLSX.utils.json_to_sheet(rows);
                                                            const csv = XLSX.utils.sheet_to_csv(ws);
                                                            downloadFile('transacciones.csv', csv, 'text/csv');
                                                        }

                                                        function exportXlsx(rows) {
                                                            const wb = XLSX.utils.book_new();
                                                            const ws = XLSX.utils.json_to_sheet(rows);
                                                            XLSX.utils.book_append_sheet(wb, ws, "Transacciones");
                                                            const out = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
                                                            const blob = new Blob([out], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                                                            downloadBlob('transacciones.xlsx', blob);
                                                        }

                                                        function downloadFile(filename, content, type) {
                                                            const blob = new Blob([content], { type });
                                                            downloadBlob(filename, blob);
                                                        }
                                                        function downloadBlob(filename, blob) {
                                                            const link = document.createElement('a');
                                                            link.href = URL.createObjectURL(blob);
                                                            link.download = filename;
                                                            document.body.appendChild(link);
                                                            link.click();
                                                            document.body.removeChild(link);
                                                        }

                                                        $('#btnCsv').on('click', async () => {
                                                            try { exportCsv(await fetchTransacciones()); }
                                                            catch (e) { alert("Error: " + e.message); }
                                                        });
                                                        $('#btnXlsx').on('click', async () => {
                                                            try { exportXlsx(await fetchTransacciones()); }
                                                            catch (e) { alert("Error: " + e.message); }
                                                        });
                                                    </script>

                                                </fieldset>
                                                <fieldset>
                                                    <legend>Descargar plantilla</legend>
                                                    <button class="container-btn-file">
                                                        <svg class="icon">
                                                            <use href="public\excel_logo.svg#icon-libro"></use>
                                                        </svg>
                                                        <a class="file" href="Plantilla_Balances-AppGro.xlsx"></a>
                                                        Descargar Archivo
                                                    </button>
                                                    </button>
                                                </fieldset>
                                                <fieldset>
                                                    <legend>Importar datos al sistema</legend>
                                                    <button class="container-btn-file">
                                                        <svg class="icon">
                                                            <use href="public\excel_logo.svg#icon-libro"></use>
                                                        </svg>
                                                        Subir archivo
                                                        <input class="file" id="fileImportCsv" type="file"
                                                            accept=".csv,.xlsx,.xlsm" />
                                                    </button>

                                                    <p>Contenido a importar:</p>
                                                    <pre id="outputImportCsv"></pre>
                                                    <p>Preview tabla:</p>
                                                    <table id="csvTable">

                                                    </table>
                                                    <button id="btnImportCsv">Importar archivo</button>
                                                </fieldset>
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
                                        <th>Nro</th>
                                        <th>Fecha</th>
                                        <th>Valor</th>
                                        <th>Tipo</th>
                                        <th>Usuario reponsable</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Comentario</th>
                                    </tr>
                                    <tbody id="tableOldBalances">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <script type="text/javascript" src="public/js/xlsx.full.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script type="module">
                    // #TODO: Añadir resumen de ganado donde: Muestre total activo, toma en cuenta tablas de defuncion, debería de mostrar un intervalo, y relacionar datos de ventas y compras a su vez
                    /**
                     * ¿Vacío?
                     * Propósito: detectar valores "vacíos" útiles para formularios/CSV.
                     * Algoritmo: true si v es null/undefined o si su representación string, recortada, queda "".
                     * Entrada: cualquier tipo (any).
                     * Salida: boolean.
                     * Ejemplos: isEmpty(null)→true, isEmpty("  ")→true, isEmpty(0)→false.
                     */
                    const isEmpty = (v) => v == null || String(v).trim() === "";

                    /**
                     * Convertir fecha dd/mm/aaaa → yyyy-mm-dd (ISO parcial).
                     * Propósito: normalizar fechas de CSV a formato SQL.
                     * Algoritmo:
                     *   1) Trim. Si queda "", retorna "" (campo opcional).
                     *   2) Validar patrón dd/mm/aaaa con regex.
                     *   3) Construir Date(yyyy, mm-1, dd) y revalidar componentes (descarta 31/02, etc.).
                     *   4) Formatear yyyy-mm-dd con cero a la izquierda.
                     * Entrada: string con formato "dd/mm/aaaa" o vacío.
                     * Salida: string "yyyy-mm-dd" si es válida, "" si input vacío, o null si inválida.
                     * Ejemplos: toYMD("05/09/2025")→"2025-09-05"; toYMD("")→""; toYMD("31/02/2024")→null.
                     */
                    const toYMD = (dmy) => {
                        const t = String(dmy).trim();
                        if (t === "") return ""; // campo opcional
                        const m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(t);
                        if (!m) return null; // formato incorrecto

                        const dd = +m[1], mm = +m[2], yyyy = +m[3];

                        // Construye fecha 
                        const dt = new Date(yyyy, mm - 1, dd);
                        const valida =
                            dt.getFullYear() === yyyy &&
                            dt.getMonth() === mm - 1 &&
                            dt.getDate() === dd;
                        if (!valida) return null;

                        // Formatea ISO yyyy-mm-dd
                        return `${yyyy}-${String(mm).padStart(2, "0")}-${String(dd).padStart(2, "0")}`;
                    };

                    /**
                     * ¿Celda CSV segura?
                     * Propósito: mitigar inyección CSV en Excel/Sheets (celdas que empiezan con =, +, -, @).
                     * Algoritmo: convierte a string, trim, y verifica que NO comience con esos prefijos.
                     * Entrada: cualquier tipo (any).
                     * Salida: boolean (true si NO es peligrosa).
                     * Ejemplos: isSafeCsvCell("=cmd()")→false; isSafeCsvCell(" producto ")→true.
                     */
                    const isSafeCsvCell = (v) => !/^[=\-+@]/.test(String(v ?? "").trim());
                    function hoyLocalYYYYMMDD() {
                        const n = new Date(); // Hora local
                        const yyyy = n.getFullYear(); // extrae year
                        const mm = String(n.getMonth() + 1).padStart(2, '0'); // Extrae month y lo ajusta a 2 dígitos
                        const dd = String(n.getDate()).padStart(2, '0'); // Extrae day y lo ajusta a 2 dígitos
                        return `${yyyy}-${mm}-${dd}`; // Formato YYYY-MM-DD
                    }
                    // Parseador: soporta ; o , por línea
                    function parseCSV(text) {
                        const rows = [];
                        const lines = text.split(/\r?\n/);
                        for (let i = 0; i < lines.length; i++) {
                            let line = lines[i];
                            if (!line || /^\s*$/.test(line)) continue; // saltear vacías
                            // elegimos separador por conteo
                            const semi = (line.match(/;/g) || []).length;
                            const coma = (line.match(/,/g) || []).length;
                            const sep = semi >= coma ? ';' : ',';
                            const cols = line.split(sep).map(c => c.trim());
                            rows.push(cols);
                        }
                        return rows;
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
                        const opciones = ["COMPRA", "VENTA", "GANADO", "CULTIVO", "HERRAMIENTA", "OTRO"];
                        return opciones.includes(input.toUpperCase()) ? input.toUpperCase() : null;
                    }
                    function saveToTable(fecha, monto, tipo, uid, producto, cantidad, comentario) {
                        // console.log(" SuFecha:"+fecha+" SuMonto:"+monto+" SuTipo:"+tipo);
                        const date = fecha;
                        const value = monto;
                        const type = tipo.toUpperCase();
                        const userId = sanitizeNumber(uid);
                        const prod = producto.toUpperCase();
                        const amount = cantidad;
                        const comment = comentario;
                        const data = {
                            monto: value,
                            fecha: date,
                            tipo: type,
                            uid: userId,
                            prod: prod,
                            prod_num: amount,
                            detail: comment
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
                                })
                                // .then((result) => {
                                //     /* Read more about handling dismissals below */
                                //     if (result.dismiss === Swal.DismissReason.timer) {
                                //         console.log("I was closed by the timer");
                                //     }
                                // });
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
                                    const index = i;
                                    tr.append($("<td>").text(index));
                                    tr.append($("<td>").text(row.fecha));
                                    tr.append($("<td>").text(new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(row.monto)));
                                    tr.append($("<td>").text(row.tipo));
                                    tr.append($("<td>").text(row.usuario_responsable));
                                    tr.append($("<td>").text(row.dato));
                                    tr.append($("<td>").text(row.dato_cantidad));
                                    tr.append($("<td>").text(row.detalle));
                                    $tbody.append(tr);
                                });
                            },
                            error: function (xhr, status, error) {
                                console.error("Error al cargar balances antiguos: " + status + " - " + error);
                            }
                        });
                    }

                    function emptyOutInputs(d, n, s, p, c) { // n=numero (monto), d=fecha, s=selección (compra/venta)
                        n ? $('#valueNewBalance').val('0') : null; // statement IF minified, después del ":" es el ELSE
                        d ? $('#dateNewBalance').val(hoyLocalYYYYMMDD()) : null; // statement IF minified, después del ":" es el ELSE
                        s ? $('input[name="optNewBalance"][value="Compra"]').prop('checked', true) : null; // statement IF minified, después del ":" es el ELSE
                        p ? $('input[name="opcionProducto"][value="Ganado"]').prop('checked', true) + $('#valueNewCantidad').val('0') : null;
                        c ? $('#optNewComentario').val("") : null;
                    }

                    var rows;
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
                            const uid = sanitizeNumber($('#uid_n').val());
                            const optProd = sanitizeText($('input[name="opcionProducto"]:checked').val());
                            const cantProd = sanitizeNumber($('#valueNewCantidad').val());
                            const textDescription = $('#optNewComentario').val();
                            console.log("New balance to add: " + date + " | " + value + " | " + type + " | " + optProd + " | " + cantProd + " | " + textDescription);
                            if (value === 0) {
                                let timerInterval;
                                Swal.fire({
                                    position: "bottom-end",
                                    title: "Un monto válido es requerido",
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
                                })
                                // .then((result) => {
                                //     if (result.dismiss === Swal.DismissReason.timer) {
                                //         console.log("I was closed by the timer");
                                //     }
                                // });
                            } else {
                                saveToTable(date, value, type, uid, optProd, cantProd, textDescription);
                            }
                            emptyOutInputs(false, true, false, false, true);
                        });
                        // Get the modal
                        var modal = document.getElementById("myModal");
                        var csvmodal = document.getElementById("csvModal");

                        // Get the button that opens the modal
                        var btn = document.getElementById("btnAdd");
                        var csvbtn = document.getElementById("csvBtn");

                        // Get the <span> element that closes the modal
                        var span = document.getElementsByClassName("close")[0];
                        var csvspan = document.getElementsByClassName("csvClose")[0];

                        // When the user clicks the button, open the modal 
                        btn.onclick = function () {
                            modal.style.display = "block";
                        }
                        csvbtn.onclick = function () {
                            const csvstyles = {
                                'width': "100vh",
                                'margin-top': "5vh",
                            }
                            csvmodal.style.display = "block";
                            $(".csvmodal-content").css(csvstyles);
                        }
                        // When the user clicks on <span> (x), close the modal
                        span.onclick = function () {
                            modal.style.display = "none";
                            emptyOutInputs(true, true, true, true, true);
                        }
                        csvspan.onclick = function () {
                            csvmodal.style.display = "none";
                        }
                        // When the user clicks anywhere outside of the modal, close it
                        window.onclick = function (event) {
                            if (event.target == modal) {
                                modal.style.display = "none";
                                emptyOutInputs(true, true, true, true, true);
                            }
                            if (event.target == csvmodal) {
                                csvmodal.style.display = "none";
                                $('#csvTable').html('');
                            }
                        }
                        $('#fileImportCsv').on('change', function () {
                            if (this.files && this.files.length > 0) {
                                $('#outputImportCsv').text(this.files[0].name);
                            } else {
                                $('#outputImportCsv').text('Archivo no seleccionado');
                            }
                            const file = this.files?.[0]; if (!file) return;
                            const isXlsx = /\.(xlsx|xlsm)$/i.test(file.name); // [.test] llama a función js para aplicar regex a dicha constante/elemento: 
                            // ej:  ^a{2}-[[a-zA-Z]{4}-[0-9]{2}$    
                            // Espera por ej -> aa-OpKs-92 (distinto de)!-> extraaa-Opks-92chars !-> sd-o9Ks-0a
                            // ^: Declara comienzo de string (sirve para aplicar esta regla a todo el string, si no se cumple la condición dentro, se devuelve false)
                            // a{2}: solo y exclusivamente DOS a (aa)
                            // - (un solo guión)
                            // [[a-zA-Z]{4}: CUATRO caracteres comprendidos de la a a la z o de la A a la z
                            //  - (un solo guión)
                            // [0-9]{2}: DOS numeros comprendidos entre 0 y 9
                            // $: Cierre del string, junto a ^ se especifica la longitud y las reglas requeridas (lo convierte en una comprobación AND, si contiene un caracter de más, se rechaza todo el string)
                            const ok = /\.(csv|xlsx|xlsm)$/i.test(file.name);
                            if (!ok) { alert('Archivo no permitido'); return; }

                            const reader = new FileReader();

                            reader.onload = function (e) {
                                try {
                                    // Si es XLSX: convertir primera hoja a CSV. Si no, usar texto leído.
                                    const text = isXlsx
                                        ? (function () {
                                            const wb = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                                            const ws = wb.Sheets[wb.SheetNames[0]];
                                            return XLSX.utils.sheet_to_csv(ws); // separador ","
                                        })()
                                        : e.target.result;

                                    rows = parseCSV(text); // Devuelve array de caracteres comprendidos dentro de archivo .csv o arreglo de caracteres pre-separados por , en caso de archivo .xlsx/xlsm
                                    const startsLikeHeader = rows[0] && /[F,f]echa/i.test(rows[0][0] || "") && /[M,m]onto/i.test(rows[0][1] || "") && /[T.t]ipo/i.test(rows[0][2] || "") && /[T,t]ipo_[P,p]roducto/i.test(rows[0][3] || "") && /[C,c]antidad_[P,p]roducto/i.test(rows[0][4] || "") && /[D,d]escripci[o,ó]n/i.test(rows[0][5] || "");
                                    $('#csvTable').append(`
                                    <thead>
                                        <tr>
                                            <td>Fecha</td>
                                            <td>Monto</td>
                                            <td>Venta/Compra</td>
                                            <td>Tipo de producto</td>
                                            <td>Cantidad del producto</td>
                                            <td>Comentario</td>
                                        </tr>
                                    </thead>`);
                                    const startsOn = startsLikeHeader ? 1 : 0;
                                    for (let i = startsOn; i < rows.length; i++) {
                                        const [fecha, monto, tipo, dato, dato_cantidad, detalle] = rows[i];
                                        // console.log(`codigo:${code} Nombre:${nombre} NumStock:${stock} YYYY-MM-DD:${toYMD(fecha)}`);
                                        // $('#csvTable').append("<tr><td>" + code + "</td><td>" + nombre + "</td><td>" + stock + "</td><td>" + toYMD(fecha) + "</td></tr>");
                                        $('#csvTable').append(`
                                        <tr>
                                            <td>${toYMD(fecha)}</td>
                                            <td>${monto}</td>
                                            <td>${tipo}</td>
                                            <td>${dato}</td>
                                            <td>${dato_cantidad}</td>
                                            <td>${detalle}</td>
                                        </tr>`);
                                    }
                                } catch (err) {
                                    console.error('Error procesando el archivo:', err);
                                    $('#outputImportCsv').text('Error leyendo el archivo. Ver consola.');
                                    $('#fileImportCsv').val('');
                                }
                            };

                            reader.onerror = function (e) {
                                console.error('Fallo de lectura del archivo:', e?.target?.error || e);
                                $('#outputImportCsv').text('No se pudo leer el archivo. Seleccione otro e intente de nuevo.');
                                $('#fileImportCsv').val('');
                            };

                            if (isXlsx) reader.readAsArrayBuffer(file); else reader.readAsText(file, 'utf-8');
                        });
                        $("#btnImportCsv").click(function (e) {
                            e.preventDefault();
                            const uid = sanitizeNumber($('#uid_n').val());
                            for (let i = 0; i < rows.length; i++) {
                                var [csvFecha, csvMonto, csvTipo, csvDato, csvDato_Cantidad, csvDetalle] = rows[i];
                                csvFecha = toYMD(csvFecha);
                                saveToTable(csvFecha, csvMonto, csvTipo, uid, csvDato, csvDato_Cantidad, csvDetalle);
                                // console.log(csvFecha, csvMonto, csvTipo, csvDato, csvDato_Cantidad, csvDetalle)
                            }
                        });
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