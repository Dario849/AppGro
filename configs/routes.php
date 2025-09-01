<?php

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
	// $r->addRoute('GET',  '/login',  fn($ROUTE_PARAMS)=> include 'pages/login.php'); //TAMBIEN FUNCIONARIA
	$r->addRoute('GET', '/', function ($ROUTE_PARAMS) {
		include('pages/index.php');
	});
	$r->addRoute('GET', '/about', function ($ROUTE_PARAMS) {
		include('pages/about.php');
	});

	$r->addRoute('GET', '/ipsum', function ($ROUTE_PARAMS) {
		include('pages/ipsum.php');
	});
	$r->addRoute('GET', '/dashboard', function ($ROUTE_PARAMS) {
		include('pages/dashboard.php');
	});
	$r->addRoute('GET', '/tareas', function ($ROUTE_PARAMS) {
		include('pages/tareas.php');
	});
	$r->addRoute('GET', '/calendario', function ($ROUTE_PARAMS) {
		include('pages/calendario.php');
	});
	$r->addRoute('GET', '/tareasdev', function ($ROUTE_PARAMS) {
		include('pages/examples/tareas.html');
	});
	$r->addRoute('GET', '/calendariodev', function ($ROUTE_PARAMS) {
		include('pages/examples/calendario.html');
	});
	$r->addRoute('GET', '/user/logout', function ($ROUTE_PARAMS) {
		include('pages/user/logout.php');
	});
	$r->addRoute('GET', '/demo-cards', function ($ROUTE_PARAMS) {
		include('pages\examples\demo-cards.php');
	});
	$r->addRoute('GET', '/user/recover', function ($ROUTE_PARAMS) {
		include('pages/user/recover.php');
	});
	$r->addRoute('GET', '/user/register', function ($ROUTE_PARAMS) {
		include('pages/user/register.php');
	});
	$r->addRoute('GET', '/user/profile', function ($ROUTE_PARAMS) {
		include('pages/user/profile.php');
	});
	$r->addRoute('GET', '/grupos_ganado', function ($ROUTE_PARAMS) {
		include('pages/grupos_ganado.php');
	});
	$r->addRoute('GET', '/administrador', function ($ROUTE_PARAMS) {
		include('pages/admin/panel.php');
	});
	$r->addRoute('GET', '/ganados', function ($ROUTE_PARAMS) {
		include('pages/ganados.php');
	});
	$r->addRoute('GET', '/ganado', function ($ROUTE_PARAMS) {
		include('pages/ganado.php');
	});
	$r->addRoute('GET', '/cultivos', function ($ROUTE_PARAMS) {
		include('pages/cultivos.php');
	});
	$r->addRoute('GET', '/estadisticasResumen', function ($ROUTE_PARAMS) {
		include('pages/estadisticas/menu.php');
	});
	$r->addRoute('GET', '/estadisticas', function ($ROUTE_PARAMS) {
		include('pages/estadisticas/estadisticasMain.php');
	});
	$r->addRoute('GET', '/test', function ($ROUTE_PARAMS) {
		include('pages/estadisticas/modaltest.html');
	});
	$r->addRoute('GET', '/backend/estadisticas', function ($ROUTE_PARAMS) {
		include('pages/estadisticas/backend/estadisticas.php');
	});
	$r->addRoute('GET', '/backend/resumen', function ($ROUTE_PARAMS) {
		include('pages/estadisticas/backend/resumen.php');
	});
	$r->addRoute('GET', '/getOldBalances', function ($ROUTE_PARAMS) {
		include('system\balances\getOldBalances.php');
	});
	$r->addRoute('GET', '/saveNewBalances', function ($ROUTE_PARAMS) {
		include('system\balances\saveNewBalances.php');
	});
	$r->addRoute('GET', '/404', function ($ROUTE_PARAMS) {
		include('pages/404.php');
	});
	// Rutas de backend (POST)
	$r->addRoute('POST', '/login', function ($ROUTE_PARAMS) {
		require('system/login/Blogin.php');
	});
	$r->addGroup('/user', function (FastRoute\RouteCollector $r) { //Permite agrupar rutas que comparten un prefijo común
		$r->addRoute('POST', '/recover', function ($ROUTE_PARAMS) {
			include('system/login/Brecover.php');
		});
		$r->addRoute('POST', '/register', function ($ROUTE_PARAMS) {
			include('system/login/Bregister.php');
		});
		$r->addRoute('POST', '/profile', function ($ROUTE_PARAMS) {
			include('system/login/Bprofile.php');
		});
	});
	// $r->addRoute('POST', '/user/recover', function ($ROUTE_PARAMS) {
	// 	include('system/login/Brecover.php');
	// });
	// $r->addRoute('POST', '/user/register', function ($ROUTE_PARAMS) {
	// 	include('system/login/Bregister.php');
	// });
	// $r->addRoute('POST', '/user/profile', function ($ROUTE_PARAMS) {
	// 	include('system/login/Bprofile.php');
	// });
	$r->addRoute('POST', '/ganado', function ($ROUTE_PARAMS) {
		include('system/ganados/Bganados.php');
	});
	$r->addRoute('POST', '/BchangePermission', function ($ROUTE_PARAMS) {
		require('system/admin/BchangePermission.php');
	});
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
	case FastRoute\Dispatcher::NOT_FOUND:
		// ... 404 Not Found
		http_response_code(404);
		require __DIR__ . '/../pages/404.php';
		break;
	case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
		$allowedMethods = $routeInfo[1];
		//... 405 Method Not Allowed
		break;
	case FastRoute\Dispatcher::FOUND:
		$routeInfo[1]($routeInfo[2]);
		break;
}
