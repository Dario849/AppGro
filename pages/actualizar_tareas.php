<?php
require_once 'clasetareas.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["id"], $data["campo"], $data["valor"])) {
    echo json_encode(["success" => false, "error" => "Faltan datos"]);
    exit;
}

$tareas = new Tareas("localhost", "root", "", "app_campo");
$resultado = $tareas->actualizarTarea($data["id"], $data["campo"], $data["valor"]);

echo json_encode(["success" => $resultado]);
?>
