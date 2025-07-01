<?php
require('system/main.php');
sessionAuth();
require dirname(__DIR__, levels: 3) . '\system\resources\database.php';// conecta con tu PDO $pdo
renderNavbar($_SESSION['user_id']);
require_once('system\admin\Bpanel.php');
$layout = new HTML(title: 'AppGro-Panel Administrativo');
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerPanel">
            <ul>
                <h2>Usuarios</h2> <br>
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
                   name="permisos[]" 
                   value="' . htmlspecialchars($nombre) . '" 
                   class="checkboxInput" ' . ($habilitado ? 'checked' : '') . '>
            <label for="permiso_' . $id . '" class="toggleSwitch"></label>
        </div><br>
    ';
                    } ?>
                    <!-- <li><button class="cta">
                            <span>Aplicar cambios</span>
                            <svg width="15px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </button></li> -->
                </ul>
            <?php else: ?>
                <p>Sin datos disponibles</p>
            <?php endif; ?>
        </div>
    </div>
</main>