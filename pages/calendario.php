<?php
require(__DIR__ . '/../../system/main.php');
session_start();
$layout = new HTML(title: 'AppGro - Calendario');
?>

<main class="main__content">
  <style>
    .calendario {
      background-color: #f9f9f9;
      border-radius: 12px;
      margin: auto;
      max-width: 650px;
      width: 100%;
      padding: 15px;
      color: #222;
    }
    .calendario h2 {
      text-align: center;
      margin-bottom: 10px;
      font-size: 30px;
    }
    .calendario .dias-semana,
    .calendario .dias {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 4px;
      text-align: center;
    }
    .calendario .dias-semana div {
      font-weight: bold;
      color: #555;
      font-size: 16px;
    }
    .calendario .dias div {
      padding: 20px 0;
      background-color: #fff;
      border-radius: 6px;
      position: relative;
      font-size: 18px;
      transition: all 0.2s ease;
    }
    .punto {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      position: absolute;
      bottom: 4px;
      left: 50%;
      transform: translateX(-50%);
    }
    .dias .dia-hover:hover {
      transform: scale(1.05);
      background-color: #eef2ff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      z-index: 1;
    }
    .dia-con-tarea {
      box-shadow: 0 0 0 2px #f0c419;
      font-weight: bold;
    }
    .dia-hoy {
      border: 2px solid #5865f2;
      background: #eef2ff;
      font-weight: bold;
    }
    /* Modal */
    #modal-tareas {
      display:none; position:fixed; top:0; left:0; 
      width:100vw; height:100vh;
      background: rgba(0,0,0,0.7); 
      justify-content:center; align-items:center; 
      z-index:1000;
    }
    #modal-tareas .card {
      background:#2c2f33; color:#fff;
      border-radius:8px;
      padding:20px;
      max-width:400px;
      width:90%;
      max-height:80vh;
      overflow-y:auto;
    }
    #modal-tareas button {
      background:#5865f2;
      color:#fff;
      border:none;
      border-radius:4px;
      padding:8px 16px;
      margin-top:15px;
      cursor:pointer;
    }
  </style>

  <div class="calendario">
    <h2>Calendario</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
      <button id="mesAnterior" style="background: none; border: none; font-size: 20px; cursor: pointer;">‹</button>
      <h3 id="titulo-mes" style="text-align:center; margin-bottom: 10px; font-size: 22px;"></h3>
      <button id="mesSiguiente" style="background: none; border: none; font-size: 20px; cursor: pointer;">›</button>
    </div>
    <div class="dias-semana">
      <div>LUN</div><div>MAR</div><div>MIE</div><div>JUE</div><div>VIE</div><div>SAB</div><div>DOM</div>
    </div>
    <div class="dias" id="contenedor-dias"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const nombresMeses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
        "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

      let fechaActual = new Date();
      let tareasPorDia = {};

      const tituloMes = document.getElementById("titulo-mes");
      const contenedorDias = document.getElementById("contenedor-dias");

      function cargarCalendario(fecha) {
        const mes = fecha.getMonth();
        const anio = fecha.getFullYear();

        tituloMes.textContent = `${nombresMeses[mes]} ${anio}`;
        contenedorDias.innerHTML = "";

        const primerDia = new Date(anio, mes, 1).getDay();
        const diasEnMes = new Date(anio, mes + 1, 0).getDate();

        const offset = primerDia === 0 ? 6 : primerDia - 1;
        for (let i = 0; i < offset; i++) {
          contenedorDias.appendChild(document.createElement("div"));
        }

        for (let d = 1; d <= diasEnMes; d++) {
          const dia = document.createElement("div");
          dia.id = `dia-${d}`;
          dia.textContent = d;
          dia.classList.add("dia-hover");

          // Resaltar el día actual
          if (anio === new Date().getFullYear() && mes === new Date().getMonth() && d === new Date().getDate()) {
            dia.classList.add("dia-hoy");
          }

          contenedorDias.appendChild(dia);
        }

        tareasPorDia = {};

        fetch(`/get_tareas.php?mes=${mes + 1}&anio=${anio}&estado=activa`)
          .then(r => r.json())
          .then(tareas => {
            tareas.forEach(t => {
              const fechaTarea = new Date(t.fecha_hora_inicio);
              const diaNum = fechaTarea.getDate();
              const celda = document.getElementById(`dia-${diaNum}`);
              if (celda) {
                celda.classList.add('dia-con-tarea');
                const punto = document.createElement('span');
                punto.classList.add('punto');

                // Colores por estado
                let colorEstado = "#f0c419";
                if (t.estado === "completada") colorEstado = "#43b581";
                if (t.estado === "cancelada") colorEstado = "#f04747";
                punto.style.backgroundColor = colorEstado;

                celda.appendChild(punto);

                if (!tareasPorDia[diaNum]) tareasPorDia[diaNum] = [];
                tareasPorDia[diaNum].push(t);
              }
            });

            Object.keys(tareasPorDia).forEach(diaNum => {
              const celda = document.getElementById(`dia-${diaNum}`);
              if (celda) {
                celda.style.cursor = "pointer";
                celda.onclick = () => mostrarModalTareas(diaNum);
              }
            });
          })
          .catch(console.error);
      }

      function mostrarModalTareas(dia) {
        const modal = document.getElementById('modal-tareas');
        const lista = document.getElementById('modal-lista-tareas');
        const titulo = document.getElementById('modal-titulo');

        titulo.textContent = `Tareas para el día ${dia} de ${nombresMeses[fechaActual.getMonth()]} ${fechaActual.getFullYear()}`;
        lista.innerHTML = '';

        function formatearFecha(fechaStr) {
          const fecha = new Date(fechaStr);
          return fecha.toLocaleDateString('es-AR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
        }

        tareasPorDia[dia].forEach(tarea => {
          const li = document.createElement('li');
          li.style.marginBottom = "12px";
          li.innerHTML = `
            <strong>Estado:</strong> ${tarea.estado}<br>
            📝 <strong>Descripción:</strong> ${tarea.texto || 'Sin descripción'}<br>
            ⏰ <strong>Vence:</strong> ${formatearFecha(tarea.fecha_hora_fin)}
          `;
          lista.appendChild(li);
        });

        modal.style.display = 'flex';
      }

      document.getElementById('modal-cerrar').addEventListener('click', () => {
        document.getElementById('modal-tareas').style.display = 'none';
      });

      document.getElementById("mesAnterior").addEventListener("click", () => {
        fechaActual.setMonth(fechaActual.getMonth() - 1);
        cargarCalendario(fechaActual);
      });

      document.getElementById("mesSiguiente").addEventListener("click", () => {
        fechaActual.setMonth(fechaActual.getMonth() + 1);
        cargarCalendario(fechaActual);
      });

      cargarCalendario(fechaActual);
    });
  </script>

  <!-- modal -->
  <div id="modal-tareas">
    <div class="card">
      <h3 id="modal-titulo">Tareas del día</h3>
      <ul id="modal-lista-tareas" style="list-style:none; padding-left:0;"></ul>
      <button id="modal-cerrar">Cerrar</button>
    </div>
  </div>
</main>