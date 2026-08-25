<?php

require_once "../Config/database.php";

header("Content-Type: application/json; charset=UTF-8");

$con = connection();


try {

    /* =====================================================
       VALIDAR ID
    ====================================================== */

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );


    if (!$id) {

        echo json_encode([
            "ok" => false,
            "mensaje" => "ID de visitante inválido."
        ], JSON_UNESCAPED_UNICODE);

        exit;

    }


    /* =====================================================
       VERIFICAR REGISTRO
    ====================================================== */

    $sql = "
        SELECT
            id,
            salida
        FROM visitantes
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":id" => $id
    ]);

    $visitante = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$visitante) {

        echo json_encode([
            "ok" => false,
            "mensaje" => "El visitante no existe."
        ], JSON_UNESCAPED_UNICODE);

        exit;

    }


    /* =====================================================
       EVITAR REGISTRAR DOS VECES LA SALIDA
    ====================================================== */

    if (
        !empty($visitante['salida']) &&
        $visitante['salida'] !== '00:00:00'
    ) {

        echo json_encode([
            "ok" => false,
            "mensaje" => "La salida de este visitante ya fue registrada."
        ], JSON_UNESCAPED_UNICODE);

        exit;

    }


    /* =====================================================
       HORA ACTUAL
    ====================================================== */

    $horaSalida = date("H:i:s");


    /* =====================================================
       ACTUALIZAR SALIDA
    ====================================================== */

    $sql = "
        UPDATE visitantes
        SET salida = :salida
        WHERE id = :id
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([

        ":salida" => $horaSalida,

        ":id" => $id

    ]);


    /* =====================================================
       RESPUESTA
    ====================================================== */

    echo json_encode([

        "ok" => true,

        "hora_salida" => $horaSalida,

        "mensaje" =>
            "Salida registrada correctamente."

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {


    http_response_code(500);


    echo json_encode([

        "ok" => false,

        "mensaje" =>
            "Error de base de datos: " .
            $e->getMessage()

    ], JSON_UNESCAPED_UNICODE);

}
