<?php
require('system/main.php');
session_start();
renderNavbar(null);
$layout = new HTML(title: 'AppGro-Inicio');
?>
<main class="main__content">
  <div class="main_container">
    <div class="main_containerLogin">
      <div id="alertBox" class="alertBox">
        <?php
        if (!empty($_SESSION['error'])):
          alertBox($_SESSION['error']);
          unset($_SESSION['error']);
        endif;
        ?>
      </div>
      <form action="/login" name="formLogin" autocomplete="on" method="POST">
        <div id="inputs" class="inputs">
          <label for="Email">Correo Electrónico</label>
          <input type="email" name="Email" id="UserName" onkeypress="return validateinputs();">
          <br>
          <label for="Password">Contraseña</label>
          <input type="password" name="Password" id="Password" oninput="return validateinputs();"
            onkeypress="return validateinputs();">
          <br>
          <button id="submitButton" name="submitButton" type="submit" class="cta"
            onmouseover="return validateinputs();">
            <span>Ingresar</span>
            <svg width="15px" height="10px" viewBox="0 0 13 10">
              <path d="M1,5 L11,5"></path>
              <polyline points="8 1 12 5 8 9"></polyline>
            </svg>
          </button>
          <br>
        </div>
        <a class="font-semibold text-indigo-600 hover:underline hover:text-indigo-500" href="/user/recover">Olvidé mi
          contraseña</a>
        <a class="font-semibold text-indigo-600 hover:underline hover:text-indigo-500"
          href="/user/register">Registrarme</a>
      </form>
    </div>
  </div>
</main>
<script type="text/javascript" language="javascript">
  $(document).ready(function () {// Carga documento, llama a verificar inputs, en caso de autocompletado o no, comprueba igual
    validateinputs();
  });
  function validateinputs() { //Si campos no están completos, deshibilita presionado de submit button

    if ($('#UserName').val().lenght > 3
      || $('#Password').val().length > 3) {
      $('#submitButton').removeAttr('disabled');
    }
    else {
      $('#submitButton').prop('disabled', 'true');
    }
  }
</script>