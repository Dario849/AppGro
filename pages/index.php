<?php
require('system/main.php');
session_start();
$layout = new HTML(title: 'AppGro-Inicio');
if (($_SESSION['cookie'] ?? false) == ($_COOKIE['PHPSESSID'] ?? false) && ($_SESSION['logged'] ?? false)) {
  header('Location: /dashboard');
  exit;
}

?>
<main class="main__content">
  <div class="main_container">
    <div class="main_containerLogin">
      <div id="alertBox" class="alertBox">
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
      <form action="/login" id="form_Login" autocomplete="on" method="POST">
        <div id="inputs" class="inputs">
          <label for="UserName">Correo Electrónico</label>
          <input type="email" name="Email" id="UserName" autocomplete="email" onkeypress="return validateinputs();">
          <br>
          <label for="Password">Contraseña</label>
          <input type="password" name="Password" id="Password" autocomplete="current-password"
            oninput="return validateinputs();" onkeypress="return validateinputs();">
          <br>
          <button id="submitLoginButton" class="g-recaptcha cta" data-sitekey="6LdT2NcrAAAAAOGcZpBzPxpkbUHJvCz7aT7Rmqwq" data-callback='onSubmit'
            data-action='submit' onmouseover="return validateinputs();">
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

    if ($('#UserName').val().lenght > 6
      || $('#Password').val().length > 4) {
      $('#submitButton').removeAttr('disabled');
    }
    else {
      $('#submitButton').prop('disabled', 'true');
    }
  }
</script>