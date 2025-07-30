<?php
require('system/main.php');
session_start();
$layout = new HTML(title: 'AppGro-Registrarse');
?>
<main class="main__content">
  <div class="main_container">
    <div class="main_containerRegister">
      <div class="alertBox">
                <?php
                if (!empty($_SESSION['error'])):
                    alertBox($_SESSION['error'], null);
                    unset($_SESSION['error']);

                elseif (!empty($_SESSION['success'])):
                    alertBox(null, $_SESSION['success']);
                    unset($_SESSION['success']);
                endif;
                ?>
      </div>
      <p style="margin-bottom: auto; text-align: center; font-family: auto; text-decoration-line: underline;">Ingrese
        sus datos para registrar su cuenta</p>
      <form action="/user/register" name="formRegister" method="POST">
        <div id="inputs" class="inputs">
          <label for="Nombre">Nombre</label>
          <input type="text" name="Nombre" id="Nombre" required>
          <br>
          <label for="Apellido">Apellido</label>
          <input type="text" name="Apellido" id="Apellido" required>
          <br>
          <label for="FechaNacimiento">Fecha_Nacimiento</label>
          <input type="date" name="FechaNacimiento" id="FechaNacimiento" required>
          <br>
          <label for="Email">Correo Electrónico</label>
          <input type="email" name="Email" id="Email" required>
          <br>
          <label for="Password">Contraseña</label>
          <input type="password" name="Password" id="Password" required>
          <br>
          <button type="submit" class="cta">
            <span>Ingresar</span>
            <svg width="15px" height="10px" viewBox="0 0 13 10">
              <path d="M1,5 L11,5"></path>
              <polyline points="8 1 12 5 8 9"></polyline>
            </svg>
          </button>
          <br>
        </div>
        <span>Ya tenes cuenta?<a class="font-semibold text-indigo-600 hover:underline hover:text-indigo-500"
            href="/">Iniciar sesión</a></span>
      </form>
    </div>
  </div>
</main>