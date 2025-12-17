<?php
require('system/main.php');
sessionAuth();
$layout = new HTML(title: 'AppGro-Panel Administrativo', uid: $_SESSION['user_id']);
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerPanel">
            <ul class="glass-card">
                <ul id="users">
                    <h3>Usuarios</h3> <br>
                    <input type="text" name="search_user" id="search_user" placeholder="Buscar..."
                        style="border: solid;">
                    <div id="listUsers">
                        <li>Cargando...</li>
                    </div>
                </ul>

                <ul id="ContainerEnableNewUsers">
                    <h3>Habilitar/Inhabilitar usuarios nuevos</h3>
                    <div id="ListPendingUsers">
                        <li>No hay usuarios pendientes...</li>
                    </div>
                </ul>
            </ul>
            <ul class="glass-card" id="userInfo">
                <h3>Datos del Usuario</h3> <br>
                <h4>Primero seleccione un usuario</h4>
                <input hidden type="number" name="userId" id="userId" value="">
                <li id="userName"></li>
                <li id="userLastName"></li>
                <li id="userEmail"></li>
                <li id="userBirthDate"><input type="date" id="fecha_nacimiento" value="" readonly>
                </li>
                <li id="userAge"></li>
                <li id="deleteUserBtn"><strong>ELIMINAR?</strong> (Esta acción no tiene vuelta atrás) <br><button
                        class="submit-button" id="btnEliminar" ondblclick="return clickEliminar($('#userId').val());"
                        title="Doble click para confirmar">CONFIRMAR</button>
                </li>
            </ul>
            <ul class="glass-card">
                <h3>Permisos</h3><br>
                <div class="permiso-item" id="listPermissions">
                    <li>Seleccione un usuario...</li>
                </div><br>
            </ul>
            <div id="report"></div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module">
    window.loadUsers = function () {
        $('#deleteUserBtn').hide();
        $("#fecha_nacimiento").hide();
        $.ajax({
            type: "POST",
            url: "/Bpanel",
            dataType: "json",
            success: function (response) {
                const usersList = $("#listUsers");
                const userEnableList = $('#ListPendingUsers');
                if (response.some(u => u.estado === 'espera')) {
                    userEnableList.empty();
                }
                usersList.empty();

                response.forEach(user => {
                    const listItem = $('<li></li>');
                    const userLink = $('<p></p>');

                    if (user.estado === 'activo') {
                        userLink
                            .attr('onclick', 'return getUserDetails(' + user.id_usuario + ');')
                            .text(user.nombre + ' ' + user.apellido);
                        listItem.append(userLink);
                        usersList.append(listItem);
                    } else if (user.estado === 'espera') {
                        userLink
                            .attr('onclick', 'return enableNewUser(' + user.id_usuario + ');')
                            .text(user.nombre + ' ' + user.apellido);
                        listItem.append(userLink);
                        userEnableList.append(listItem);
                    }
                });
            },
            error: (xhr, status, error) => console.error("Error al cargar usuarios:", status, error)
        });
    };

    window.getUserDetails = function (uid) {
        $.ajax({
            type: "POST",
            url: "/Bpanel",
            data: { uid },
            dataType: "json",
            success: function (response) {
                if (!response) return console.warn("No se encontraron datos para el usuario:", uid);
                uid > 1?$('#deleteUserBtn').show():$('#deleteUserBtn').hide();
                $('#userInfo h4').hide();
                $("#userId").val(response.datos.id);
                $("#userName").html("<strong>Nombre:</strong> " + response.datos.nombre);
                $("#userLastName").html("<strong>Apellido:</strong> " + response.datos.apellido);
                $("#userEmail").html("<strong>Email:</strong> " + response.datos.username);
                $('#userBirthDate').html('<strong>Fecha de nacimiento:</strong>');
                $("#fecha_nacimiento").show();
                $("#fecha_nacimiento").val(response.datos.fecha_nacimiento);
                $("#userAge").html("<strong>Edad: " + response.datos.edad + " años</strong>");
                loadPermissions(response.vistas, response.permisos);
            },
            error: (xhr, status, error) => console.error("Error al cargar detalles:", status, error)
        });
    };

    window.loadPermissions = function (vistas, permisos) {
        const permissionsList = $("#listPermissions");
        permissionsList.empty();
        vistas.forEach(vista => {
            const isChecked = permisos.includes(vista.nombre);
            const listItem = $('<li></li>').text(vista.nombre + ' ');
            const checkbox = $('<input type="checkbox" class="checkboxInput">')
                .attr('id', 'permiso_' + vista.id)
                .attr('value', vista.nombre)
                .prop('checked', isChecked)
                .on('change', () => changePermission(checkbox[0]));
            const label = $('<label class="toggleSwitch"></label>')
                .attr('for', 'permiso_' + vista.id);
            listItem.append(checkbox, label);
            permissionsList.append(listItem);
        });
    };

    window.changePermission = function (element) {
        let uid = parseInt($("#userId").val());
        let permissionId = parseInt(element.id.split('_')[1]);
        const parameter = {
            "permId": permissionId,
            "selectedUserId": uid,
        };
        $.ajax({
            url: '/BchangePermission',
            type: 'POST', data: parameter,
            success: function (response) {
                $(".toggleSwitch").attr('disabled', true);
                $("input[type='checkbox']").attr('disabled', true);
                setTimeout(() => {
                    $(".toggleSwitch").attr('disabled', false);
                    $("input[type='checkbox']").attr('disabled', false);
                }, 1000);
            },
            error: function () {
                $("#report").prepend("error");
                $('#report > span').slice(1).remove();
            }
        });
    }

    window.clickEliminar = function (uid) {
        if (uid > 1) {
            $.ajax({
                type: "POST",
                url: "/disableUser",
                data: { dropUId: uid },
                dataType: "json",
                success: () => location.reload(),
                error: (xhr, status, error) => console.error("Error al eliminar usuario:", status, error)
            });
        }
    };

    window.enableNewUser = async function (uid) {
        const { value: estado } = await Swal.fire({
            title: "Activar o desactivar usuario",
            html: `<p>Selecciona el estado que querés asignar al usuario #${uid}</p>`,
            input: "radio",
            inputOptions: {
                activo: "Activo",
                inactivo: "Inactivo"
            },
            inputValidator: (value) => {
                if (!value) return "Seleccioná una opción antes de continuar.";
            },
            showCancelButton: true,
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar",
            reverseButtons: true,
            focusConfirm: false
        });

        if (estado) {
            // Confirmación final
            const confirm = await Swal.fire({
                title: "¿Confirmar cambio?",
                text: `El usuario será marcado como ${estado}.`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sí, proceder",
                cancelButtonText: "No, cancelar",
                reverseButtons: true
            });

            if (confirm.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "/Bpanel",
                    data: { uid, estado },
                    dataType: "json",
                    success: function (response) {
                        Swal.fire({
                            icon: "success",
                            title: "Cambio aplicado",
                            text: `El usuario fue marcado como ${estado}.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadUsers();
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al cambiar estado:", status, error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "No se pudo aplicar el cambio."
                        });
                    }
                });
            }
        }
    };


    $(document).ready(() => {
        loadUsers();
        $("#search_user").on('input', function () {
            const filter = $(this).val().toUpperCase();
            $("#users li").each(function () {
                const name = $(this).text().toUpperCase();
                $(this).toggle(name.includes(filter));
            });
        });
    });
</script>