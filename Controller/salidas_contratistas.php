<?php

/* ==========================================================
   SALIDA DE CONTRATISTAS
========================================================== */

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once "../Config/database.php";


/* ==========================================================
   CONEXIÓN
========================================================== */

try {

    $con = connection();

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => 'No fue posible conectar con la base de datos.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    /* ==========================================================
       USUARIO QUE REGISTRA LA SALIDA
    ========================================================== */

    $realizoSalida =
        $_SESSION['nombre']
        ?? $_SESSION['usuario']
        ?? '';

    $realizoSalida = trim(
        (string) $realizoSalida
    );


    if ($realizoSalida === '') {

        echo json_encode([
            'ok' => false,
            'mensaje' =>
                'No se pudo identificar el usuario que registra la salida.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       RECIBIR ID
    ========================================================== */

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );


    if (!$id || $id <= 0) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'ID de contratista inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       BUSCAR CONTRATISTA
    ========================================================== */

    $sql = "
        SELECT
            id,
            nombre,
            salida,
            realizo_salida
        FROM contratistas
        WHERE id = :id
        LIMIT 1
    ";


    $stmt = $con->prepare($sql);


    $stmt->execute([
        ':id' => $id
    ]);


    $registro = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    /* ==========================================================
       VERIFICAR EXISTENCIA
    ========================================================== */

    if (!$registro) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'El contratista no existe.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VERIFICAR SALIDA EXISTENTE
    ========================================================== */

    $salidaExistente = trim(
        (string) ($registro['salida'] ?? '')
    );


    if (
        $salidaExistente !== '' &&
        $salidaExistente !== '00:00:00'
    ) {

        echo json_encode([
            'ok' => false,
            'mensaje' =>
                'El contratista ya tiene registrada una hora de salida.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       HORA ACTUAL
    ========================================================== */

    date_default_timezone_set(
        'America/Bogota'
    );


    $horaSalida = date('H:i:s');


    /* ==========================================================
       ACTUALIZAR
       SOLO SI TODAVÍA NO EXISTE SALIDA
    ========================================================== */

    $sql = "
        UPDATE contratistas

        SET
            salida = :salida,
            realizo_salida = :realizo_salida

        WHERE id = :id

        AND (
            salida IS NULL
            OR salida = ''
            OR salida = '00:00:00'
        )
    ";


    $stmt = $con->prepare($sql);


    $stmt->execute([

        ':salida' =>
            $horaSalida,

        ':realizo_salida' =>
            $realizoSalida,

        ':id' =>
            $id

    ]);


    /* ==========================================================
       VERIFICAR ACTUALIZACIÓN
    ========================================================== */

    if ($stmt->rowCount() === 0) {

        echo json_encode([
            'ok' => false,
            'mensaje' =>
                'La salida ya fue registrada o el registro no está disponible.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       RESPUESTA
    ========================================================== */

    echo json_encode([

        'ok' =>
            true,

        'hora_salida' =>
            $horaSalida,

        'realizo_salida' =>
            $realizoSalida,

        'mensaje' =>
            'Salida registrada correctamente.'

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {


    /* ==========================================================
       ERROR PDO
    ========================================================== */

    http_response_code(500);


    echo json_encode([

        'ok' =>
            false,

        'mensaje' =>
            'Error de base de datos.',

        'error' =>
            $e->getMessage()

    ], JSON_UNESCAPED_UNICODE);


} finally {


    /* ==========================================================
       CERRAR RECURSOS
    ========================================================== */

    $stmt = null;

    $con = null;

}
