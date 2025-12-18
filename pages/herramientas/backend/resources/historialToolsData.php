<?php
session_start();

/**
 * Clase para manejar el historial de herramientas
 * Gestiona registros de uso, imágenes y actualizaciones de estado
 */
class HistorialToolsData
{
    private $pdo;
    
    // Configuración de rutas y límites
    private const DIRECTORIO_BASE = '/var/www/public/uploads/herramientas/';
    private const DIRECTORIO_PUBLICO = '/public/uploads/herramientas/';
    private const MAX_IMAGENES = 5;
    private const MAX_TAMANO_MB = 5;
    private const TIPOS_PERMITIDOS = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    private const EXTENSIONES_PERMITIDAS = ['jpg', 'jpeg', 'png', 'gif'];

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->inicializarDirectorios();
    }

    /**
     * Inicializa y verifica los directorios necesarios
     */
    private function inicializarDirectorios()
    {
        try {
            if (!is_dir(self::DIRECTORIO_BASE)) {
                if (!mkdir(self::DIRECTORIO_BASE, 0775, true)) {
                    throw new Exception("No se pudo crear el directorio: " . self::DIRECTORIO_BASE);
                }
                
                // Establecer permisos correctos para www-data
                @chown(self::DIRECTORIO_BASE, 'www-data');
                @chgrp(self::DIRECTORIO_BASE, 'www-data');
                @chmod(self::DIRECTORIO_BASE, 0775);
            }
            
            // Verificar permisos de escritura
            if (!is_writable(self::DIRECTORIO_BASE)) {
                error_log("ADVERTENCIA: Directorio no escribible: " . self::DIRECTORIO_BASE);
            }
        } catch (Exception $e) {
            error_log("Error al inicializar directorios: " . $e->getMessage());
        }
    }

    /**
     * Maneja las diferentes acciones del módulo
     */
    public function handle($action, $data)
    {
        switch($action) {
            case 'agregarHistorial':
                return $this->agregarHistorial($data);
            case 'getHistorial':
                return $this->obtenerHistorial($data);
            default:
                http_response_code(400);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Acción no válida',
                    'action_received' => $action
                ]);
        }
    }

    /**
     * Agrega un nuevo registro de historial con sus imágenes
     */
    private function agregarHistorial($data)
    {
        try {
            // Validar sesión de usuario
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Usuario no autenticado');
            }

            // Validar datos requeridos
            $this->validarDatosHistorial($data);

            $this->pdo->beginTransaction();

            // 1. Insertar registro de historial
            $idHistorial = $this->insertarRegistroHistorial($data);

            // 2. Procesar y guardar imágenes si existen
            $imagenesGuardadas = [];
            if (!empty($_FILES['imagenes']['name'][0])) {
                $imagenesGuardadas = $this->procesarImagenes(
                    $_FILES['imagenes'], 
                    $idHistorial, 
                    $_SESSION['user_id']
                );
            }

            // 3. Actualizar información de la herramienta
            $this->actualizarHerramienta($data, $_SESSION['user_id']);

            $this->pdo->commit();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Historial agregado correctamente',
                'data' => [
                    'id_historial' => $idHistorial,
                    'imagenes_guardadas' => count($imagenesGuardadas),
                    'rutas_imagenes' => $imagenesGuardadas
                ]
            ]);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            error_log("Error en agregarHistorial: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Error al agregar historial: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Valida los datos del historial antes de insertarlos
     */
    private function validarDatosHistorial($data)
    {
        if (empty($data['id_herramienta'])) {
            throw new Exception('ID de herramienta requerido');
        }
        
        if (empty($data['fecha'])) {
            throw new Exception('Fecha requerida');
        }
        
        // Validar formato de fecha
        $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha']);
        if (!$fecha) {
            throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
        }
        
        // Validar horas de uso
        if (isset($data['horas_uso']) && (!is_numeric($data['horas_uso']) || $data['horas_uso'] < 0)) {
            throw new Exception('Horas de uso debe ser un número positivo');
        }
    }

    /**
     * Inserta el registro de historial en la base de datos
     */
    private function insertarRegistroHistorial($data)
    {
        $sqlHistorial = "INSERT INTO herramientas_historial 
                        (id_herramienta, id_usuario_responsable, descripcion, horas_uso, fecha) 
                        VALUES (:id_herramienta, :usuario_responsable, :descripcion, :horas_uso, :fecha)";
        
        $stmt = $this->pdo->prepare($sqlHistorial);
        $stmt->execute([
            ':id_herramienta' => $data['id_herramienta'],
            ':usuario_responsable' => $_SESSION['user_id'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':horas_uso' => $data['horas_uso'] ?? 0,
            ':fecha' => $data['fecha']
        ]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Procesa y guarda las imágenes subidas
     * Retorna array con las rutas públicas de las imágenes guardadas
     */
    private function procesarImagenes($archivosImagenes, $idHistorial, $idUsuario)
    {
        $rutasImagenes = [];
        
        // Validar estructura del array de archivos
        if (!isset($archivosImagenes['name']) || !is_array($archivosImagenes['name'])) {
            throw new Exception('Formato de archivos inválido');
        }

        // Validar cantidad de imágenes
        $totalImagenes = count($archivosImagenes['name']);
        if ($totalImagenes > self::MAX_IMAGENES) {
            throw new Exception('Máximo ' . self::MAX_IMAGENES . ' imágenes permitidas');
        }

        // Procesar cada imagen
        for ($i = 0; $i < $totalImagenes; $i++) {
            // Saltar si no hay archivo o hay error de subida (excepto UPLOAD_ERR_OK)
            if (empty($archivosImagenes['name'][$i]) || 
                $archivosImagenes['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            try {
                $rutaPublica = $this->procesarImagenIndividual(
                    $archivosImagenes, 
                    $i, 
                    $idHistorial, 
                    $idUsuario
                );
                
                $rutasImagenes[] = $rutaPublica;
                
            } catch (Exception $e) {
                error_log("Error procesando imagen {$i}: " . $e->getMessage());
                throw new Exception("Error en imagen '{$archivosImagenes['name'][$i]}': " . $e->getMessage());
            }
        }
        
        return $rutasImagenes;
    }

    /**
     * Procesa una imagen individual
     */
    private function procesarImagenIndividual($archivosImagenes, $indice, $idHistorial, $idUsuario)
    {
        $nombreOriginal = basename($archivosImagenes['name'][$indice]);
        $temporal = $archivosImagenes['tmp_name'][$indice];
        $tipo = $archivosImagenes['type'][$indice];
        $tamano = $archivosImagenes['size'][$indice];
        
        // Validar tipo MIME
        if (!in_array($tipo, self::TIPOS_PERMITIDOS)) {
            throw new Exception("Tipo de archivo no permitido: {$nombreOriginal}. Tipos permitidos: JPG, PNG, GIF");
        }
        
        // Validar extensión del archivo
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if (!in_array($extension, self::EXTENSIONES_PERMITIDAS)) {
            throw new Exception("Extensión no permitida: {$extension}");
        }
        
        // Validar tamaño
        $maxTamanoBytes = self::MAX_TAMANO_MB * 1024 * 1024;
        if ($tamano > $maxTamanoBytes) {
            $tamanoMB = round($tamano / (1024 * 1024), 2);
            throw new Exception("Imagen demasiado grande ({$tamanoMB}MB). Máximo: " . self::MAX_TAMANO_MB . "MB");
        }
        
        // Validar que sea una imagen real
        if (!getimagesize($temporal)) {
            throw new Exception("El archivo no es una imagen válida: {$nombreOriginal}");
        }
        
        // Generar nombre único y seguro
        $nombreUnico = sprintf(
            "historial_%d_usuario_%d_%d_%d.%s",
            $idHistorial,
            $idUsuario,
            time(),
            $indice,
            $extension
        );
        
        $rutaDestinoServidor = self::DIRECTORIO_BASE . $nombreUnico;
        $rutaPublica = self::DIRECTORIO_PUBLICO . $nombreUnico;
        
        // Mover archivo al directorio de uploads
        if (!move_uploaded_file($temporal, $rutaDestinoServidor)) {
            $error = error_get_last();
            $mensajeError = $error ? $error['message'] : 'Error desconocido';
            error_log("Error move_uploaded_file: {$mensajeError}");
            throw new Exception("Error al guardar la imagen en el servidor");
        }
        
        // Establecer permisos correctos
        @chmod($rutaDestinoServidor, 0664);
        @chown($rutaDestinoServidor, 'www-data');
        @chgrp($rutaDestinoServidor, 'www-data');
        
        // Guardar en base de datos
        $this->guardarImagenEnBD($rutaPublica, $extension, $idUsuario, $idHistorial);
        
        return $rutaPublica;
    }

    /**
     * Guarda la información de la imagen en la base de datos
     */
    private function guardarImagenEnBD($rutaPublica, $formato, $idUsuario, $idHistorial)
    {
        // Insertar en tabla imagenes
        $sqlImagen = "INSERT INTO imagenes (ruta_imagen, formato, id_usuario_responsable) 
                     VALUES (:ruta_imagen, :formato, :usuario_responsable)";
        $stmt = $this->pdo->prepare($sqlImagen);
        $stmt->execute([
            ':ruta_imagen' => $rutaPublica,
            ':formato' => $formato,
            ':usuario_responsable' => $idUsuario
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

    /**
     * Actualiza la información de la herramienta (horas totales y último uso)
     */
    private function actualizarHerramienta($data, $idUsuario)
    {
        $sqlUpdate = "UPDATE herramientas 
                     SET horas_uso_total = horas_uso_total + :horas_uso, 
                         fecha_ultimo_uso = :fecha,
                         id_ultimo_usuario_responsable = :usuario
                     WHERE id = :id_herramienta";
        
        $stmt = $this->pdo->prepare($sqlUpdate);
        $stmt->execute([
            ':horas_uso' => $data['horas_uso'] ?? 0,
            ':fecha' => $data['fecha'],
            ':usuario' => $idUsuario,
            ':id_herramienta' => $data['id_herramienta']
        ]);
    }

    /**
     * Obtiene el historial de una herramienta con sus imágenes
     */
    private function obtenerHistorial($data)
    {
        try {
            if (empty($data['id_herramienta'])) {
                throw new Exception('ID de herramienta requerido');
            }

            $idHerramienta = $data['id_herramienta'];
            
            // Consulta optimizada con información completa
            $sql = "SELECT 
                    hh.id,
                    hh.id_herramienta,
                    hh.id_usuario_responsable,
                    hh.descripcion,
                    hh.horas_uso,
                    hh.fecha,
                    u.nombre as usuario_nombre,
                    u.apellido as usuario_apellido,
                    (SELECT GROUP_CONCAT(CONCAT(i.id, ':', i.ruta_imagen, ':', i.formato) SEPARATOR '||') 
                     FROM herramientas_imagenes ih 
                     JOIN imagenes i ON ih.id_imagen = i.id 
                     WHERE ih.id_herramienta_historial = hh.id) as imagenes_data
                   FROM herramientas_historial hh 
                   LEFT JOIN usuarios u ON hh.id_usuario_responsable = u.id 
                   WHERE hh.id_herramienta = :id_herramienta 
                   ORDER BY hh.fecha DESC, hh.id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_herramienta' => $idHerramienta]);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar y formatear datos de imágenes
            $historialProcesado = array_map(function($item) {
                // Formatear nombre completo del usuario
                $item['usuario_nombre_completo'] = trim(
                    ($item['usuario_nombre'] ?? '') . ' ' . ($item['usuario_apellido'] ?? '')
                );
                
                // Procesar imágenes
                if (!empty($item['imagenes_data'])) {
                    $imagenesArray = [];
                    $pares = explode('||', $item['imagenes_data']);
                    
                    foreach ($pares as $par) {
                        $partes = explode(':', $par,);
                        if (count($partes) === 3) {
                            list($idImagen, $rutaImagen, $formato) = $partes;
                            $imagenesArray[] = [
                                'id' => (int)$idImagen,
                                'ruta' => $rutaImagen,
                                'formato' => $formato,
                                'nombre' => basename($rutaImagen),
                                'url_completa' => $rutaImagen // Para uso en frontend
                            ];
                        }
                    }
                    $item['imagenes'] = $imagenesArray;
                } else {
                    $item['imagenes'] = [];
                }
                
                // Limpiar datos temporales
                unset($item['imagenes_data']);
                unset($item['usuario_apellido']);
                
                // Formatear fecha para mejor legibilidad
                if (!empty($item['fecha'])) {
                    $fecha = new DateTime($item['fecha']);
                    $item['fecha_formateada'] = $fecha->format('d/m/Y');
                }
                
                return $item;
            }, $historial);
            
            echo json_encode([
                'status' => 'success', 
                'data' => $historialProcesado,
                'total_registros' => count($historialProcesado)
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerHistorial: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Error al obtener historial: ' . $e->getMessage()
            ]);
        }
    }
}
