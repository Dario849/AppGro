<?php
session_start();
class HistorialToolsData
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function handle($action, $data)
    {
        switch($action) {
            case 'agregarHistorial':
                return $this->agregarHistorial($data);
            case 'getHistorial':
                return $this->obtenerHistorial($data);
            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        }
    }

    private function agregarHistorial($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Insertar en Historial_Herramienta
            $sqlHistorial = "INSERT INTO herramientas_historial 
                            (id_herramienta, id_usuario_responsable, descripcion, horas_uso, fecha) 
                            VALUES (:id_herramienta, :usuario_responsable, :descripcion, :horas_uso, :fecha)";
            
            $stmt = $this->pdo->prepare($sqlHistorial);
            $stmt->execute([
                ':id_herramienta' => $data['id_herramienta'],
                ':usuario_responsable' => $_SESSION['user_id'], // Asumiendo que existe sesión
                ':descripcion' => $data['descripcion'],
                ':horas_uso' => $data['horas_uso'] ?? 0,
                ':fecha' => $data['fecha']
            ]);
            
            $idHistorial = $this->pdo->lastInsertId();

            // Procesar imágenes si existen
            if (!empty($_FILES['imagenes'])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
                    if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK) {
                        $imagenData = file_get_contents($tmpName);
                        $formato = pathinfo($_FILES['imagenes']['name'][$index], PATHINFO_EXTENSION);
                        
                        // Insertar en Imagenes
                        $sqlImagen = "INSERT INTO imagenes (imagen, formato, id_usuario_responsable) 
                                     VALUES (:imagen, :formato, :usuario_responsable)";
                        $stmt = $this->pdo->prepare($sqlImagen);
                        $stmt->execute([
                            ':imagen' => $imagenData,
                            ':formato' => $formato,
                            ':usuario_responsable' => $_SESSION['user_id']
                        ]);
                        
                        $idImagen = $this->pdo->lastInsertId();
                        
                        // Relacionar imagen con historial
                        $sqlRelacion = "INSERT INTO herramientas_imagenes (id_herramienta_historial, id_imagen) 
                                       VALUES (:id_historial, :id_imagen)";
                        $stmt = $this->pdo->prepare($sqlRelacion);
                        $stmt->execute([
                            ':id_historial' => $idHistorial,
                            ':id_imagen' => $idImagen
                        ]);
                    }
                }
            }

            // Actualizar herramienta (horas totales y fecha último uso)
            $sqlUpdate = "UPDATE herramientas 
                         SET horas_uso_total = horas_uso_total + :horas_uso, 
                             fecha_ultimo_uso = :fecha,
                             id_ultimo_usuario_responsable = :usuario
                         WHERE id = :id_herramienta";
            
            $stmt = $this->pdo->prepare($sqlUpdate);
            $stmt->execute([
                ':horas_uso' => $data['horas_uso'] ?? 0,
                ':fecha' => $data['fecha'],
                ':usuario' => $_SESSION['user_id'],
                ':id_herramienta' => $data['id_herramienta']
            ]);

            $this->pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Historial agregado correctamente']);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar historial: ' . $e->getMessage()]);
        }
    }

    private function obtenerHistorial($data)
    {
        try {
            $idHerramienta = $data['id_herramienta'];
            
            $sql = "SELECT hh.*, u.nombre as usuario_nombre,
                   (SELECT GROUP_CONCAT(i.id) 
                    FROM herramientas_imagenes ih 
                    JOIN imagenes i ON ih.id_imagen = i.id 
                    WHERE ih.id_herramienta_historial = hh.id) as imagenes_ids
                   FROM herramientas_historial hh 
                   LEFT JOIN usuarios u ON hh.id_usuario_responsable = u.id 
                   WHERE hh.id_herramienta = :id_herramienta 
                   ORDER BY hh.fecha DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_herramienta' => $idHerramienta]);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['status' => 'success', 'data' => $historial]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener historial: ' . $e->getMessage()]);
        }
    }
}