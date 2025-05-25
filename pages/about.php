<?php
require('system/main.php');
renderNavbar();
$layout = new HTML(title: 'PHP via Vite');
?>
<main class="main__content">

	<div class="flex flex-col items-center gap-10 text-2xl">
		<?php include('partials/nav.php'); ?>
		
		<div class="w-full max-w-lg text-base">
			Este es un menú plantilla para visualizar información detallada sobre el funcionamiento de la página AppGro<br />
			<br />
			Contiene las siguientes funcionalidades:
			<ul class="list-disc pl-10">
				<li>Menú de pre-visualización de tareas, mapa, clima</li>
				<li>Creación y manejo de tareas diarias/mensuales</li>
				<li>Registro de ganados, creación, actualización</li>
				<li>Registro de gastos, ganancias</li>
				<li>Registro de cultivos</li>
				<li>Registro de herramientas</li>
			</ul>
		</div>
	</div>
	
</main>