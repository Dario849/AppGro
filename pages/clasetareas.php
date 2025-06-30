<?php
class Tareas {
    private $conexion;

    public function __construct($servidor, $usuario, $password, $bd) {
        $this->conexion = new mysqli($servidor, $usuario, $password, $bd);
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        $this->conexion->set_charset("utf8");
    }

    public function obtenerTareas($estado = null) {
        $sql = "SELECT id, texto, fecha_hora_inicio, fecha_hora_fin, estado FROM tareas";
        $params = [];

        if ($estado) {
            $sql .= " WHERE estado = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $estado);
        } else {
            $stmt = $this->conexion->prepare($sql);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        $tareas = [];
        while ($row = $res->fetch_assoc()) {
            $tareas[] = [
                'id' => (int)$row['id'],
                'texto' => $row['texto'],
                'inicio' => $row['fecha_hora_inicio'],
                'fin' => $row['fecha_hora_fin'],
                'estado' => $row['estado']
            ];
        }

        return $tareas;
    }

    public function insertarTarea($texto, $inicio, $fin) {
        $estado = 'activa';
        $sql = "INSERT INTO tareas (texto, fecha_hora_inicio, fecha_hora_fin, estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssss", $texto, $inicio, $fin, $estado);
        return $stmt->execute();
    }

    public function actualizarTarea($id, $campo, $valor) {
        $permitidos = ['texto', 'fecha_hora_inicio', 'fecha_hora_fin', 'estado'];
        if (!in_array($campo, $permitidos)) return false;

        $sql = "UPDATE tareas SET $campo = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $valor, $id);
        return $stmt->execute();
    }
}
?>
