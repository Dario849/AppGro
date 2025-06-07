<?php
require('system/main.php');
renderNavbar();
session_start();
$layout = new HTML(title: 'PHP via Vite');

?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerLogin">
            <?php if (!empty($_SESSION['error'])): ?>
  <div class="flex p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
  <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
  </svg>
  <span class="sr-only">Info</span>
  <div>
    <span>      <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
              ?><br></span>
    <span class="font-medium">Ensure that these requirements are met:</span>
      <ul class="mt-1.5 list-disc list-inside">
        <li>At least 10 characters (and up to 100 characters)</li>
        <li>At least one lowercase character</li>
        <li>Inclusion of at least one special character, e.g., ! @ # ?</li>
    </ul>
  </div>
</div>


            <?php endif; ?>
            <form action="/login" name="formLogin" method="POST">
                <div id="mostrarAlerta" class="Alerta">
                </div>
                <div id="inputs" class="inputs">
                    <label for="Email">Correo Electrónico</label>
                    <input type="text" name="Email" id="Email">
                    <br>
                    <label for="Password">Contraseña</label>
                    <input type="text" name="Password" id="Password">
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
                <a href="/recover">Olvidé mi contraseña</a>
                <a href="/register">Registrarme</a>
            </form>
        </div>
    </div>
</main>