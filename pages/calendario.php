<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Calendario</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: Arial, sans-serif;
      display: flex;
      background-color: #1e1e1e;
      color: #eee;
      height: 100vh;
      overflow: hidden;
    }

    .sidebar {
      width: 200px;
      background-color: #2c2f33;
      padding-top: 20px;
      display: flex;
      flex-direction: column;
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

    .main-content {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .modo-selector {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 16px;
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
    }

    .modo-btn.activo {
      background-color: #2ecc71;
      color: #fff;
    }

    .calendario {
      background-color: #f9f9f9;
      border-radius: 12px;
      margin: auto;
      width: 650px;
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
    }

    .punto {
      width: 6px;
      height: 6px;
      background-color: #444;
      border-radius: 50%;
      position: absolute;
      bottom: 4px;
      left: 50%;
      transform: translateX(-50%);
    }

    .dias .dia-hover:hover {
      transform: scale(1.2);
      background-color: #ddd;
      transition: transform 0.2s ease;
      z-index: 1;
    }

    .dia-con-tarea {
      box-shadow: 0 0 0 2px #f0c419;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <nav>
      <a href="tareas.php">Tareas</a>
      <a href="calendario.php" class="active">Calendario</a>
      <a href="#">Mapa</a>
    </nav>
  </div>

  <div class="main-content">
    <div class="modo-selector">
      <button class="modo-btn" onclick="window.location.href='tareas.php'">TAREAS</button>
      <button class="modo-btn activo">CALENDARIO</button>
    </div>

    <div class="calendario">
      <h2>Calendario</h2>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <button id="mesAnterior" style="background: none; border: none; font-size: 20px; cursor: pointer;">←</button>
        <h3 id="titulo-mes" style="text-align:center; margin-bottom: 10px; font-size: 22px;"></h3>
        <button id="mesSiguiente" style="background: none; border: none; font-size: 20px; cursor: pointer;">→</button>
      </div>
      <div class="dias-semana">
        <div>LUN</div><div>MAR</div><div>MIE</div><div>JUE</div><div>VIE</div><div>SAB</div><div>DOM</div>
      </div>
        <div class="dias" id="contenedor-dias">
        </div>
      </div>
    </div>
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
        const vacio = document.createElement("div");
        contenedorDias.appendChild(vacio);
      }

      for (let d = 1; d <= diasEnMes; d++) {
        const dia = document.createElement("div");
        dia.id = `dia-${d}`;
        dia.textContent = d;
        dia.classList.add("dia-hover");
        contenedorDias.appendChild(dia);
      }

      tareasPorDia = {};

      fetch(`get_tareas.php?mes=${mes + 1}&anio=${anio}&estado=activa`)
        .then(r => r.json())
        .then(tareas => {
          tareas.forEach(t => {
            const fechaTarea = new Date(t.fecha_hora_inicio);
            const dia = fechaTarea.getDate();
            const celda = document.getElementById(`dia-${dia}`);
            if (celda) {
              celda.classList.add('dia-con-tarea');
              const punto = document.createElement('span');
              punto.classList.add('punto');
              punto.style.backgroundColor = '#f0c419';
              celda.appendChild(punto);

              if (!tareasPorDia[dia]) tareasPorDia[dia] = [];
              tareasPorDia[dia].push(t);
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
          🟡 <strong>Estado:</strong> ${tarea.estado}<br>
          📝 <strong>Descripción:</strong> ${tarea.texto|| 'Sin descripción'}<br>
          ⏰ <strong>La tarea vence:</strong> ${formatearFecha(tarea.fecha_hora_fin)}
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
  <div id="modal-tareas" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; 
  background: rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:1000;">
    <div style="background:#fff; color:#222; border-radius:10px; padding:20px; max-width:400px; width:90%; max-height:80vh; overflow-y:auto;">
      <h3 id="modal-titulo">Tareas del día</h3>
      <ul id="modal-lista-tareas" style="list-style:none; padding-left:0;"></ul>
      <button id="modal-cerrar" style="margin-top:15px; padding:8px 16px; cursor:pointer;">Cerrar</button>
    </div>
  </div>
</body>
</html>
