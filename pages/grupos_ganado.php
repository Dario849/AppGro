<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Grupos_ganado UwU');
require dirname(__DIR__, 1) . '/system/resources/database.php';
//require dirname(__DIR__,2) .'/system/ganados/Bganados.php';


$id_grupo = $_GET['id_grupo'] ?? null;

?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerbuscador">
            <form action="/grupos_ganado" method="GET">
                <input type="text" name="id_grupo" placeholder="Buscar por ID del grupo de animales" required>
                <button type="submit">Buscar</button>
            </form>

        </div>
        <div class="main_containerganados">

            <fieldset>
                <legend>Datos del Animal</legend>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Desde</th>
                            <th>Fecha Hasta</th>
                            <th>ID Subdivisión</th>
                            <th>Comentario</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        

                        try {
                            $stmt = $pdo->prepare(
                                $id_grupo ?
                                "SELECT id, fecha_desde, fecha_hasta, id_subdivision, comentario FROM grupos WHERE id = ?" :
                                "SELECT id, fecha_desde, fecha_hasta, id_subdivision, comentario FROM grupos"
                            );

                            $stmt->execute($id_grupo ? [$id_grupo] : []);
                            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            echo "Error en la conexión: " . $e->getMessage();
                            exit;
                        }

                        if (count($grupos) > 0) {
                            foreach ($grupos as $grupo) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($grupo['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($grupo['fecha_desde']) . "</td>";
                                echo "<td>" . htmlspecialchars($grupo['fecha_hasta']) . "</td>";
                                echo "<td>" . htmlspecialchars($grupo['id_subdivision']) . "</td>";
                                echo "<td>" . htmlspecialchars($grupo['comentario']) . "</td>";
                                echo "<td><a href='/ganados?id_grupo=" . urlencode($grupo['id']) . "'>Ver animales</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No hay registros disponibles</td></tr>";
                        }

                        $pdo = null;
                        ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</main>