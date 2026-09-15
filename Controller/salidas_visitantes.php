<?php

/* ==========================================================
   SALIDA DE VISITANTES
========================================================== */

session_start();

require_once "../Config/database.php";

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   CONEXIÓN
========================================================== */

try {

    $con = connection();

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([

        "ok" => false,

        "mensaje" =>
            "No fue posible conectar con la base de datos."

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

            "ok" => false,

            "mensaje" =>
                "No se pudo identificar el usuario que registra la salida."

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VALIDAR ID
    ========================================================== */

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );


    if (!$id || $id <= 0) {

        echo json_encode([

            "ok" => false,

            "mensaje" =>
                "ID de visitante inválido."

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       BUSCAR VISITANTE
    ========================================================== */

    $sql = "
        SELECT
            id,
            nombre,
            salida,
            realizo_salida
        FROM visitantes
        WHERE id = :id
        LIMIT 1
    ";


    $stmt = $con->prepare($sql);


    $stmt->execute([

        ":id" =>
            $id

    ]);


    $visitante = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    /* ==========================================================
       VERIFICAR EXISTENCIA
    ========================================================== */

    if (!$visitante) {

        echo json_encode([

            "ok" => false,

            "mensaje" =>
                "El visitante no existe."

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VERIFICAR SALIDA EXISTENTE
    ========================================================== */

    $salidaExistente = trim(
        (string) ($visitante['salida'] ?? '')
    );


    if (
        $salidaExistente !== '' &&
        $salidaExistente !== '00:00:00'
    ) {

        echo json_encode([

            "ok" => false,

            "mensaje" =>
                "La salida de este visitante ya fue registrada."

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       ZONA HORARIA
    ========================================================== */

    date_default_timezone_set(
        "America/Bogota"
    );


    /* ==========================================================
       HORA ACTUAL
    ========================================================== */

    $horaSalida = date(
        "H:i:s"
    );


    /* ==========================================================
       ACTUALIZAR SALIDA Y USUARIO
       
       IMPORTANTE:
       Solo actualiza si todavía no existe salida.
    ========================================================== */

    $sql = "
        UPDATE visitantes

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

        ":salida" =>
            $horaSalida,

        ":realizo_salida" =>
            $realizoSalida,

        ":id" =>
            $id

    ]);


    /* ==========================================================
       VERIFICAR ACTUALIZACIÓN
    ========================================================== */

    if ($stmt->rowCount() === 0) {

        echo json_encode([

            "ok" => false,

            "mensaje" =>
                "La salida ya fue registrada o el visitante no está disponible."

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       RESPUESTA
    ========================================================== */

    echo json_encode([

        "ok" =>
            true,

        "hora_salida" =>
            $horaSalida,

        "realizo_salida" =>
            $realizoSalida,

        "mensaje" =>
            "Salida registrada correctamente."

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {


    /* ==========================================================
       ERROR BASE DE DATOS
    ========================================================== */

    http_response_code(500);


    echo json_encode([

        "ok" =>
            false,

        "mensaje" =>
            "Error de base de datos.",

        "error" =>
            $e->getMessage()

    ], JSON_UNESCAPED_UNICODE);

} finally {


    /* ==========================================================
       CERRAR RECURSOS
    ========================================================== */

    $stmt = null;

    $con = null;

}