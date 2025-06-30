<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestión de Tareas</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: Arial, sans-serif;
      height: 100vh;
      margin: 0;
      background-color: #1e1e1e;
      color: #eee;
      overflow: auto;
      display: flex;
    }

    /* Barra lateral */
    .sidebar {
      width: 200px;
      background-color: #2c2f33;
      display: flex;
      flex-direction: column;
      padding-top: 20px;
    }
	  
    .sidebar nav a {
      color: #bbb;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
      font-size: 14px;
      transition: background-color 0.2s;
    }
	  
    .sidebar nav a:hover {
      background-color: #40444b;
      color: #fff;
    }
	  
    .sidebar nav a.active {
      background-color: #7289da;
      color: #fff;
    }

    /* Contenedor principal */
    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 20px;
      overflow-y: auto;
    }

    /* Filtros + Ordenamiento */
    .filtros {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }
	  
    .filtros .buscador {
      display: flex;
      align-items: center;
    }
	  
    .filtros .buscador select {
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #5865f2;
      background-color: #2f3136;
      color: #eee;
      font-size: 13px;
      margin-left: 8px;
      cursor: pointer;
    }
	  
    .filtros .buscador select:focus {
      outline: none;
      border-color: #7289da;
    }

    .filtros .orden {
      display: flex;
      gap: 8px;
    }
	  
    .filtros .orden button {
      background-color: #40444b;
      border: none;
      color: #fff;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 12px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
	  
    .filtros .orden button:hover {
      background-color: #5865f2;
    }

    /* Tabla de tareas */
    .tabla-tareas {
      width: 100%;
      border-collapse: collapse;
    }
	  
    .tabla-tareas th,
    .tabla-tareas td {
      border: 1px solid #40444b;
      padding: 10px;
      text-align: center;
      font-size: 14px;
      color: #eee;
    }
	  
    .tabla-tareas th {
      background-color: #2f3136;
      font-weight: 400;
    }
	  
    .tabla-tareas td input,
    .tabla-tareas td select {
      width: 100%;
      padding: 6px;
      border: 1px solid #5865f2;
      border-radius: 4px;
      background-color: #2f3136;
      color: #eee;
      font-size: 13px;
    }
	  
    .tabla-tareas td .status-circle {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: inline-block;
      cursor: pointer;
      border: 2px solid #ccc;
    }
	  
    /* Colores de estado */
    .status-circle[data-estado="activa"] {
      background-color: #f0c419;
    }
	  
    .status-circle[data-estado="completada"] {
      background-color: #43b581;
    }
	  
    .status-circle[data-estado="cancelada"] {
      background-color: #f04747;
    }
	  
    /* Botón agregar fila */
    .btn-agregar {
      margin-top: 8px;
      padding: 8px 12px;
      background-color: #5865f2;
      border: none;
      border-radius: 4px;
      color: #fff;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
	  
    .btn-agregar:hover {
      background-color: #4e5ac1;
    }

    .footer {
      margin-top: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
    }
	  
    .leyenda-estados {
      display: flex;
      align-items: center;
      gap: 16px;
    }
	  
    .leyenda-estados .item-leyenda {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
    }
	  
    .leyenda-estados .item-leyenda .circulo {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
    }
	  
    .circulo.activa {
      background-color: #f0c419;
    }
	  
    .circulo.completada {
      background-color: #43b581;
    }
	  
    .circulo.cancelada {
      background-color: #f04747;
    }

    .admin-controls {
      background-color: #2f3136;
      padding: 12px 16px;
      border-radius: 6px;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
	  
    .admin-controls select,
    .admin-controls button {
      padding: 6px 10px;
      border: 1px solid #5865f2;
      border-radius: 4px;
      background-color: #2f3136;
      color: #eee;
      font-size: 13px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
	  
    .admin-controls button:hover,
    .admin-controls select:hover {
      background-color: #4e5ac1;
    }

    .modo-selector {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 12px;
      gap: 10px;
    }

    .modo-btn {
      padding: 8px 16px;
      border: 1px solid #ccc;
      background-color: #2c2f33;
      color: #eee;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      transition: background-color 0.2s;
    }

    .modo-btn.activo {
      background-color: #2ecc71;
      color: #fff;
    }

    .modo-btn:hover {
      background-color: #3d3d3d;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <nav>
      <a href="tareas.php" class="active">Tareas</a>
      <a href="calendario.php">Calendario</a>
      <a href="#">Mapa</a>
    </nav>
  </div>

  <div class="main-content">
    <div class="modo-selector">
        <button class="modo-btn activo">TAREAS</button>
        <button class="modo-btn" onclick="window.location.href='calendario.php'">CALENDARIO</button>
    </div>

    <div class="filtros">
      <div class="buscador">
        <label for="filtro">FILTRAR POR ESTADO:</label>
        <select id="filtro">
          <option value="activa">Activa</option>
          <option value="completada">Completada</option>
          <option value="cancelada">Cancelada</option>
          <option value="todas">Todas</option>
        </select>
      </div>
      <div class="orden">
        <button id="btnVencimiento" data-orden="asc">
          VENCIMIENTO <span id="flechaOrden">↓</span>
        </button>
      </div>
    </div>

    <table class="tabla-tareas" id="tablaTareas">
      <thead>
        <tr>
          <th></th>
          <th>ESTADO</th>
          <th>INICIO</th>
          <th>VENCIMIENTO</th>
          <th>DESCRIPCIÓN</th>
        </tr>
      </thead>
      <tbody>
  
      </tbody>
    </table>

    <button class="btn-agregar" id="btnAgregar">＋ Agregar Tarea</button>
    <div id="modalAgregarTarea" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:1000;">
      <div style="background-color:#2c2f33; padding:20px; border-radius:8px; color:white; width:300px;">
        <h3>Agregar Nueva Tarea</h3>
        <label>Inicio:</label>
        <input type="date" id="inputInicio" style="width:100%; margin-bottom:10px;"><br>
        <label>Vencimiento:</label>
        <input type="date" id="inputVencimiento" style="width:100%; margin-bottom:10px;"><br>
        <label>Descripción:</label>
        <input type="text" id="inputDescripcion" style="width:100%; margin-bottom:20px;"><br>
        <button id="btnAceptarTarea" style="background:#2ecc71; color:#fff; padding:8px 16px; border:none; border-radius:4px; margin-right:10px;">Aceptar</button>
        <button id="btnCancelarTarea" style="background:#f04747; color:#fff; padding:8px 16px; border:none; border-radius:4px;">Cancelar</button>
      </div>
    </div>

    <div class="footer">
      <div class="leyenda-estados">
        <div class="item-leyenda">
          <span class="circulo activa"></span>
          <span>activa</span>
        </div>
        <div class="item-leyenda">
          <span class="circulo completada"></span>
          <span>completada</span>
        </div>
        <div class="item-leyenda">
          <span class="circulo cancelada"></span>
          <span>cancelada</span>
        </div>
      </div>

      <div class="admin-controls">
        <label for="auto-eliminar">ELIMINACIÓN AUTOMÁTICA:</label>
        <select id="auto-eliminar">
          <option value="ninguno">NINGUNO</option>
          <option value="semanal">SEMANAL</option>
          <option value="mensual">MENSUAL</option>
          <option value="anual">ANUAL</option>
        </select>
        <button id="btnEliminar">ELIMINAR</button>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const tabla = document.querySelector("#tablaTareas tbody");
    const btnAgregar = document.getElementById("btnAgregar");
    const filtro = document.getElementById("filtro");
    const modal = document.getElementById("modalAgregarTarea");
    const btnAceptarTarea = document.getElementById("btnAceptarTarea");
    const btnCancelarTarea = document.getElementById("btnCancelarTarea");
    const btnVencimiento = document.getElementById("btnVencimiento");
    const flecha = document.getElementById("flechaOrden");
    const tr = document.createElement("tr");
    
    // Por defecto, empieza en orden ascendente
    let ordenAscendente = true;

    btnVencimiento.addEventListener("click", function () {
      const orden = ordenAscendente ? "asc" : "desc";

      flecha.textContent = ordenAscendente ? "↓" : "↑";

      fetch(`get_tareas.php?estado=activa&orden=vencimiento&direccion=${orden}`)
        .then(response => response.json())
        .then(data => {
          tabla.innerHTML = "";

          data.forEach(t => {
            const tr = document.createElement("tr");

            const tdIcon = document.createElement("td");
            const span = document.createElement("span");
            span.className = "status-circle";
            span.setAttribute("data-estado", t.estado);
            span.title = "Marcar como completada";
            span.style.pointerEvents = "none";
            tdIcon.appendChild(span);
            tr.appendChild(tdIcon);

            tr.setAttribute("data-id", t.id);

            const tdEstado = document.createElement("td");
            const select = document.createElement("select");
            ["activa", "completada", "cancelada"].forEach(opt => {
              const o = document.createElement("option");
              o.value = opt;
              o.textContent = opt;
              if (opt === t.estado) o.selected = true;
              select.appendChild(o);
            });
            tdEstado.appendChild(select);
            tr.appendChild(tdEstado);

            const tdInicio = document.createElement("td");
            const inputInicio = document.createElement("input");
            inputInicio.type = "date";
            inputInicio.value = t.fecha_hora_inicio.split(" ")[0];
            inputInicio.onchange = () => onInputChange(inputInicio, t.id, 'fecha_hora_inicio');
            tdInicio.appendChild(inputInicio);
            tr.appendChild(tdInicio);

            const tdVenc = document.createElement("td");
            const inputVenc = document.createElement("input");
            inputVenc.type = "date";
            inputVenc.onchange = () => onInputChange(inputVenc, t.id, 'fecha_hora_fin');
            if (t.fecha_hora_fin) inputVenc.value = t.fecha_hora_fin.split(" ")[0];
            tdVenc.appendChild(inputVenc);
            tr.appendChild(tdVenc);

            const tdDesc = document.createElement("td");
            const inputDesc = document.createElement("input");
            inputDesc.type = "text";
            inputDesc.onchange = () => onInputChange(inputDesc, t.id, 'texto');
            inputDesc.value = t.texto;
            tdDesc.appendChild(inputDesc);
            tr.appendChild(tdDesc);

            tabla.appendChild(tr);
          });

          // Alternar el orden para la próxima vez
          ordenAscendente = !ordenAscendente;
        })
        .catch(console.error);
    });

    // Cargar tareas del backend
    fetch('get_tareas.php?estado=activa')
      .then(response => response.json())
      .then(data => {
        data.forEach(t => {
          const tr = document.createElement("tr");

          // Icono estado
          const tdIcon = document.createElement("td");
          const span = document.createElement("span");
          span.className = "status-circle";
          span.setAttribute("data-estado", t.estado);
          span.title = "Marcar como completada";
          span.style.pointerEvents = "none";
          tdIcon.appendChild(span);
          tr.appendChild(tdIcon);

          tr.setAttribute("data-id", t.id);

          // Estado select
          const tdEstado = document.createElement("td");
          const select = document.createElement("select");
          ["activa", "completada", "cancelada"].forEach(opt => {
            const o = document.createElement("option");
            o.value = opt;
            o.textContent = opt;
            if (opt === t.estado) o.selected = true;
            select.appendChild(o);
          });
          select.onchange = () => onInputChange(select, t.id, 'estado');
          tdEstado.appendChild(select);
          tr.appendChild(tdEstado);

          // Inicio
          const tdInicio = document.createElement("td");
          const inputInicio = document.createElement("input");
          inputInicio.type = "date";
          inputInicio.value = t.fecha_hora_inicio.split(" ")[0];
          inputInicio.onchange = () => onInputChange(inputInicio, t.id, 'fecha_hora_inicio');
          tdInicio.appendChild(inputInicio);
          tr.appendChild(tdInicio);

          // Vencimiento (lo agregás en el backend si tenés)
          const tdVenc = document.createElement("td");
          const inputVenc = document.createElement("input");
          inputVenc.type = "date";
          inputVenc.onchange = () => onInputChange(inputVenc, t.id, 'fecha_hora_fin');
          if (t.fecha_hora_fin) inputVenc.value = t.fecha_hora_fin.split(" ")[0];
          tdVenc.appendChild(inputVenc);
          tr.appendChild(tdVenc);

          // Descripción
          const tdDesc = document.createElement("td");
          const inputDesc = document.createElement("input");
          inputDesc.type = "text";
          inputDesc.value = t.texto;
          inputDesc.onchange = () => onInputChange(inputDesc, t.id, 'texto');
          tdDesc.appendChild(inputDesc);
          tr.appendChild(tdDesc);

          tabla.appendChild(tr);
        });
      })
      .catch(console.error);

    function onClickEliminarFila(e) {
      const span = e.currentTarget;
      const fila = span.closest("tr");
      span.setAttribute("data-estado", "completada");
      fila.style.transition = "opacity 0.5s";
      fila.style.opacity = "0";
      setTimeout(() => fila.remove(), 500);
    }

    btnAgregar.addEventListener("click", function () {
      modal.style.display = "flex"; // solo muestra el modal
    });

    btnAceptarTarea.addEventListener("click", function () {
      const inicio = document.getElementById("inputInicio").value;
      const vencimiento = document.getElementById("inputVencimiento").value;
      const descripcion = document.getElementById("inputDescripcion").value;

      if (!inicio || !vencimiento || !descripcion) {
        alert("Todos los campos son obligatorios");
        return;
      }

      const nuevaTarea = {
        estado: "activa",
        fecha_hora_inicio: inicio,
        fecha_hora_fin: vencimiento,
        texto: descripcion
      };

      fetch('guardar_tarea.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(nuevaTarea)
      })
      .then(response => response.json())
      .then(data => {
        console.log("Respuesta del servidor:", data);
        if (data.success) {
          modal.style.display = "none";
          document.getElementById("inputInicio").value = "";
          document.getElementById("inputVencimiento").value = "";
          document.getElementById("inputDescripcion").value = "";

          filtro.value = "activa";
          filtro.dispatchEvent(new Event("change"));
        } else {
          alert("Error al guardar la tarea");
        }
      })
      .catch(err => {
        console.error(err);
        alert("Error al conectar con el servidor");
      });
    });

    btnCancelarTarea.addEventListener("click", function () {
      modal.style.display = "none"; // Ocultar
    });

    filtro.addEventListener("change", function () {
      const estado = filtro.value;
      let url = "get_tareas.php";
      if (estado !== "todas") {
        url += `?estado=${estado}`;
      }

      fetch(url)
        .then(response => response.json())
        .then(data => {
          tabla.innerHTML = "";

          data.forEach(t => {
            const tr = document.createElement("tr");

            const tdIcon = document.createElement("td");
            const span = document.createElement("span");
            span.className = "status-circle";
            span.setAttribute("data-estado", t.estado);
            span.title = "Marcar como completada";
            span.style.pointerEvents = "none";
            tdIcon.appendChild(span);
            tr.appendChild(tdIcon);

            tr.setAttribute("data-id", t.id);

            const tdEstado = document.createElement("td");
            const select = document.createElement("select");
            ["activa", "completada", "cancelada"].forEach(opt => {
              const o = document.createElement("option");
              o.value = opt;
              o.textContent = opt;
              if (opt === t.estado) o.selected = true;
              select.appendChild(o);
            });
            select.onchange = () => onInputChange(select, t.id, 'estado');
            tdEstado.appendChild(select);
            tr.appendChild(tdEstado);

            const tdInicio = document.createElement("td");
            const inputInicio = document.createElement("input");
            inputInicio.type = "date";
            inputInicio.value = t.fecha_hora_inicio.split(" ")[0];
            inputInicio.onchange = () => onInputChange(inputInicio, t.id, 'fecha_hora_inicio');
            tdInicio.appendChild(inputInicio);
            tr.appendChild(tdInicio);

            const tdVenc = document.createElement("td");
            const inputVenc = document.createElement("input");
            inputVenc.type = "date";
            inputVenc.onchange = () => onInputChange(inputVenc, t.id, 'fecha_hora_fin');
            if (t.fecha_hora_fin) inputVenc.value = t.fecha_hora_fin.split(" ")[0];
            tdVenc.appendChild(inputVenc);
            tr.appendChild(tdVenc);

            const tdDesc = document.createElement("td");
            const inputDesc = document.createElement("input");
            inputDesc.type = "text";
            inputDesc.value = t.texto;
            inputDesc.onchange = () => onInputChange(inputDesc, t.id, 'texto');
            tdDesc.appendChild(inputDesc);
            tr.appendChild(tdDesc);

            tabla.appendChild(tr);
          });
        })
        .catch(console.error);
    });

    function crearFilaVacia() {
      const tr = document.createElement("tr");
      return tr;
    }
  });

  let debounceTimers = {};

  function onInputChange(input, id, campo) {
    clearTimeout(debounceTimers[id + campo]);

    debounceTimers[id + campo] = setTimeout(() => {
      const valor = input.value;

      fetch('actualizar_tarea.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, campo, valor })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          input.style.border = '1px solid red';
          console.error(data.error);
        } else {
          input.style.border = '';
            
            if (campo === 'estado') {
            const fila = document.querySelector(`tr[data-id='${id}']`);
            if (fila) {
              const circulo = fila.querySelector('.status-circle');
              if (circulo) {
                circulo.setAttribute('data-estado', valor);
                circulo.title = `Estado: ${valor}`;
              }
            }
          }
        }
      })
      .catch(err => {
        input.style.border = '1px solid red';
        console.error(err);
      });

    }, 600);
  }

  document.getElementById("btnEliminar").addEventListener("click", () => {
    const criterio = document.getElementById("auto-eliminar").value;

    if (criterio === "ninguno") {
      alert("Por favor, selecciona un criterio válido para eliminar.");
      return;
    }

    // Confirmación antes de eliminar
    if (!confirm(`¿Seguro que querés eliminar tareas ${criterio}? Sólo se eliminaran tareas completadas o canceladas.`)) {
      return;
    }

    fetch("eliminar_tareas.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `criterio=${encodeURIComponent(criterio)}`
    })
    .then(response => response.text())
    .then(html => {
      alert(html.replace(/<\/?[^>]+(>|$)/g, ""));
      window.location.reload();
      document.getElementById("filtro").dispatchEvent(new Event("change"));
    })
    .catch(err => {
      console.error(err);
      alert("Error al eliminar tareas");
    });
  });
  </script>
</body>
</html>
