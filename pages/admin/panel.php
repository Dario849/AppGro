<?php
require('system/main.php');
sessionAuth();
require dirname(__DIR__, levels: 3) . '\system\resources\database.php'; // conecta con tu PDO $pdo
$layout = new HTML(title: 'AppGro-Panel Administrativo', uid: $_SESSION['user_id']);
?>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerPanel">
            <ul id="users">
                <h2>Usuarios</h2> <br>
                <input type="text" name="search_user" id="search_user" placeholder="Buscar..." style="border: solid;">
                <div id="listUsers">
                    <li>Cargando...</li>
                </div>
            </ul>
            <ul>
                <h2>Datos del Usuario</h2> <br>
                <input hidden type="number" name="userId" id="userId" value="">
                <li id="userName"><strong>Nombre:</strong></li>
                <li id="userLastName"><strong>Apellido:</strong></li>
                <li id="userEmail"><strong>Email:</strong></li>
                <li id="userBirthDate"><strong>Fecha de nacimiento:</strong><input type="date" id="fecha_nacimiento"
                        value="" readonly>
                </li>
                <li id="userAge"><strong>Edad:</strong></li>
                <li><strong>ELIMINAR?</strong> <button class="submit-button" id="btnEliminar"
                        ondblclick="return clickEliminar($('#userId').val());">CONFIRMAR</button>
                </li>
            </ul>
            <ul>
                <h2>Permisos</h2><br>
                <div class="permiso-item" id="listPermissions">
                    <li>Seleccione un usuario</li>
                </div><br>
            </ul>
            <div id="report"></div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function () {
        loadUsers();
    });
    function loadUsers() {
        $.ajax({
            type: "GET",
            url: "/Bpanel",
            dataType: "json",
            success: function (response) {
                console.log(response);
                var usersList = $("#listUsers");
                usersList.empty(); // Limpiar la lista actual
                response.forEach(function (user) {
                    var listItem = $('<li></li>');
                    var userLink = $('<p></p>')
                        .attr('onclick', 'return getUserDetails(' + user.id_usuario + ');')
                        .text(user.nombre);
                    listItem.append(userLink);
                    usersList.append(listItem);
                });
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar usuarios: " + status + " - " + error);
            }
        });
    }
    function loadPermissions(vistas, permisos) {
        var permissionsList = $("#listPermissions");
        permissionsList.find("li").remove(); // Limpiar permisos actuales
        vistas.forEach(function (vista) {
            var isChecked = permisos.includes(vista.nombre) ? 'checked' : '';
            var listItem = $('<li></li>').text(vista.nombre + ' ');
            var checkbox = $('<input type="checkbox" onchange=" return changePermission(permiso_' + vista.id + ')" class="checkboxInput">')
                .attr('id', 'permiso_' + vista.id)
                .attr('value', vista.nombre)
                .prop('checked', isChecked);
            var label = $('<label class="toggleSwitch"></label>')
                .attr('for', 'permiso_' + vista.id)
                .attr('onchange', ' return changePermission();');
            listItem.append(checkbox).append(label);
            permissionsList.append(listItem);
        });
    }
    function getUserDetails(uid) {
        $.ajax({
            type: "POST",
            url: "/Bpanel",
            data: { uid: uid },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response) {
                    $("#userId").val(response.datos.id);
                    $("#userName").html("<strong>Nombre:</strong> " + response.datos.nombre);
                    $("#userLastName").html("<strong>Apellido:</strong> " + response.datos.apellido);
                    $("#userEmail").html("<strong>Email:</strong> " + response.datos.username);
                    $("#fecha_nacimiento").val(response.datos.fecha_nacimiento);
                    $("#userAge").html("<strong>Edad: " + response.datos.edad + " años</strong>");
                    loadPermissions(response.vistas, response.permisos);
                } else {
                    console.warn("No se encontraron datos para el usuario con ID: " + uid);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar detalles del usuario: " + status + " - " + error);
            }
        });
    }
    $("#search_user").on('input', function () {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("search_user");
        filter = input.value.toUpperCase();
        ul = document.getElementById("users");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("p")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    });
    function changePermission(element) { //Llamada a función asincrona para cambiar el permiso cambiado (false or true)
        let uid = parseInt($("#userId").val());
        let permissionId = parseInt(element.id.split('_')[1]);
        const parameter = {
            "permId": permissionId,
            "selectedUserId": uid,
        };
        $.ajax({ //Envio de solicitud a back, envia id_usuario y id_vista
            url: '/BchangePermission',
            type: 'POST',
            data: parameter,
            success: function (response) {
                $(".toggleSwitch").attr('disabled', true);
                $("input[type='checkbox']").attr('disabled', true);
                console.log(response);
                setTimeout(() => { //timeout para evitar flooding de envio de formulario
                    $(".toggleSwitch").attr('disabled', false);
                    $("input[type='checkbox']").attr('disabled', false);
                }, 1000);
            },
            error: function () { //fallback en caso de que no exista conexión al backend
                $("#report").prepend("error");
                $('#report > span').slice(1).remove();
            }
        });
    }
    function clickEliminar(uid) {
        dropUserId = parseInt(uid);
        console.log(dropUserId + '- Doble click en botón eliminar');
        data = {
            dropUId: dropUserId,
        };
        $.ajax({
            type: "POST",
            url: "/disableUser",
            data: data,
            dataType: "json",
            success: function (response) {
                console.log(response);
                location.href = location.pathname
            },
            error: function (xhr, status, error) {
                console.error("Error al realizar cambio a usuario: " + status + " - " + error);
            }
        });
    }
</script>