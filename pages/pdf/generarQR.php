<?php
// submit genera un QR 
require('system/main.php');
session_start();
$layout = new HTML(title: 'AppGro-QR');

if ($_POST["procesar"]) {
    $content = "https://appgro.zeabur.app/generarQR?accion=mostrar";   //esto es lo que invoca el qr cuando se escanea
    $archivo = "qr.png";
    // QRCode::png($contenido, $archivo, $ecc,$tamaño,$margen);
    require_once("src/scripts/phpqrcode/qrlib.php");
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
    ?>
    <img src="<?php echo $archivo; ?>">
    <?php
}

if ($_GET["accion"] == "mostrar") {
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
							Este es el contenido a mostrar<br>
						</font>
					</p>
					<hr>
				</body>
			</html>';
    print $tabla;

}

?>
<form id="form1" name="form1" method="POST" action="generarQR">
    <input type="submit" name="procesar" id="procesar" value="Generar QR de acceso">
</form>