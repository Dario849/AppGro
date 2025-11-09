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
                ':usuario_responsable' => $_SESSION['user_id'],
                ':descripcion' => $data['descripcion'],
                ':horas_uso' => $data['horas_uso'] ?? 0,
                ':fecha' => $data['fecha']
            ]);
            
            $idHistorial = $this->pdo->lastInsertId();

            // Procesar imágenes si existen
            if (!empty($_FILES['imagenes'])) {
                $imagenesSubidas = $this->procesarImagenes($_FILES['imagenes'], $idHistorial, $_SESSION['user_id']);
                
                foreach ($imagenesSubidas as $rutaImagen) {
                    // Insertar en Imagenes con la ruta del archivo
                    $sqlImagen = "INSERT INTO imagenes (ruta_imagen, formato, id_usuario_responsable) 
                                 VALUES (:ruta_imagen, :formato, :usuario_responsable)";
                    $stmt = $this->pdo->prepare($sqlImagen);
                    
                    $formato = pathinfo($rutaImagen, PATHINFO_EXTENSION);
                    $stmt->execute([
                        ':ruta_imagen' => $rutaImagen,
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
                ':id_herramienta' => $data['id_herramienta'],
            ]);

            $this->pdo->commit();
            echo json_encode([
                'status' => 'success', 
                'message' => 'Historial agregado correctamente',
                'id_historial' => $idHistorial
            ]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar historial: ' . $e->getMessage()]);
        }
    }
//Alacena en servidor la imagen, devuelve ruta a mostrar públicamente
    private function procesarImagenes($archivosImagenes, $idHistorial, $idUsuario)
    {
        $rutasImagenes = [];
        $directorioBase = "public/uploads/herramientas/";
        $directorioPublico = "../uploads/herramientas/";
        
        // Crear directorio si no existe
        if (!is_dir($directorioBase)) {
            mkdir($directorioBase, 0755, true);
        }

        // Validar que no exceda el límite de 5 imágenes
        $totalImagenes = count($archivosImagenes['name']);
        if ($totalImagenes > 5) {
            throw new Exception('Máximo 5 imágenes permitidas');
        }

        for ($i = 0; $i < $totalImagenes; $i++) {
            if ($archivosImagenes['error'][$i] === UPLOAD_ERR_OK) {
                $nombreOriginal = $archivosImagenes['name'][$i];
                $temporal = $archivosImagenes['tmp_name'][$i];
                $tipo = $archivosImagenes['type'][$i];
                $tamano = $archivosImagenes['size'][$i];
                
                // Validar tipo de archivo
                $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($tipo, $tiposPermitidos)) {
                    throw new Exception("Tipo de archivo no permitido: $nombreOriginal");
                }
                
                // Validar tamaño (5MB máximo)
                $maxTamano = 5 * 1024 * 1024;
                if ($tamano > $maxTamano) {
                    throw new Exception("Imagen demasiado grande: $nombreOriginal");
                }
                
                // Generar nombre único para el archivo
                $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
                $nombreUnico = "historial_{$idHistorial}_usuario_{$idUsuario}_" . time() . "_{$i}." . $extension;
                $rutaDestino = $directorioBase . $nombreUnico;
                $rutaDestinoPublic = $directorioPublico . $nombreUnico;
                
                // Mover archivo al directorio de uploads
                if (move_uploaded_file($temporal, $rutaDestino)) {
                    $rutasImagenes[] = $rutaDestinoPublic;
                } else {
                    throw new Exception("Error al guardar la imagen: $nombreOriginal");
                }
            }
        }
        
        return $rutasImagenes;
    }

    private function obtenerHistorial($data)
    {
        try {
            $idHerramienta = $data['id_herramienta'];
            
            // Consulta mejorada para obtener información completa de imágenes
            $sql = "SELECT 
                    hh.*, 
                    u.nombre as usuario_nombre,
                    (SELECT GROUP_CONCAT(CONCAT(i.id, ':', i.ruta_imagen) SEPARATOR ',') 
                     FROM herramientas_imagenes ih 
                     JOIN imagenes i ON ih.id_imagen = i.id 
                     WHERE ih.id_herramienta_historial = hh.id) as imagenes_data
                   FROM herramientas_historial hh 
                   LEFT JOIN usuarios u ON hh.id_usuario_responsable = u.id 
                   WHERE hh.id_herramienta = :id_herramienta 
                   ORDER BY hh.fecha DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_herramienta' => $idHerramienta]);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar datos de imágenes para formato más usable
            $historialProcesado = array_map(function($item) {
                if (!empty($item['imagenes_data'])) {
                    $imagenesArray = [];
                    $pares = explode(',', $item['imagenes_data']);
                    foreach ($pares as $par) {
                        list($idImagen, $rutaImagen) = explode(':', $par, 2);
                        $imagenesArray[] = [
                            'id' => $idImagen,
                            'ruta' => $rutaImagen,
                            'nombre' => basename($rutaImagen)
                        ];
                    }
                    $item['imagenes'] = $imagenesArray;
                } else {
                    $item['imagenes'] = [];
                }
                unset($item['imagenes_data']);
                return $item;
            }, $historial);
            
            echo json_encode(['status' => 'success', 'data' => $historialProcesado]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener historial: ' . $e->getMessage()]);
        }
    }
}