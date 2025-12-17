<?php
class GetToolsData
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function handle($action, $data)
    {
        switch($action) {
            case 'getAll':
                return $this->listar($data);
            case 'getTipos':
                return $this->obtenerTipos($data);
            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        }
    }

    private function listar($data)
    {
        try {
            $filtros = $data['filtros'] ?? [];
            
            $sql = "SELECT h.*, ht.nombre_herramienta as tipo_nombre 
                    FROM herramientas h 
                    LEFT JOIN herramientas_tipos ht ON h.id_tipo_herramienta = ht.id 
                    WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros
            if (!empty($filtros['nombre'])) {
                $sql .= " AND h.nombre LIKE :nombre";
                $params[':nombre'] = '%' . $filtros['nombre'] . '%';
            }
            
            if (!empty($filtros['marca'])) {
                $sql .= " AND h.marca LIKE :marca";
                $params[':marca'] = '%' . $filtros['marca'] . '%';
            }
            
            if (!empty($filtros['modelo'])) {
                $sql .= " AND h.modelo LIKE :modelo";
                $params[':modelo'] = '%' . $filtros['modelo'] . '%';
            }
            
            if (!empty($filtros['estado'])) {
                $sql .= " AND h.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            if (!empty($filtros['id_tipo_herramienta'])) {
                $sql .= " AND h.id_tipo_herramienta = :id_tipo_herramienta";
                $params[':id_tipo_herramienta'] = $filtros['id_tipo_herramienta'];
            }
            
            if (!empty($filtros['fecha_compra'])) {
                $sql .= " AND h.fecha_compra = :fecha_compra";
                $params[':fecha_compra'] = $filtros['fecha_compra'];
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $herramientas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['status' => 'success', 'data' => $herramientas]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al buscar herramientas: ' . $e->getMessage()]);
        }
    }

    private function obtenerTipos($data)
    {
        try {
            $sql = "SELECT * FROM herramientas_tipos ORDER BY nombre_herramienta";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['status' => 'success', 'data' => $tipos]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener tipos: ' . $e->getMessage()]);
        }
    }
}