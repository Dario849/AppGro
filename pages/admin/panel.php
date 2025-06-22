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
            <h2>Usuarios</h2>
            <ul>
                <?php foreach ($usuarios as $u): ?>
                    <li>
                        <a href="?uid=<?= $u['id_usuario'] ?>"> <?= htmlspecialchars($u['nombre']) ?> </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- Permisos -->
            <h2>Permisos</h2>
            <?php if (isset($permisos)): ?>
                <ul>
                    <?php foreach ($permisos as $permiso): ?>
                        <li><?= htmlspecialchars($permiso) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Seleccioná un usuario</p>
            <?php endif; ?>
            <h2>Datos del Usuario</h2>
            <?php if (isset($datos)): ?>
                <ul>
                    <li><strong>Nombre:</strong> <?= htmlspecialchars($datos['nombre']) ?></li>
                    <li><strong>Apellido:</strong> <?= htmlspecialchars($datos['apellido']) ?></li>
                    <li><strong>Email:</strong> <?= htmlspecialchars($datos['username']) ?></li>
                    <li><strong>Fecha de nacimiento:</strong><input type="date" name="fecha_nacimiento"
                            id="fecha_nacimiento" value="<?= htmlspecialchars($datos['fecha_nacimiento']) ?>" readonly>
                    </li>
                    <li><strong>Edad:</strong> <?= (int) $datos['edad'] ?> años</li>

                </ul>
            <?php else: ?>
                <p>Sin datos disponibles</p>
            <?php endif; ?>
        </div>
    </div>
</main>