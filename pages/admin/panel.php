<?php
require('system/main.php');
sessionAuth();
require dirname(__DIR__, levels: 3) . '/system/resources/database.php'; // conecta con tu PDO $pdo
require_once('system/admin/Bpanel.php');
$layout = new HTML(title: 'AppGro-Panel Administrativo');
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerPanel">
            <ul id="users">
                <h2>Usuarios</h2> <br>
                <input type="text" name="search_user" id="search_user" placeholder="Buscar..." style="border: solid;">
                <?php foreach ($usuarios as $u): ?>
                    <li>
                        <a href="?uid=<?= $u['id_usuario'] ?>"> <?= htmlspecialchars($u['nombre']) ?> </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- Permisos -->
            <?php if (isset($datos)): ?>
                <ul>
                    <h2>Datos del Usuario</h2> <br>
                    <input hidden type="number" name="userId" id="userId" value="<?= htmlspecialchars($datos['id']) ?>">
                    <li><strong>Nombre:</strong> <?= htmlspecialchars($datos['nombre']) ?></li>
                    <li><strong>Apellido:</strong> <?= htmlspecialchars($datos['apellido']) ?></li>
                    <li><strong>Email:</strong> <?= htmlspecialchars($datos['username']) ?></li>
                    <li><strong>Fecha de nacimiento:</strong><input type="date" name="fecha_nacimiento"
                            id="fecha_nacimiento" value="<?= htmlspecialchars($datos['fecha_nacimiento']) ?>" readonly>
                    </li>
                    <li><strong>Edad:</strong> <?= (int) $datos['edad'] ?> años</li>

                </ul>
                <ul>
                    <h2>Permisos</h2><br>
                    <?php foreach ($vistas as $vista) {
                        $id = $vista['id'];
                        $nombre = $vista['nombre'];
                        $habilitado = in_array($nombre, $permisos);

                        echo '
                        <div class="permiso-item">
                        ' . htmlspecialchars($nombre) . ' - 
                            <input type="checkbox" 
                        id="permiso_' . $id . '"  
                        value="' . htmlspecialchars($nombre) . '" 
                        class="checkboxInput" ' . ($habilitado ? 'checked' : '') . '>
                        <label for="permiso_' . $id . '" class="toggleSwitch" onchange=" return changePermission();"></label>
                        </div><br>
                        ';
                    } ?>
                </ul>
            <?php else: ?>
                <p>Sin datos disponibles</p>
            <?php endif; ?>
            <div id="report"></div>
        </div>
    </div>
</main>
<script>
    $("#search_user").on('input', function () {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("search_user");
        filter = input.value.toUpperCase();
        ul = document.getElementById("users");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    });
    $("input[type='checkbox']").on("change", function () { //Llamada a función asincrona para cambiar el permiso cambiado (false or true)
        let uid = parseInt($("#userId").val());
        let permissionId = parseInt(this.id.split('_')[1]);
        const parameter = {
            "permId": permissionId,
            "selectedUserId": uid,
        };
        $.ajax({ //Envio de solicitud a back, envia id_usuario y id_vista
            url: '/BchangePermission',
            type: 'POST',
            data: parameter,
            success: function (response) {
                $(".toggleSwitch").attr('disabled', true);
                $("input[type='checkbox']").attr('disabled', true);
                console.log(response);
                setTimeout(() => { //timeout para evitar flooding de envio de formulario
                    $(".toggleSwitch").attr('disabled', false);
                    $("input[type='checkbox']").attr('disabled', false);
                }, 1000);
            },
            error: function () { //fallback en caso de que no exista conexión al backend
                $("#report").prepend("error");
                $('#report > span').slice(1).remove();

            }
        });


    });
</script>