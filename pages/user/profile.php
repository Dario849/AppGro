<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'AppGro-Panel de usuario');
require dirname(__DIR__, 3) . '\system\resources\database.php';
$sql = "SELECT nombre, apellido, username, fecha_nacimiento FROM Usuarios WHERE id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'uid' => $_SESSION['user_id'],
]);
$usuarios = $stmt->fetchAll(); // array de filas
?>
<script>
    $(document).ready(function () {// Carga documento, llama a verificar inputs, en caso de autocompletado o no, comprueba igual
        // validateinputs();
        if (window.location.search.indexOf('ChangeOfMail=') !== -1) { // Si la URL contiene ChangeOfMail=, procede
            $("#ChangeOfMail").hide();
            $("#UserChangeOfMail").show();
            $("#UserChangeOfPswrd").hide();
            $("#confirmPass").prop('required','true');
            console.log("Parámetro ChangeOfMail presente");
        } else if (window.location.search.indexOf('ChangeOfPswrd=') !== -1) {
            $("#ChangeOfPswrd").hide();
            $("#UserChangeOfMail").hide();
            $("#UserChangeOfPswrd").show();
            $("#actualPass").prop('required','true');
            console.log("Parámetro ChangeOfPswrd presente");
        } else {
            $("#ChangeOfPswrd").show();
            $("#ChangeOfPswrd").show();
            $("#UserChangeOfMail").hide();
            $("#UserChangeOfPswrd").hide();
        }
        $("#submitButtonPassword").click(function () {
            if(validateinputs()){
                $('#formProfile').prop('method', 'POST');
            }
        });
        $("#submitButtonEmail").click(function () {
            if(validateinputs()){
                $('#formProfile').prop('method', 'POST');
            }
        });
        $("#ChangeOfMail").click(function () {
            $("#actualPass").removeAttr('required');
        });
        $("#ChangeOfPswrd").click(function () {
            $("#confirmPass").removeAttr('required');
        });
        function validateinputs() { //Si campos no están completos, deshabilita presionado de submit button
            if (($("#confirmPass").val().length > 3) || ($("#actualPass").val().length > 3)) {
                return true;
            }
            else {
                return false;
            }
        }

    });
</script>
<main class="main__content">
    <div class="main_container">
        <?php
        if (!empty($_SESSION['user_id'])):
            echo "Panel de usuario: " . $_SESSION['user_name'] . " -- ID:" . $_SESSION['user_id'];
            // unset($_SESSION['user_id']); //ELIMINA CONTENIDO (PODRIA SERVIR PARA CERRAR SESIÓN)
            // $_SESSION = [];  // Limpia el arreglo de sesión
        endif;

        if (!isset($_SESSION['contador'])) {
            $_SESSION['contador'] = 1;
        } else {
            $_SESSION['contador']++;
        }
        echo " <br> Has visitado esta página " . $_SESSION['contador'] . " veces.";
        ?>
        <div class="main_containerProfile">
            <div id="alertBox" class="alertBox">
                <?php
                if (!empty($_SESSION['error'])):
                    alertBox($_SESSION['error'], 0);
                    unset($_SESSION['error']);

                elseif (!empty($_SESSION['success'])):
                    alertBox(0, $_SESSION['success']);
                    unset($_SESSION['success']);
                endif;
                ?>
            </div>
            <div class="UserData">
                <?php
                foreach ($usuarios as $u) {
                    echo '<p>' . htmlspecialchars($u['nombre']) . '</p>';
                    echo '<p>' . htmlspecialchars($u['apellido']) . '</p>';
                    echo '<p>' . htmlspecialchars($u['username']) . '</p>';
                    echo '<p>' . htmlspecialchars($u['fecha_nacimiento']) . '</p>';
                }
                ?>
            </div>
            <form action="/user/profile" id="formProfile" class="formProfile" method="GET">
                <button id="ChangeOfMail" name="ChangeOfMail" type="submit" class="cta">
                    <span>Cambiar mi correo</span>
                    <svg width="15px" height="10px" viewBox="0 0 13 10">
                        <path d="M1,5 L11,5"></path>
                        <polyline points="8 1 12 5 8 9"></polyline>
                    </svg>
                </button> <button id="ChangeOfPswrd" name="ChangeOfPswrd" type="submit" class="cta">
                    <span>Cambiar mi Contraseña</span>
                    <svg width="15px" height="10px" viewBox="0 0 13 10">
                        <path d="M1,5 L11,5"></path>
                        <polyline points="8 1 12 5 8 9"></polyline>
                    </svg>
                </button>
                <div id="UserChangeOfMail" class="UserChangeOfMail">
                    <p>Cambiar mi correo</p>
                    <br>
                    <label for="confirmPass">Ingrese su contraseña</label>
                    <br>
                    <input type="password" id="confirmPass" 
                        autocomplete="current-password" name="confirmPass">
                    <br>
                    <label for="newMail">Ingrese su nuevo correo</label>
                    <input type="email" name="newMail" id="newMail">
                    <button id="submitButtonEmail" name="submitButtonEmail" type="submit" class="cta"
                        >
                        <span>Ingresar</span>
                        <svg width="15px" height="10px" viewBox="0 0 13 10">
                            <path d="M1,5 L11,5"></path>
                            <polyline points="8 1 12 5 8 9"></polyline>
                        </svg>
                    </button>
                </div>
                <div id="UserChangeOfPswrd" class="UserChangeOfPswrd">
                    <p>Cambiar mi contraseña</p>
                    <br>
                    <label for="actualPass">Confirme su contraseña actual</label>
                    <br><input type="password" id="actualPass" name="actualPass" autocomplete="current-password"
                        >
                    <br>
                    <label for="newPass1">Ingrese su nueva contraseña</label>
                    <input type="password" name="newPass1" id="newPass1" >
                    <label for="newPass2">Confirme su nueva contraseña</label>
                    <input type="password" name="newPass2" id="newPass2" >
                    <button id="submitButtonPassword" name="submitButtonPassword" type="submit" class="cta"
                        >
                        <span>Ingresar</span>
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