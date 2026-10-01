<?php

session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once '../Config/database.php';

try {

    /* ==========================================================
       VALIDAR MÉTODO
    ========================================================== */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'Método no permitido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       OBTENER Y VALIDAR ID
    ========================================================== */

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id || $id <= 0) {

        http_response_code(400);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'ID de registro no válido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       USUARIO QUE REGISTRA LA SALIDA
    ========================================================== */

    $realizoSalida = trim(
        $_SESSION['nombre'] ??
        $_SESSION['usuario'] ??
        ''
    );

    if ($realizoSalida === '') {

        http_response_code(401);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'No se pudo identificar al usuario que registra la salida.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       CONEXIÓN
    ========================================================== */

    $con = connection();


    /* ==========================================================
       BUSCAR VEHÍCULO
    ========================================================== */

    $sql = "
        SELECT
            id_registro,
            hora_salida,
            realizo_salida
        FROM vehiculos
        WHERE id_registro = :id
        LIMIT 1
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);


    /* ==========================================================
       VALIDAR EXISTENCIA
    ========================================================== */

    if (!$vehiculo) {

        http_response_code(404);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'El registro del vehículo no existe.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VALIDAR SI YA TIENE SALIDA
    ========================================================== */

    $horaSalidaExistente = trim(
        $vehiculo['hora_salida'] ?? ''
    );

    if (
        $horaSalidaExistente !== '' &&
        $horaSalidaExistente !== '00:00:00'
    ) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'La salida ya fue registrada.',
            'hora_salida' => $horaSalidaExistente,
            'realizo_salida' => $vehiculo['realizo_salida'] ?? ''
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       DATOS DE SALIDA
    ========================================================== */

    $horaSalida = date('H:i:s');


    /* ==========================================================
       ACTUALIZAR SALIDA
    ========================================================== */

    $sql = "
        UPDATE vehiculos
        SET
            hora_salida = :hora_salida,
            realizo_salida = :realizo_salida
        WHERE id_registro = :id
          AND hora_salida = '00:00:00'
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':hora_salida' => $horaSalida,
        ':realizo_salida' => $realizoSalida,
        ':id' => $id
    ]);


    /* ==========================================================
       VALIDAR ACTUALIZACIÓN
    ========================================================== */

    if ($stmt->rowCount() === 0) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'No fue posible registrar la salida. Puede que otro usuario ya la haya registrado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       RESPUESTA EXITOSA
    ========================================================== */

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Salida registrada correctamente.',
        'hora_salida' => $horaSalida,
        'realizo_salida' => $realizoSalida
    ], JSON_UNESCAPED_UNICODE);

    exit;

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error de base de datos al registrar la salida.'
    ], JSON_UNESCAPED_UNICODE);

    exit;

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error interno del servidor.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}