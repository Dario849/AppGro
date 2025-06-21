<?php
require('system/main.php');
renderNavbar();
session_start();
$layout = new HTML(title: 'AppGro-Calendario');

// use App\resources\Database;

// $db   = new Database();
// $conn = $db->connect();

// $sql    = "SELECT nombre FROM Usuarios WHERE id BETWEEN 1 AND 5";
// $result = $conn->query($sql);

// if ($result === false) {
//     die("Error en la consulta: " . $conn->error);
// }
?>
<main class="main__content">
    <div class="main_container">
        <?php
        if (!empty($_SESSION['user_id'])): echo $_SESSION['user_id'] . "Bienvenido usuario:" . $_SESSION['user_name'];
            // unset($_SESSION['user_id']); // esto remueve la varíable, una vez se visita por primera vez (en reload se elimina)
        endif;
        ?>
    </div>
</main>