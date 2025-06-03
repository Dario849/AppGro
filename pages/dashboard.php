<?php
require('system/main.php');
renderNavbar();

$layout = new HTML(title: 'PHP via Vite');

use App\resources\Database;

$db   = new Database();
$conn = $db->connect();

$sql    = "SELECT nombre FROM Usuarios WHERE id BETWEEN 1 AND 5";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}
?>
<script>
    addEventListener("DOMContentLoaded", (event) => {
        var json = JSON.parse($("#climaJson").val());
        $("#containerTiempo").html(json.location.name +" <br><strong>"+json.current.condition.text+"</strong> <br>"+json.current.last_updated+"<img src="+json.current.condition.icon+" alt='Girl in a jacket' width='10%' height='10%'>");
    });
</script>
<main class="main__content">
    <div class="main_container">
        <div id="containerTiempo" class="main_containerTiempo">
<input type="hidden" name="climaJson" id="climaJson" value='<?php
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$url = "https://api.weatherapi.com/v1/current.json?q=".$ip."&lang=es&key=fe64220e13714edf99e215240253105";
 echo file_get_contents(filename: $url); 
 ?>'>
        </div>
        <div class="main_containerTareas">
            <?php
            // 5) Recorremos y mostramos los nombres
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // escapamos por seguridad
                    $nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
                    echo $nombre;
                }
            } else {
                echo '<div class="text-gray-500">No se encontraron usuarios.</div>';
            }
            ?>

        </div>
        
        <div class="main_containerMapa">

        </div>
    </div>
</main>
<script src="/src/scripts/repos.ts" type="module"></script>