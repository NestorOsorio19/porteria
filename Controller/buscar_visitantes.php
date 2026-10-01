<?php

require_once("../Config/database.php");

header('Content-Type: application/json; charset=utf-8');

/* =====================================================
   RESPUESTA JSON
===================================================== */

function responderJSON(array $respuesta, int $codigo = 200): void
{
    http_response_code($codigo);

    echo json_encode(
        $respuesta,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/* =====================================================
   PROCESAR
===================================================== */

try {

    /* =================================================
       CONEXIÓN
    ================================================= */

    $con = connection();

    if (!$con instanceof PDO) {
        throw new Exception(
            "Error de conexión a la base de datos"
        );
    }

    /* =================================================
       VALIDAR CÉDULA
    ================================================= */

    if (
        !isset($_POST['cedula']) ||
        trim($_POST['cedula']) === ''
    ) {

        responderJSON([
            'error' => true,
            'mensaje' => 'No se recibió la cédula'
        ], 400);
    }

    $cedula = trim($_POST['cedula']);

    /* =================================================
       VALIDAR QUE SEAN SOLO NÚMEROS
    ================================================= */

    if (!ctype_digit($cedula)) {

        responderJSON([
            'error' => true,
            'mensaje' => 'La cédula debe contener únicamente números'
        ], 400);
    }

    /* =================================================
       CONSULTAR VISITANTE
    ================================================= */

    $sql = "
    SELECT
        nombre,
        telefono,
        id_arl,
        id_eps,
        rh,
        contacto,
        numero_emergencia
    FROM visitantes
    WHERE cedula = :cedula
    ORDER BY
        (
            IF(telefono IS NOT NULL AND telefono <> '',1,0) +
            IF(contacto IS NOT NULL AND contacto <> '',1,0) +
            IF(numero_emergencia IS NOT NULL AND numero_emergencia <> '',1,0) +
            IF(id_arl IS NOT NULL,1,0) +
            IF(id_eps IS NOT NULL,1,0) +
            IF(rh IS NOT NULL AND rh <> '',1,0)
        ) DESC,
        id DESC
    LIMIT 1
";


    $stmt = $con->prepare($sql);

    $stmt->bindValue(
        ':cedula',
        (int) $cedula,
        PDO::PARAM_INT
    );

    $stmt->execute();

    $visitante = $stmt->fetch(PDO::FETCH_ASSOC);

    /* =================================================
       NO ENCONTRADO
    ================================================= */

    if (!$visitante) {

        responderJSON([
            'error' => true,
            'mensaje' => 'Visitante no encontrado'
        ], 404);
    }

    /* =================================================
       RESPUESTA
    ================================================= */

    responderJSON([

        'error' => false,

        'nombre' =>
        $visitante['nombre'] ?? '',

        'telefono' =>
        $visitante['telefono'] ?? '',

        'arl' =>
        $visitante['id_arl'] ?? '',

        'eps' =>
        $visitante['id_eps'] ?? '',

        'rh' =>
        $visitante['rh'] ?? '',

        'contacto' =>
        $visitante['contacto'] ?? '',

        'numero_emergencia' =>
        $visitante['numero_emergencia'] ?? ''

    ]);
} catch (PDOException $e) {

    responderJSON([

        'error' => true,

        'mensaje' =>
        'Error al consultar el visitante',

        'detalle' =>
        $e->getMessage()

    ], 500);
} catch (Exception $e) {

    responderJSON([

        'error' => true,

        'mensaje' =>
        $e->getMessage()

    ], 500);
}
