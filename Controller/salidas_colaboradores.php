<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once '../Config/database.php';

try {

    $con = connection();

    /* ==========================================================
       USUARIO QUE REGISTRA LA SALIDA
    ========================================================== */

    $realizoSalida = $_SESSION['nombre']
        ?? $_SESSION['usuario']
        ?? '';

    $realizoSalida = trim($realizoSalida);

    if ($realizoSalida === '') {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'No se pudo identificar el usuario que registra la salida.'
        ]);

        exit;
    }


    /* ==========================================================
       RECIBIR ID
    ========================================================== */

    $id = filter_input(
        INPUT_POST,
        'id_registro',
        FILTER_VALIDATE_INT
    );

    if (!$id || $id <= 0) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'ID de registro inválido.'
        ]);

        exit;
    }


    /* ==========================================================
       BUSCAR REGISTRO
    ========================================================== */

    $sql = "
        SELECT
            id_registro,
            salida
        FROM colaboradores
        WHERE id_registro = :id
        LIMIT 1
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $registro = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$registro) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'El colaborador no existe.'
        ]);

        exit;
    }


    /* ==========================================================
       VERIFICAR SI YA TIENE SALIDA
    ========================================================== */

    if (
        !empty($registro['salida']) &&
        $registro['salida'] !== '00:00:00'
    ) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'El colaborador ya tiene registrada una hora de salida.'
        ]);

        exit;
    }


    /* ==========================================================
       HORA ACTUAL
    ========================================================== */

    $horaSalida = date('H:i:s');


    /* ==========================================================
       ACTUALIZAR SALIDA Y USUARIO
    ========================================================== */

    $sql = "
        UPDATE colaboradores
        SET
            salida = :salida,
            realizo_salida = :realizo_salida
        WHERE id_registro = :id
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ':salida'         => $horaSalida,
        ':realizo_salida' => $realizoSalida,
        ':id'             => $id
    ]);


    /* ==========================================================
       RESPUESTA
    ========================================================== */

    echo json_encode([
        'ok'              => true,
        'hora_salida'     => $horaSalida,
        'realizo_salida'  => $realizoSalida,
        'mensaje'         => 'Salida registrada correctamente.'
    ]);

    exit;


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error de base de datos.',
        'error' => $e->getMessage()
    ]);

    exit;
}
