<?php
require('system/main.php');

sessionCheck();
renderNavbar();
$layout = new HTML(title: 'Ganado UwU');
require dirname(__DIR__, 2) .'\system\resources\database.php';
//require dirname(__DIR__,2) .'\system\ganados\Bganados.php';

//obtener id_ganado desde la url
$id_ganado = isset($_POST['id']) ? intval($_POST['id']) : 0;
$ganado = [];
if ($id_ganado > 0) {
    $ganado = obtenerGanadoPorId($pdo, $id_ganado);
}
?>
    <form method="post" action="/ganado">
        
        <div id="contenedor">
            <div id="datos">

            <input type="hidden" name="id" value="1">


                <fieldset>
                    <legend>Características</legend>
                    Nacimiento: <input type="date" name="nacimiento" value="<?= $ganado['fecha_nacimiento'] ?? '' ?>"><br>
                    Tipo: <input type="text" name="tipo" value="<?= $ganado['tipo_ganado'] ?? '' ?>"><br>
                    Raza: <input type="text" name="raza" value="<?= $ganado['nombre_cientifico'] ?? '' ?>"><br>
                    Sexo: <input type="text" name="sexo" value="<?= $ganado['sexo'] ?? '' ?>"><br>
                </fieldset>
                
                <fieldset>
                    <legend>Ubicación</legend>
                    Caravana: <input type="text" name="caravana" value="<?= $ganado['nro_caravana'] ?? '' ?>"><br>
                    Subdivisión: <input type="text" name="subdivision" value="<?= $ganado['id_grupo'] ?? '' ?>"><br>
                    Grupo: <input type="text" name="grupo" value="<?= $ganado['id_grupo'] ?? '' ?>"><br>
                </fieldset>
                
                <fieldset>
                    <legend>Alimento</legend>
                    Tipo: <input type="text" name="alimento_tipo"><br>
                    Ración: <input type="text" name="alimento_racion"><br>
                </fieldset>
                
                <fieldset>
                    <legend>Defunción</legend>
                    Razón: <input type="text" name="defuncion_razon"><br>
                </fieldset>
                
            </div>
            <div id="vacunas_y_baños">
            
                <fieldset>
                    <legend>Vacunas</legend>
                    <div id="vacunas">
          <div>
              <input type="text" name="vacuna_id[]" placeholder="ID">
              <input type="text" name="vacuna_nombre[]" placeholder="Nombre">
              <input type="date" name="vacuna_fecha[]"><br>
            </div>
        </div>
        <button type="button" onclick="agregarVacuna()">Agregar Vacuna</button>
    </fieldset>
    
    <fieldset>
        <legend>Baños</legend>
        <div id="banos">
            <div>
                <input type="text" name="bano_id[]" placeholder="ID">
                <input type="date" name="bano_fecha[]"><br>
            </div>
        </div>
        <button type="button" onclick="agregarBano()">Agregar Baño</button>
    </fieldset>

    <fieldset>
        <legend>Pesos</legend>
        <div id="pesos">
            <div>
                <input type="text" name="peso_kg[]" value="<?= $ganado['peso'] ?? '' ?>" placeholder="Kg">
                <input type="date" name="peso_fecha[]"><br>
            </div>
        </div>
        <button type="button" onclick="agregarPeso()">Agregar Peso</button>
    </fieldset>

</div>
<div id="terceracolumna">
        
        <fieldset>
            <legend>Imagen</legend>
            <img  src="../public/imagenes_ganados/vaca.jpg" alt="Imagen de vaca" width="300">
        </fieldset>
        
        <fieldset>
            <legend>Comentario</legend>
            <textarea name="comentario" rows="5" cols="40"><?= $ganado['comentario'] ?? '' ?></textarea>
        </fieldset>
        
        </div>

    </div>
    <input type="submit" value="Guardar">

</form>

<style>
    #contenedor {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    #datos {
        width: 33%;
    }

    #vacunas_y_baños {
        width: 33%;
    }

    #terceracolumna {
        width: 33%;
    }

    fieldset {
        border: 2px solid #007bff;
        /* Define un borde de 2px de ancho, sólido y de color azul */
        border-radius: 10px;
        /* Redondea las esquinas del borde */
        background-color: #f0f0f0;
        /* Define un color de fondo gris claro */
        padding: 10px;
        /* Agrega un relleno interno al fieldset */
    }
</style>