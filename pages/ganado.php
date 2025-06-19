<?php
require('system/main.php');

sessionCheck();
renderNavbar();
$layout = new HTML(title: 'PHP via Vite');
//require dirname(__DIR__, 2) .'\system\resources\database.php';
/*$sql  = "SELECT nombre FROM Usuarios WHERE id BETWEEN :min AND :max";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'min' => 1,
    'max' => 5,
])*/
?>
    <script>
        function agregarVacuna() {
            const contenedor = document.getElementById('vacunas');
            const div = document.createElement('div');
            div.innerHTML = `
                <input type="text" name="vacuna_id[]" placeholder="ID">
                <input type="text" name="vacuna_nombre[]" placeholder="Nombre">
                <input type="date" name="vacuna_fecha[]">
                <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
                <br>
            `;
            contenedor.appendChild(div);
        }

        function agregarBano() {
            const contenedor = document.getElementById('banos');
            const div = document.createElement('div');
            div.innerHTML = `
                <input type="text" name="bano_id[]" placeholder="ID">
                <input type="date" name="bano_fecha[]">
                <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
                <br>
            `;
            contenedor.appendChild(div);
        }

        function agregarPeso() {
            const contenedor = document.getElementById('pesos');
            const div = document.createElement('div');
            div.innerHTML = `
                <input type="text" name="peso_kg[]" placeholder="Kg">
                <input type="date" name="peso_fecha[]">
                <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
                <br>
            `;
            contenedor.appendChild(div);
        }
        </script>
    <form method="POST" action="ganado.php">
        
        <div id="contenedor">
            <div id="datos">

                <fieldset>
                    <legend>Características</legend>
                    Nacimiento: <input type="date" name="nacimiento"><br>
                    Peso: <input type="text" name="peso"><br>
                    Tipo: <input type="text" name="tipo"><br>
                    Raza: <input type="text" name="raza"><br>
                    Sexo: <input type="text" name="sexo"><br>
                </fieldset>
                
                <fieldset>
                    <legend>Ubicación</legend>
                    Caravana: <input type="text" name="caravana"><br>
                    Subdivisión: <input type="text" name="subdivision"><br>
                    Grupo: <input type="text" name="grupo"><br>
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
                <input type="text" name="peso_kg[]" placeholder="Kg">
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
            <textarea name="comentario" rows="5" cols="40"></textarea>
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
        border: 2px solid #007bff; /* Define un borde de 2px de ancho, sólido y de color azul */
        border-radius: 10px; /* Redondea las esquinas del borde */
        background-color: #f0f0f0; /* Define un color de fondo gris claro */
        padding: 10px; /* Agrega un relleno interno al fieldset */
    }
</style>