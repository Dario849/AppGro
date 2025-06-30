<?php
/*require('system/main.php');

sessionCheck();
renderNavbar();
$layout = new HTML(title: 'Ganado UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';
//require dirname(__DIR__,2) .'\system\ganados\Bganados.php';

//obtener id_ganado desde la url
$id_ganado = isset($_POST['id']) ? intval($_POST['id']) : 0;
$ganado = [];
if ($id_ganado > 0) {
    $ganado = obtenerGanadoPorId($pdo, $id_ganado);
}
    */
?>
<html>
    <head></head>
    <body>

<div id="contenedor">
    <div id="datos">
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
                    $conn = new mysqli('localhost', 'root', '', 'app_campo');

                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    $sql = "SELECT id, fecha_desde, fecha_hasta, id_subdivision, comentario FROM grupos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($grupo = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($grupo['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['fecha_desde']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['fecha_hasta']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['id_subdivision']) . "</td>";
                            echo "<td>" . htmlspecialchars($grupo['comentario']) . "</td>";
                            echo "<td><a href='ganados.php?id_grupo=" . urlencode($grupo['id']) . "'>Ver animales</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay registros disponibles</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </fieldset>
    </div>

    <!-- Puedes agregar las otras dos columnas aquí si es necesario -->
    <div id="vacunas_y_baños">
        <!-- Contenido adicional -->
    </div>
    <div id="terceracolumna">
        <!-- Contenido adicional -->
    </div>
</div>
<style>
        #contenedor {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        #datos {
            width: 33%;
        }

        #vacunas_y_baños {
            width: 33%;
        }

        #terceracolumna {
            width: 33%;
        }

        fieldset {
            border: 2px solid #007bff;
            border-radius: 10px;
            background-color: #f0f0f0;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</body> 
</html>