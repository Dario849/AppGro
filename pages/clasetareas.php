<?php
class Tareas {
    private $pdo;


    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function obtenerTareas($estado = null) {
        $sql = "SELECT id, texto, fecha_hora_inicio, fecha_hora_fin, estado FROM tareas WHERE baja_logica = 0";
        $params = [];

        if ($estado && $estado !== 'todas') {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarTarea($texto, $inicio, $fin) {
        $sql = "INSERT INTO tareas (texto, fecha_hora_inicio, fecha_hora_fin, estado) 
                VALUES (:texto, :inicio, :fin, 'activa')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':texto' => $texto,
            ':inicio' => $inicio,
            ':fin' => $fin
        ]);
    }

    public function actualizarTarea($id, $campo, $valor) {
        $permitidos = ['texto', 'fecha_hora_inicio', 'fecha_hora_fin', 'estado'];
        if (!in_array($campo, $permitidos)) return false;

        $sql = "UPDATE tareas SET $campo = :valor WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':valor' => $valor,
            ':id' => $id
        ]);
    }

    // Ejemplo: filtro por mes y año
    public function obtenerTareasPorFecha($mes, $anio, $estado = null) {
        $sql = "SELECT id, texto, fecha_hora_inicio, fecha_hora_fin, estado FROM tareas WHERE baja_logica = 0";
        $params = [];

        if ($estado && $estado !== 'todas') {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($mes > 0 && $anio > 0) {
            $fechaInicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
            $fechaFin = date("Y-m-t", strtotime($fechaInicio));
            $sql .= " AND fecha_hora_inicio BETWEEN :inicio AND :fin";
            $params[':inicio'] = $fechaInicio;
            $params[':fin'] = $fechaFin;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
