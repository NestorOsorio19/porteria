<?php

header('Content-Type: application/json; charset=utf-8');

/* ==========================================================
   EVITAR CACHE
========================================================== */

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');


/* ==========================================================
   CONEXIÓN
========================================================== */

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


    /* ==========================================================
       LIMPIAR CÉDULA
    ========================================================== */

    $cedula = trim(
        (string) $_POST['cedula']
    );


    if ($cedula === '') {

        http_response_code(400);

        echo json_encode([
            'error' => true,
            'mensaje' => 'La cédula está vacía.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VALIDAR SOLO NÚMEROS
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
       CREAR CONEXIÓN
    ========================================================== */

    $connection = connection();


    /* ==========================================================
       CONSULTAR VEHÍCULO
       
       Se toma el registro más reciente de la cédula.
       
       También se consulta:
       - frecuencia_ingreso
       - induccion_sst
       
       para poder utilizar estos datos en el formulario.
    ========================================================== */

    $sql = "
        SELECT
            id_registro,
            nombre,
            cedula,
            arl,
            eps,
            placa,
            frecuencia_ingreso,
            induccion_sst
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
       NORMALIZAR FRECUENCIA
    ========================================================== */

    $frecuencia = strtolower(
        trim(
            (string) ($row['frecuencia_ingreso'] ?? '')
        )
    );


    /*
     * Si por algún motivo el registro antiguo
     * no tiene frecuencia, se considera ocasional.
     */

    if (
        $frecuencia !== 'frecuente' &&
        $frecuencia !== 'ocasional'
    ) {

        $frecuencia = 'ocasional';
    }


    /* ==========================================================
       NORMALIZAR INDUCCIÓN SG-SST
    ========================================================== */

    $induccionSST =
        (string) ($row['induccion_sst'] ?? '0');


    /*
     * Garantizar que solamente devuelva 0 o 1.
     */

    $induccionSST =
        $induccionSST === '1'
            ? '1'
            : '0';


    /* ==========================================================
       RESPUESTA EXITOSA
    ========================================================== */

    echo json_encode([

        'error' => false,

        'id_registro' =>
            (int) ($row['id_registro'] ?? 0),

        'nombre' =>
            $row['nombre'] ?? '',

        'cedula' =>
            $row['cedula'] ?? '',

        'arl' =>
            $row['arl'] ?? '',

        'eps' =>
            $row['eps'] ?? '',

        'placa' =>
            $row['placa'] ?? '',

        'frecuencia_ingreso' =>
            $frecuencia,

        'induccion_sst' =>
            $induccionSST

    ], JSON_UNESCAPED_UNICODE);

    exit;


} catch (PDOException $e) {

    /* ==========================================================
       ERROR BASE DE DATOS
    ========================================================== */

    http_response_code(500);

    echo json_encode([

        'error' => true,

        'mensaje' =>
            'Error al consultar la base de datos.'

    ], JSON_UNESCAPED_UNICODE);

    exit;


} catch (Throwable $e) {

    /* ==========================================================
       ERROR GENERAL
    ========================================================== */

    http_response_code(500);

    echo json_encode([

        'error' => true,

        'mensaje' =>
            'Error interno del servidor.'

    ], JSON_UNESCAPED_UNICODE);

    exit;
}
