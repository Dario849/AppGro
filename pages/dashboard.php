<?php
require('system/main.php');
renderNavbar(); //BARRA DE NAVEGACION
//DB CONNECTION
require_once __DIR__.('/../../system/database.php');
$layout = new HTML(title: 'PHP via Vite');
$db        = new Database();
$connection = $db->connect();//CONECTADO
?>

<main class="main__content">
    <div class="main_container">
        <div class="main_containerTiempo">
    
        </div>
        <div class="main_containerTareas">
    
        </div>
        <div class="main_containerMapa">
    
        </div>
    </div>
</main>
<script src="/src/scripts/repos.ts" type="module"></script>
