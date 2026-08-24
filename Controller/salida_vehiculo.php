<?php

require_once '../Config/database.php';

header('Content-Type: application/json; charset=UTF-8');

try {

    // Solo permitir POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'Método no permitido.'
        ]);

        exit;
    }

    // Obtener ID
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'ID de registro no válido.'
        ]);

        exit;
    }

    // Conexión
    $con = connection();

    // Buscar registro
    $sql = "
        SELECT id_registro, hora_salida
        FROM vehiculos
        WHERE id_registro = :id
        LIMIT 1
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

    // No existe
    if (!$vehiculo) {

        http_response_code(404);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'El registro no existe.'
        ]);

        exit;
    }

    // Ya tiene salida
    if (
        isset($vehiculo['hora_salida']) &&
        $vehiculo['hora_salida'] !== '' &&
        $vehiculo['hora_salida'] !== '00:00:00'
    ) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'La salida ya fue registrada.',
            'hora_salida' => $vehiculo['hora_salida']
        ]);

        exit;
    }

    // Hora del servidor
    $horaSalida = date('H:i:s');

    // Actualizar
    $sql = "
        UPDATE vehiculos
        SET hora_salida = :hora_salida
        WHERE id_registro = :id
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':hora_salida' => $horaSalida,
        ':id' => $id
    ]);

    // Confirmar
    echo json_encode([
        'ok' => true,
        'mensaje' => 'Salida registrada correctamente.',
        'hora_salida' => $horaSalida
    ]);

    exit;

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error de base de datos.',
        'detalle' => $e->getMessage()
    ]);

    exit;

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error interno del servidor.',
        'detalle' => $e->getMessage()
    ]);

    exit;
}