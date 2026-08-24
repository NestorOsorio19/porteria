<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once '../Config/database.php';

try {

    $con = connection();


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

    $stmt = $con->prepare("
        SELECT
            id_registro,
            salida
        FROM colaboradores
        WHERE id_registro = :id
        LIMIT 1
    ");

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
            'mensaje' =>
                'El colaborador ya tiene registrada una hora de salida.'
        ]);

        exit;
    }


    /* ==========================================================
       HORA ACTUAL
    ========================================================== */

    $horaSalida = date('H:i:s');


    /* ==========================================================
       ACTUALIZAR SALIDA
    ========================================================== */

    $stmt = $con->prepare("
        UPDATE colaboradores
        SET salida = :salida
        WHERE id_registro = :id
    ");

    $stmt->execute([
        ':salida' => $horaSalida,
        ':id' => $id
    ]);


    /* ==========================================================
       RESPUESTA
    ========================================================== */

    echo json_encode([

        'ok' => true,

        'hora_salida' => $horaSalida,

        'mensaje' =>
            'Salida registrada correctamente.'

    ]);

    exit;


} catch (PDOException $e) {


    http_response_code(500);


    echo json_encode([

        'ok' => false,

        'mensaje' =>
            'Error de base de datos.',

        'error' =>
            $e->getMessage()

    ]);

    exit;

}
