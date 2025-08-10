<?php
require('system/main.php');
session_start();
$layout = new HTML(title: 'AppGro-Inicio');
if(($_SESSION['cookie'] ?? false) == ($_COOKIE['PHPSESSID'] ?? false) && ($_SESSION['logged'] ?? false)) {
  header('Location: /dashboard');
  exit;
}
?>
<!-- RECAPTCHA -->
<!-- <script type="text/javascript">
  var onloadCallback = function () {
    alert("grecaptcha is ready!");
  };
</script> -->
<script src="https://www.google.com/recaptcha/api.js?hl=es-419" async defer>
</script><!-- RECAPTCHA -->
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
      <form action="/login" name="formLogin" autocomplete="on" method="POST">
        <div id="inputs" class="inputs">
          <label for="UserName">Correo Electrónico</label>
          <input type="email" name="Email" id="UserName" autocomplete="username" onkeypress="return validateinputs();">
          <br>
          <label for="Password">Contraseña</label>
          <input type="password" name="Password" id="Password" autocomplete="current-password"
            oninput="return validateinputs();" onkeypress="return validateinputs();">
          <br>
          <!-- RECAPTCHA -->
          <div class="g-recaptcha" data-sitekey="6LehHnsrAAAAAIU1rLgtG7CTnQfpw880nX_wFA40" data-action="LOGIN"></div>
          <br />
          <!-- RECAPTCHA -->
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

    if ($('#UserName').val().lenght > 6
      || $('#Password').val().length > 4) {
      $('#submitButton').removeAttr('disabled');
    }
    else {
      $('#submitButton').prop('disabled', 'true');
    }
  }
</script>