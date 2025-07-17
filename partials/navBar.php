<?php
function renderNavbar($uid): void
{
  if (!$uid) { ?>
    <div class="home">
    <?php } else {

    require __DIR__ . '\..\..\system\resources\database.php'; // conexión PDO
    $currentPerm = $pdo->prepare("SELECT v.nombre
    FROM usuarios_vistas uv
    JOIN vistas v ON uv.id_vista = v.id
    WHERE uv.id_usuario = ?
    ");
    $currentPerm->execute([intval($uid)]);
    $access = $currentPerm->fetchAll(PDO::FETCH_COLUMN);
    ?>
      <div class="home">
        <div class="home__navbar" id="sidebar">
          <!-- DIRECCIONES, ELEMENTOS SECCION SUPERIOR DE LA BARRA -->
          <ul class="home__navbar-TopList">
            <?php
            foreach ($access as $vista) {
              echo "  <li><a href=/".strtolower( $vista)."><button>". htmlspecialchars($vista) ."</button></a></li>\n";
            }
            ?>
            <!-- … resto de items … -->
          </ul>
          <!-- UTILIDADES, ELEMENTOS SECCION INFERIOR DE LA BARRA -->
          <ul class="home__navbar-BottomList">
            <li class="home__navbar-item"><a href="/dashboard"><button>Dashboard</button></a></li>
            <li class="home__navbar-item"><a href="/user/profile"><button>Perfil</button></a></li>
            <li class="home__navbar-item"><a href="/user/logout"><button>Log-out</button></a></li>

          </ul>
        </div>
      </div>
  <?php }
}
