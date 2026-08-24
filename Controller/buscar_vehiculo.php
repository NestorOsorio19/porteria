<?php

header('Content-Type: application/json; charset=utf-8');

// Evitar cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once '../Config/database.php';

try {

    /* ==========================================================
       VALIDAR MÉTODO
    ========================================================== */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);

        echo json_encode([
            'error' => true,
            'mensaje' => 'Método no permitido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VALIDAR CÉDULA
    ========================================================== */

    if (!isset($_POST['cedula'])) {

        http_response_code(400);

        echo json_encode([
            'error' => true,
            'mensaje' => 'No se recibió la cédula.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    // Convertir siempre a texto y quitar espacios
    $cedula = trim((string) $_POST['cedula']);


    if ($cedula === '') {

        http_response_code(400);

        echo json_encode([
            'error' => true,
            'mensaje' => 'La cédula está vacía.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VALIDAR QUE SOLO CONTENGA NÚMEROS
    ========================================================== */

    if (!preg_match('/^\d+$/', $cedula)) {

        http_response_code(400);

        echo json_encode([
            'error' => true,
            'mensaje' => 'La cédula solo puede contener números.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       CONEXIÓN
    ========================================================== */

    $connection = connection();


    /* ==========================================================
       CONSULTAR VEHÍCULO
       
       CAST permite comparar correctamente la cédula aunque
       la columna en MySQL sea numérica o de texto.
    ========================================================== */

    $sql = "
        SELECT
            nombre,
            cedula,
            arl,
            eps,
            placa
        FROM vehiculos
        WHERE CAST(cedula AS CHAR) = :cedula
        ORDER BY id_registro DESC
        LIMIT 1
    ";


    $stmt = $connection->prepare($sql);


    $stmt->execute([
        ':cedula' => $cedula
    ]);


    $row = $stmt->fetch(PDO::FETCH_ASSOC);


    /* ==========================================================
       CÉDULA NO ENCONTRADA
    ========================================================== */

    if (!$row) {

        echo json_encode([
            'error' => true,
            'mensaje' => 'La cédula no está registrada.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       RESPUESTA EXITOSA
    ========================================================== */

    echo json_encode([

        'error' => false,

        'nombre' => $row['nombre'] ?? '',

        'cedula' => $row['cedula'] ?? '',

        'arl' => $row['arl'] ?? '',

        'eps' => $row['eps'] ?? '',

        'placa' => $row['placa'] ?? ''

    ], JSON_UNESCAPED_UNICODE);

    exit;


} catch (PDOException $e) {

    echo json_encode([
        'error' => true,
        'mensaje' => 'Error al consultar la base de datos.'
    ]);
} catch (Throwable $e) {

    /* ==========================================================
       ERROR GENERAL
    ========================================================== */

    http_response_code(500);

    echo json_encode([
        'error' => true,
        'mensaje' => 'Error interno del servidor.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}