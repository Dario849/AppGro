<?php
require('system/main.php');
sessionCheck();
renderNavbar($_SESSION['user_id']);
$layout = new HTML(title: 'AppGro-Menú');
require dirname(__DIR__, 2) . '\system\resources\database.php';
$sql = "SELECT nombre FROM Usuarios WHERE id BETWEEN :min AND :max";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'min' => 1,
    'max' => 5,
]);
$usuarios = $stmt->fetchAll(); // array de filas
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerDashboard">


            <?php
            if (!empty($_SESSION['user_id'])):
                echo $_SESSION['user_id'] . "Bienvenido usuario: " . $_SESSION['user_name'];
                // unset($_SESSION['user_id']); //ELIMINA CONTENIDO (PODRIA SERVIR PARA CERRAR SESIÓN)
                // $_SESSION = [];  // Limpia el arreglo de sesión
            endif;
            ?>
            <?php
            if (!isset($_SESSION['contador'])) {
                $_SESSION['contador'] = 1;
            } else {
                $_SESSION['contador']++;
            }
            echo "Has visitado esta página " . $_SESSION['contador'] . " veces.";
            ?>
            <div id="containerTiempo" class="main_containerDashboardTiempo">
                <?php
                weatherApi();
                ?>
            </div>
            <div class="main_containerDashboardTareas">
                <?php
                foreach ($usuarios as $u) {
                    echo '<p>' . htmlspecialchars($u['nombre']) . '</p>';
                }
                ?>
            </div>
            <div class="main_containerDashboardMapa">

            </div>
        </div>
    </div>
</main>