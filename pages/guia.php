<?php
require('system/main.php');
session_start();
$layout = new HTML(title: 'Guía de usuario', uid: $_SESSION['user_id'] ?? 0);
?>
<script>
    $(function () {
        if($('#uid_n').val()==0){
            $('#btnEditUserContent').hide(true);
        }
        OnLoadBundledScript();
        ClickEditorTinyMCE();
    });
</script>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerGuiaDeUsuario">
            <h1 class="menu-title">Guía de usuario</h1>
            <p>Bienvenido a la guía de usuario. Aquí encontrarás información útil para navegar y
                utilizar las diferentes funcionalidades de la aplicación.</p>
            <br>
            <h2 class="menu-subtitle">Navegación</h2>
            <ul class="menu-list">
                <li><strong>Barra lateral:</strong> La página posee una barra lateral que permite navegar de forma
                    sensilla
                    entre las diferentes secciones de la aplicación. Simplemente haz clic en el ícono correspondiente
                    para acceder a las secciones que desees.
                    <label class="infoColapse" for="colapsarNavBar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path
                                d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                        </svg> Ver ejemplo
                    </label>
                    <button type="button" id="colapsarNavBar" class="collapsible"></button>
                    <div class="content glass-card">
                        Esta lista se puede visualizar en el lado izquierdo de la pantalla en todas las secciones de la
                        aplicación en las cuales tenes permitido acceder.
                        <div class="guiaShowNavBar">
                            <div class="home__navbar">
                                <div class="containerHide">
                                    <button title="Oculta o expande la lista" class="setting-btn" id="buttonHide"
                                        data-active="true" title="ocultar/mostrar">
                                        <span class="bar bar1"></span>
                                        <span class="bar bar2"></span>
                                        <span class="bar bar1"></span>
                                    </button>
                                </div>
                                <br>
                                <!-- DIRECCIONES, ELEMENTOS SECCION SUPERIOR DE LA BARRA -->
                                <ul class="home_navbar-TopList">
                                    <br>
                                    <hr>
                                    <li>Todas las secciones de la página se listarían acá</li>
                                    <hr>
                                </ul>
                                <!-- UTILIDADES, ELEMENTOS SECCION INFERIOR DE LA BARRA -->
                                <ul class="home__navbar-BottomList">
                                    <li>Estos elementos son fijos y los verá siempre ⇓</li>
                                    <li class="home__navbar-item"><a href="/dashboard"
                                            title="Lleva al menú principal"><button>Dashboard</button></a>
                                    </li>
                                    <li class="home__navbar-item"><a href="/guia"
                                            title="Ver esta guía"><button>Guía/ayuda</button></a>
                                    </li>
                                    <li class="home__navbar-item"><a href="/user/profile"
                                            title="Ver/modificar perfil del usuario"><button>Perfil</button></a>
                                    </li>
                                    <li class="home__navbar-item"><a href="/user/logout"
                                            title="Cerrar sesión"><button>Log-out</button></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li><strong>Administrar Balance:</strong> Gestiona tus balances financieros, incluyendo ingresos y
                    gastos.
                    <label class="infoColapse" for="colapsarTransacciones">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path
                                d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                        </svg> Ver ejemplo
                    </label>
                    <button type="button" id="colapsarTransacciones" class="collapsible"></button>
                    <div class="content glass-card">
                        Esto es solo un resumen, para más detalles, consulta la sección de "Balance" en el menú
                        principal.
                        <div id="balanceMenu"></div>
                        <br>
                    </div>
                </li>
            </ul>
            <ul>
                <div id="displayTinyMCE_LastUserContent">
                </div>
                <button id="btnEditUserContent" action="0">Editar contenido</button>
            </ul>
            <h2 class="menu-subtitle">Soporte</h2>
            <p>Si necesitas ayuda adicional, no dudes en contactar con nuestro equipo de soporte a través del correo
                electrónico <strong><a
                        href="mailto:soporteappgro.zeabur@gmail.com">soporteappgro.zeabur@gmail.com</a></strong></p>
            <p>¡Gracias por utilizar nuestra aplicación!</p>
            <p style="align-self: flex-end; margin-right: 7vh;">Versión: <strong>1.0.0</strong></p>
        </div>
    </div>
</main>
<script>
    function OnLoadBundledScript() {
        $('#balanceMenu').load('/pages/estadisticas/balanceMenu.html');
        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                } else {
                    content.style.display = "block";
                }
            });
        }
    }
    function ClickEditorTinyMCE() {
        const loadGuia = $('displayTinyMCE_LastUserContent').html();
        const action = $('#btnEditUserContent').attr('action');
        let data = { action: action, content: "" };
        if (!loadGuia) {
            $('#btnEditUserContent').text('Cargando contenido...');
            $('#displayTinyMCE_LastUserContent').html('<p>Cargando contenido...</p>');
            $.ajax({
                url: '/BGuiaContent',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response && response.contenido) {
                        $('#displayTinyMCE_LastUserContent').html(response.contenido);
                        $('#btnEditUserContent').text('Editar contenido');
                    } else {
                        $('#displayTinyMCE_LastUserContent').html('<p>No se pudo cargar el contenido de la guía.</p>');
                        console.warn('Respuesta inesperada del servidor:', response);
                    }
                },
                error: function () {
                    $('#displayTinyMCE_LastUserContent').html('<p>Error al cargar el contenido de la guía.</p>');
                }
            });
        } else {
            console.log("exists already loaded data")
        }

        $('#btnEditUserContent').click(function () {
            const editorTinyMCE = tinymce.get("displayTinyMCE_LastUserContent");
            const btnConfirm = $('#btnEditUserContent');
            data.action = btnConfirm.attr('action');
            // Inicializar TinyMCE
            tinymce.init({
                selector: '#displayTinyMCE_LastUserContent',
                width: 1000,
                height:700,
                setup: function (editor) {
                    editor.on('change input', function () {
                        $('#btnEditUserContent').text('Guardar cambios');
                        $('#btnEditUserContent').attr('action', '2');
                    });
                },
                license_key: 'gpl' // gpl for open source, T8LK:... for commercial
            });
            if (btnConfirm.attr('action') == 2) {
                tinymce.triggerSave();
                data.content = tinymce.get('displayTinyMCE_LastUserContent').getContent();
                $.ajax({
                    url: '/BGuiaContent',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response && response.contenido) {
                            $('#displayTinyMCE_LastUserContent').html(response.contenido);
                            $('#btnEditUserContent').text('Editar contenido');
                            $('#btnEditUserContent').attr('action', '1');
                            tinymce.remove('#displayTinyMCE_LastUserContent');
                        } else {
                            $('#displayTinyMCE_LastUserContent').html('<p>No se pudo guardar el contenido de la guía.</p>');
                            console.warn('Respuesta inesperada del servidor:', response);
                        }
                    },
                    error: function () {
                        $('#displayTinyMCE_LastUserContent').html('<p>Error al cargar el contenido de la guía.</p>');
                    }
                });
            } else if (btnConfirm.attr('action') == 1) {
                tinymce.remove('#displayTinyMCE_LastUserContent');
                $('#btnEditUserContent').text('Editar contenido');
                $('#btnEditUserContent').attr('action', '0');
            } else {
                $('#btnEditUserContent').text('Cerrar editor');
                $('#btnEditUserContent').attr('title', 'Realize cambios si no desea cerrar el editor aún');
                $('#btnEditUserContent').attr('action', '1');

            }
        });

    }
</script>