<?php
require('system/main.php');
session_start();
renderNavbar(null);
$layout = new HTML(title: 'AppGro-Restablecer Contraseña');
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerRecover">
            <div id="alertBox" class="alertBox">
                <?php
                if (!empty($_SESSION['error'])):
                    alertBox($_SESSION['error']);
                    unset($_SESSION['error']);
                endif;
                ?>
            </div>

            <form action="/recover" method="POST" name="formRecover" onsubmit="return oneRequestOnly();">
                <div id="inputs" class="inputs">
                    <h2>Recuperar contraseña</h2>
                    <label for="email">Correo electrónico:</label> <br>
                    <input type="email" name="email" id="email" required> <br>
                    <button type="submit" class="cta" id="buttonRecover">
                        <span id="spanRecover">Enviar nueva contraseña</span>
                        <svg width="15px" height="10px" viewBox="0 0 13 10">
                            <path d="M1,5 L11,5"></path>
                            <polyline points="8 1 12 5 8 9"></polyline>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    function oneRequestOnly() {
        $("#buttonRecover").attr("disabled", true);
        $("#spanRecover").text("Procesando...");
    }
</script>