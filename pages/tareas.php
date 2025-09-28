<?php
// Asegurate que la ruta a system/main.php sea correcta.
// Si pages está en la raíz del proyecto y system está al mismo nivel que pages:
// require(__DIR__ . '/../system/main.php');
// Si require('system/main.php') te funcionó antes, podés usar esa línea.
require(__DIR__ . '/../system/main.php');
session_start();
$layout = new HTML(title: 'AppGro - Tareas');
?>

<main class="main__content">
  <style>
    /* --- Estilos de la tabla --- */
    .tabla-container {
      max-height: 500px;      /* altura máxima visible */
      overflow-y: auto;       /* scroll vertical */
      border: 1px solid #40444b;
      border-radius: 6px;
      margin-bottom: 12px;
    }
    .tabla-tareas { width:100%; border-collapse:collapse; }
    .tabla-tareas th, .tabla-tareas td {
      border:1px solid #40444b;
      padding:8px;
      text-align:center;
      background:#2f3136;
      color:#eee;
    }
    .tabla-tareas td input,
    .tabla-tareas td select {
      width:100%;
      padding:6px;
      border-radius:4px;
      background:#1f2124;
      color:#eee;
      border:1px solid #5865f2;
    }
    .status-circle {
      width:16px; height:16px; border-radius:50%;
      display:inline-block; border:2px solid #ccc;
    }
    .status-circle[data-estado="activa"]     { background:#f0c419; }
    .status-circle[data-estado="completada"] { background:#43b581; }
    .status-circle[data-estado="cancelada"]  { background:#f04747; }

    /* Botones */
    .btn-agregar {
      margin:10px 0;
      padding:8px 12px;
      background:#5865f2;
      color:#fff;
      border:none;
      border-radius:4px;
      cursor:pointer;
    }
    .admin-controls {
      display:flex; gap:10px; align-items:center; margin-top:12px;
    }
    .admin-controls select, .admin-controls button {
      padding:6px 10px;
      border-radius:4px;
      background:#2f3136;
      color:#eee;
      border:1px solid #5865f2;
      cursor:pointer;
    }

    /* Modal */
    #modalAgregarTarea {
      display:none;
      position:fixed; inset:0;
      background:rgba(0,0,0,0.7);
      justify-content:center; align-items:center;
      z-index:1000;
    }
    #modalAgregarTarea .card {
      background:#2c2f33; color:#fff;
      padding:18px;
      border-radius:8px;
      width:320px;
    }
  </style>

   <!-- Leyenda y controles -->
  <div style="margin-top:14px;">
    <div style="display:flex; gap:12px; align-items:center;">
      <div><span style="display:inline-block;width:12px;height:12px;background:#f0c419;border-radius:50%;"></span> activa</div>
      <div><span style="display:inline-block;width:12px;height:12px;background:#43b581;border-radius:50%;"></span> completada</div>
      <div><span style="display:inline-block;width:12px;height:12px;background:#f04747;border-radius:50%;"></span> cancelada</div>
    </div>

    <div id="mensaje-eliminar" style="display:none; padding:8px; margin-bottom:10px; border-radius:4px; color:#fff;"></div>

    <div class="admin-controls">
      <label for="criterioEliminar">ELIMINACIÓN SEMANAL - MENSUAL - ANUAL:</label>
      <select id="criterioEliminar">
        <option value="semanal">SEMANAL</option>
        <option value="mensual">MENSUAL</option>
        <option value="anual">ANUAL</option>
      </select>
      <button id="btnEliminarTareas">ELIMINAR</button>
    </div>
  </div>

  <button id="btnAgregar" class="btn-agregar">＋ Agregar Tarea</button>

  <!-- Filtros y orden -->
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <div>
      <label for="filtro">Filtrar:</label>
      <select id="filtro">
        <option value="activa">Activa</option>
        <option value="completada">Completada</option>
        <option value="cancelada">Cancelada</option>
        <option value="todas">Todas</option>
      </select>
    </div>
    <div>
      <button id="btnVencimiento" class="btn-agregar">
        Ordenar por vencimiento <span id="flechaOrden">↓</span>
      </button>
    </div>
  </div>

  <!-- Caja scrolleable con la tabla -->
  <div class="tabla-container">
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
      <tbody></tbody>
    </table>
  </div>

  <!-- Modal -->
  <div id="modalAgregarTarea">
    <div class="card">
      <h3>Agregar Nueva Tarea</h3>
      <label>Inicio:</label>
      <input type="date" id="inputInicio" style="width:100%; margin-bottom:10px;"><br>
      <label>Vencimiento:</label>
      <input type="date" id="inputVencimiento" style="width:100%; margin-bottom:10px;"><br>
      <label>Descripción:</label>
      <input type="text" id="inputDescripcion" style="width:100%; margin-bottom:10px;"><br>
      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button id="btnAceptarTarea" style="background:#2ecc71; color:#fff; padding:8px 12px; border:none; border-radius:4px;">Aceptar</button>
        <button id="btnCancelarTarea" style="background:#f04747; color:#fff; padding:8px 12px; border:none; border-radius:4px;">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    // ---- Variables globales ----
    let debounceTimers = {};
    let ordenAscendente = true;

    // ---- Cargar tareas ----
    function cargarTareas(estado = 'activa') {
      const url = estado === 'todas' ? 'get_tareas' : `get_tareas?estado=${estado}`;
      fetch(url)
        .then(r => r.json())
        .then(data => {
          const tabla = document.querySelector('#tablaTareas tbody');
          tabla.innerHTML = '';
          data.forEach(t => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', t.id);

            const tdIcon = document.createElement('td');
            const span = document.createElement('span');
            span.className = 'status-circle';
            span.setAttribute('data-estado', t.estado);
            span.title = `Estado: ${t.estado}`;
            tdIcon.appendChild(span);
            tr.appendChild(tdIcon);

            const tdEstado = document.createElement('td');
            const select = document.createElement('select');
            ['activa','completada','cancelada'].forEach(opt => {
              const o = document.createElement('option');
              o.value = opt;
              o.textContent = opt;
              if (opt === t.estado) o.selected = true;
              select.appendChild(o);
            });
            select.onchange = () => onInputChange(select, t.id, 'estado');
            tdEstado.appendChild(select);
            tr.appendChild(tdEstado);

            const tdInicio = document.createElement('td');
            const inputInicio = document.createElement('input');
            inputInicio.type = 'date';
            if (t.fecha_hora_inicio) inputInicio.value = t.fecha_hora_inicio.split(' ')[0];
            inputInicio.onchange = () => onInputChange(inputInicio, t.id, 'fecha_hora_inicio');
            tdInicio.appendChild(inputInicio);
            tr.appendChild(tdInicio);

            const tdVenc = document.createElement('td');
            const inputVenc = document.createElement('input');
            inputVenc.type = 'date';
            if (t.fecha_hora_fin) inputVenc.value = t.fecha_hora_fin.split(' ')[0];
            inputVenc.onchange = () => onInputChange(inputVenc, t.id, 'fecha_hora_fin');
            tdVenc.appendChild(inputVenc);
            tr.appendChild(tdVenc);

            const tdDesc = document.createElement('td');
            const inputDesc = document.createElement('input');
            inputDesc.type = 'text';
            inputDesc.value = t.texto || '';
            inputDesc.onchange = () => onInputChange(inputDesc, t.id, 'texto');
            tdDesc.appendChild(inputDesc);
            tr.appendChild(tdDesc);

            tabla.appendChild(tr);
          });
        })
        .catch(err => console.error('Error cargando tareas:', err));
    }

    // ---- debounce + update backend ----
    function onInputChange(input, id, campo) {
      clearTimeout(debounceTimers[id + campo]);
      debounceTimers[id + campo] = setTimeout(() => {
        const valor = input.value;
        fetch('actualizar_tareas', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, campo, valor })
        })
        .then(r => r.json())
        .then(resp => {
          if (!resp.success) {
            input.style.border = '1px solid red';
            console.error(resp.error);
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

    // ---- botones / modal ----
    document.addEventListener('DOMContentLoaded', () => {
      const filtro = document.getElementById('filtro');
      const btnAgregar = document.getElementById('btnAgregar');
      const modal = document.getElementById('modalAgregarTarea');
      const btnAceptarTarea = document.getElementById('btnAceptarTarea');
      const btnCancelarTarea = document.getElementById('btnCancelarTarea');
      const btnVencimiento = document.getElementById('btnVencimiento');
      const flecha = document.getElementById('flechaOrden');
      const btnEliminar = document.getElementById('btnEliminarTareas');
      const criterioSelect = document.getElementById('criterioEliminar');
      const mensajeEliminar = document.getElementById('mensaje-eliminar');

      btnEliminar.addEventListener('click', () => {
        const criterio = criterioSelect.value;
        if (!criterio) return;

        fetch('eliminar_tareas', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ criterio })
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            mostrarMensaje(`Se eliminaron ${data.eliminadas} tareas (${criterio}).`, true);
            cargarTareas(document.getElementById('filtro').value);
          } else {
            mostrarMensaje("Error: " + data.error, false);
          }
        })
        .catch(err => {
          console.error(err);
          mostrarMensaje("Error de conexión al eliminar tareas.", false);
        });
      });

      // Función para mostrar mensaje dinámico
      function mostrarMensaje(texto, exito) {
        mensajeEliminar.textContent = texto;
        mensajeEliminar.style.background = exito ? "#2ecc71" : "#e74c3c"; // verde o rojo
        mensajeEliminar.style.display = "block";

        setTimeout(() => {
          mensajeEliminar.style.display = "none";
        }, 3000);
      }

      filtro.addEventListener('change', () => cargarTareas(filtro.value));

      btnVencimiento.addEventListener('click', () => {
        const orden = ordenAscendente ? 'asc' : 'desc';
        flecha.textContent = ordenAscendente ? '↓' : '↑';
        fetch(`./get_tareas?estado=activa&orden=vencimiento&direccion=${orden}`)
          .then(r => r.json())
          .then(data => {
            // reutilizamos la carga manual para no mezclar filtros
            const tabla = document.querySelector('#tablaTareas tbody');
            tabla.innerHTML = '';
            data.forEach(t => {
              // (mismo render que en cargarTareas)
              const tr = document.createElement('tr');
              tr.setAttribute('data-id', t.id);
              const tdIcon = document.createElement('td');
              const span = document.createElement('span');
              span.className = 'status-circle';
              span.setAttribute('data-estado', t.estado);
              tdIcon.appendChild(span);
              tr.appendChild(tdIcon);
              // estado
              const tdEstado = document.createElement('td');
              const select = document.createElement('select');
              ['activa','completada','cancelada'].forEach(opt => {
                const o = document.createElement('option'); o.value = opt; o.textContent = opt;
                if (opt === t.estado) o.selected = true;
                select.appendChild(o);
              });
              select.onchange = () => onInputChange(select, t.id, 'estado');
              tdEstado.appendChild(select); tr.appendChild(tdEstado);
              // inicio
              const tdInicio = document.createElement('td');
              const inputInicio = document.createElement('input'); inputInicio.type = 'date';
              if (t.fecha_hora_inicio) inputInicio.value = t.fecha_hora_inicio.split(' ')[0];
              inputInicio.onchange = () => onInputChange(inputInicio, t.id, 'fecha_hora_inicio');
              tdInicio.appendChild(inputInicio); tr.appendChild(tdInicio);
              // venc
              const tdVenc = document.createElement('td');
              const inputVenc = document.createElement('input'); inputVenc.type = 'date';
              if (t.fecha_hora_fin) inputVenc.value = t.fecha_hora_fin.split(' ')[0];
              inputVenc.onchange = () => onInputChange(inputVenc, t.id, 'fecha_hora_fin');
              tdVenc.appendChild(inputVenc); tr.appendChild(tdVenc);
              // texto
              const tdDesc = document.createElement('td');
              const inputDesc = document.createElement('input'); inputDesc.type = 'text';
              inputDesc.value = t.texto || '';
              inputDesc.onchange = () => onInputChange(inputDesc, t.id, 'texto');
              tdDesc.appendChild(inputDesc); tr.appendChild(tdDesc);

              tabla.appendChild(tr);
            });

            ordenAscendente = !ordenAscendente;
          })
          .catch(console.error);
      });

      btnAgregar.addEventListener('click', () => modal.style.display = 'flex');
      btnCancelarTarea.addEventListener('click', () => modal.style.display = 'none');

      btnAceptarTarea.addEventListener('click', () => {
        const inicio = document.getElementById('inputInicio').value;
        const venc = document.getElementById('inputVencimiento').value;
        const desc = document.getElementById('inputDescripcion').value;
        if (!inicio || !venc || !desc) { alert('Completa todos los campos'); return; }
        fetch('/guardar_tarea', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ estado: 'activa', fecha_hora_inicio: inicio, fecha_hora_fin: venc, texto: desc })
        })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            modal.style.display = 'none';
            document.getElementById('inputInicio').value = '';
            document.getElementById('inputVencimiento').value = '';
            document.getElementById('inputDescripcion').value = '';
            document.getElementById('filtro').value = 'activa';
            cargarTareas('activa');
          } else alert('Error al guardar');
        })
        .catch(err => { console.error(err); alert('Error de conexión'); });
      });

      // carga inicial
      cargarTareas(filtro.value);
    });

    // ---- validar y enviar baja lógica por form ----
    function validarEliminar(form) {
      const criterio = form.querySelector('[name="criterio"]').value;
      if (criterio === 'ninguno') {
        alert('Seleccioná un criterio para eliminar tareas.');
        return false;
      }
      // si querés confirmar:
      return confirm('¿Confirmás dar de baja (baja lógica) las tareas completadas/canceladas según el criterio?');
    }
  </script>
</main>