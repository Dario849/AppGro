<?php
function renderNavbar($uid): void
{
  if (!$uid) { ?>
    <div class="home">
    <?php } else {
    require __DIR__ . '/../../system/resources/database.php'; // conexión PDO
    $currentPerm = $pdo->prepare("SELECT v.nombre
    FROM usuarios_vistas uv
    JOIN vistas v ON uv.id_vista = v.id
    WHERE uv.id_usuario = ?
    ");
    $currentPerm->execute([intval($uid)]);
    $access = $currentPerm->fetchAll(PDO::FETCH_COLUMN);
    ?>
      <div class="home">
        <script src="js/jquery-3.7.1.min.js"></script>
        <script>
          $(document).ready(function () {
            $('#buttonHide').click(function () {
              const btnBool = $(this).data("active"); //se carga "false" o "true", JS admite como bool tal statement
              $(this).data("active", !btnBool); // prepara en memorio el contrario del statement previo, para próximo press de button
              if (!btnBool) {
                $('.home__navbar').css({
                  "transition": "width 1s ease-out, transform 1s ease-out",
                  "transform": "translateX(0px)",
                  'width': '200px',
                });
                $('.home').css({
                  "transition": "transform 1s ease-out, visibility 1s, margin-right 1s ease-out",
                  "transform": "translateX(-150px)",
                  "margin-right": "-150px",
                });
              } else {
                $('.home__navbar').css({
                  "transition": "width 1s ease-out, transform 1s ease-out",
                  "transform": "translateX(0px)",
                  'width': '',
                });
                $('.home').css({
                  "transition": "transform 1s ease-out, margin-right 1s ease-out",
                  "transform": "translateX(0px)",
                  "margin-right": "",
                });
              }

            });
          });
        </script>
        <div class="home__navbar" id="sidebar">
          <div class="containerHide">
              <button class="setting-btn" id="buttonHide" title="ocultar/mostrar">
                <span class="bar bar1"></span>
                <span class="bar bar2"></span>
                <span class="bar bar1"></span>
              </button>
          </div>
          <!-- DIRECCIONES, ELEMENTOS SECCION SUPERIOR DE LA BARRA -->
          <ul class="home__navbar-TopList">
            <?php
            foreach ($access as $vista) {
              echo "  <li><a href=/" . strtolower($vista) . "><button>" . htmlspecialchars($vista) . "</button></a></li>\n";
            }
            ?>
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
