<?php 
require_once('src/scripts/phpqrcode/qrlib.php');
session_start();
header("Content-type: text/html; charset=iso-8859-1");
include_once("database.php");
include_once("clases/class.pacientes.php");
$database = new Database();
$pdoConnection = $database->getConnection();
$situacion = "";
if ($_POST["procesar"] ?? null) {
	$dni = $_POST["dni"];
	$pa = new pacientes($pdoConnection);
	$pa->dni = $dni;
	$paDatos = $pa->selectByDNI($pa);
	if ($paDatos) {
		require_once("phpqrcode/qrlib.php");
		$content = "https://qrcode.test/index.php?accion=mostrar&dni=" . $dni;   //esto es lo que invoca el qr cuando se escanea
        // $content = "https://appgro.zeabur.app/dashboard";
		$archivo = "qr.png";
		// QRCode::png($contenido, $archivo, $ecc,$tamaño,$margen);
		QRcode::png(
			$content        //Contenido
			,
			$archivo       // Nombre del archivo
			,
			QR_ECLEVEL_M   // Indice de corrección de errores
			,
			15              // Tamaño en pixeles de cada cuadro que conforma el QR
			,
			1              // Margen en unidades "Tamaño".
		);
		$situacion = "ok";   //indica que hay paciente y hay código QR
		?>
		<img src="<?php echo $archivo; ?>">
		<?php
	} else {
		//no hay
		$situacion = "no";    //indica que NO existe el paciente y no hay código QR
	}
}

if ($_GET["accion"] ?? null == "mostrar") {
	$situacion = "ok";
	$dni = $_GET["dni"];
	$pa = new pacientes($pdoConnection);
	$pa->dni = $dni;
	$paDatos = $pa->selectByDNI($pa);
	if ($paDatos) {
		$tabla = '<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
						<style>';
		$tabla .= '          @page { size:a4 portrait; margin:20px 20px 10px 20px; 
		                </style>
					</head>
					<body>
						<p align="center">
							<font face="Arial" size="2">
								HISTORIA CLINICA<br>' .
			$dni . '<br>' .
			$paDatos->apellido_nombre . '
							</font>
						</p>
						<hr>
						<p>' . $paDatos->detalle . '
	    				</p>
						<p align="center">
							<i>Actualizado al:' . date("d-m-Y") . '</i>
						</p>
					</body>
				</html>';
		print $tabla;
	}
}

?>