const filtros = {
	desde: document.getElementById('filtro_desde'),
	hasta: document.getElementById('filtro_hasta'),
	grupo: document.getElementById('filtro_agrupado'),
};

const canvasIds = {
	ventas: 'grafico_ventas',
	compras: 'grafico_compras',
	balance: 'grafico_balance',
	ganado_alta: 'grafico_ganado',
	cultivo_alta: 'grafico_cultivos',
};

const charts = {};

// Inicializar todo al cargar
window.addEventListener('load', () => {
	const hoy = new Date().toISOString().slice(0, 10);
	filtros.hasta.value = hoy;
	filtros.desde.value = new Date(hoy.slice(0, 4), 0, 1)
		.toISOString()
		.slice(0, 10); //primer día de año actual

	// Cargar todos los gráficos inicialmente
	Object.keys(canvasIds).forEach((tipo) => {
		cargarGrafico(tipo);
	});

	// Activar tabs
	document.querySelectorAll('.stats-tab-btn').forEach((btn) => {
		btn.addEventListener('click', () => {
			const target = btn.dataset.target;
			activarTab(target);
		});
	});

	// Refiltrar todos los gráficos al cambiar filtros
	[filtros.desde, filtros.hasta, filtros.grupo].forEach((input) => {
		input.addEventListener('change', () => {
			Object.keys(canvasIds).forEach((tipo) => {
				cargarGrafico(tipo);
			});
		});
	});
});

// Cambia de pestaña
function activarTab(target) {
	document
		.querySelectorAll('.stats-tab-btn')
		.forEach((btn) => btn.classList.remove('active'));
	document
		.querySelector(`.stats-tab-btn[data-target="${target}"]`)
		.classList.add('active');

	document
		.querySelectorAll('.stats-chart-container')
		.forEach((div) => div.classList.remove('active'));
	document.getElementById(`tab-${target}`).classList.add('active');
}

// Carga un gráfico específico
function cargarGrafico(tipo) {
	const desde = filtros.desde.value;
	const hasta = filtros.hasta.value;
	const grupo = filtros.grupo.value;

	const canvasId = canvasIds[tipo];
	const ctx = document.getElementById(canvasId).getContext('2d');

	
	const url = `/backend/estadisticas?tipo=${tipo}&desde=${desde}&hasta=${hasta}&grupo=${grupo}`;
	
	fetch(url)
	.then((res) => res.json())
	.then((data) => {
		if (data.error) {
			console.error(`Error (${tipo}):`, data.error);
			return;
		}
		
		const labels = data.map((item) => item.periodo);
		let valores = [];
		const cantidad = labels.length;
		const compactar = cantidad > 12; // Más de 12 → compactar visual
	
		const barThickness = compactar ? 12 : 24;
		const barPercentage = compactar ? 0.5 : 0.9;
		const categoryPercentage = compactar ? 0.6 : 0.9;
		
		if (tipo === 'balance') {
			valores = data.map((item) => item.balance);
		} else {
			const campo = Object.keys(data[0]).find((k) => k !== 'periodo');
			valores = data.map((item) => item[campo]);
			}

			if (charts[tipo]) {
				charts[tipo].destroy();
			}

			charts[tipo] = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: [
						{
							label: tipo.replace('_', ' ').toUpperCase(),
							data: valores,
							backgroundColor: '#3887be',
							barThickness,
							barPercentage,
							categoryPercentage,
						},
					],
				},
				options: {
					responsive: true,
					plugins: {
						legend: { display: true },
						tooltip: { mode: 'index', intersect: false },
					},
					scales: {
						x: {
							ticks: {
								maxRotation: 50,
								minRotation: 0,
								autoSkip: false,
							},
						},
						y: {
							beginAtZero: true,
						},
					},
				},
			});
		})
		.catch((err) => {
			console.error(`Fetch error (${tipo}):`, err);
		});
}
