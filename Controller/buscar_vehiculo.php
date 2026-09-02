<?php

/* ==========================================================
   RESPUESTA JSON
========================================================== */

header('Content-Type: application/json; charset=utf-8');

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
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
            'encontrado' => false,
            'mensaje' => 'Método no permitido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       VALIDAR CÉDULA
    ========================================================== */

    $cedula = trim((string) ($_POST['cedula'] ?? ''));


    if ($cedula === '') {

        http_response_code(400);

        echo json_encode([
            'error' => true,
            'encontrado' => false,
            'mensaje' => 'La cédula está vacía.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       SOLO NÚMEROS
    ========================================================== */

    if (!preg_match('/^\d+$/', $cedula)) {

        http_response_code(400);

        echo json_encode([
            'error' => true,
            'encontrado' => false,
            'mensaje' => 'La cédula solo puede contener números.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       CONEXIÓN
    ========================================================== */

    $connection = connection();


    /* ==========================================================
       CONSULTAR ÚLTIMO REGISTRO DE LA CÉDULA
       
       La tabla actual contiene:
       
       - nombre
       - cedula
       - eps
       - arl
       - placa
       - frecuencia_ingreso
       - induccion_sst
    ========================================================== */

    $sql = "
        SELECT
            id_registro,
            nombre,
            cedula,
            eps,
            arl,
            placa,
            frecuencia_ingreso,
            induccion_sst
        FROM vehiculos
        WHERE cedula = :cedula
        ORDER BY id_registro DESC
        LIMIT 1
    ";


    $stmt = $connection->prepare($sql);

    $stmt->bindValue(
        ':cedula',
        $cedula,
        PDO::PARAM_STR
    );

    $stmt->execute();


    $row = $stmt->fetch(PDO::FETCH_ASSOC);


    /* ==========================================================
       CÉDULA NO ENCONTRADA
    ========================================================== */

    if (!$row) {

        echo json_encode([

            'error' => true,

            'encontrado' => false,

            'mensaje' =>
                'La cédula no está registrada. Complete la información manualmente.'

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* ==========================================================
       NORMALIZAR DATOS
    ========================================================== */

    $nombre = trim(
        (string) ($row['nombre'] ?? '')
    );

    $cedulaBD = trim(
        (string) ($row['cedula'] ?? $cedula)
    );

    $arl = (string) (
        $row['arl'] ?? ''
    );

    $eps = (string) (
        $row['eps'] ?? ''
    );

    $placa = strtoupper(
        trim(
            (string) ($row['placa'] ?? '')
        )
    );


    /* ==========================================================
       FRECUENCIA
    ========================================================== */

    $frecuencia = strtolower(
        trim(
            (string) (
                $row['frecuencia_ingreso']
                ?? 'ocasional'
            )
        )
    );


    if (
        $frecuencia !== 'frecuente' &&
        $frecuencia !== 'ocasional'
    ) {

        $frecuencia = 'ocasional';
    }


    /* ==========================================================
       INDUCCIÓN SG-SST
    ========================================================== */

    $induccion = trim(
        (string) (
            $row['induccion_sst']
            ?? '0'
        )
    );


    if ($induccion !== '1') {

        $induccion = '0';
    }


    /* ==========================================================
       ESTADO SG-SST
    ========================================================== */

    $requiereSST =
        ($frecuencia === 'frecuente');


    $induccionRealizada =
        ($induccion === '1');


    $puedeIngresar =
        !$requiereSST ||
        $induccionRealizada;


    /* ==========================================================
       MENSAJE SG-SST
    ========================================================== */

    if (!$requiereSST) {

        $mensajeSST =
            'Vehículo de ingreso ocasional.';

    } elseif ($induccionRealizada) {

        $mensajeSST =
            'Vehículo frecuente con inducción SG-SST registrada.';

    } else {

        $mensajeSST =
            'Vehículo frecuente. Debe realizar la inducción SG-SST.';
    }


    /* ==========================================================
       RESPUESTA
    ========================================================== */

    echo json_encode([

        'error' => false,

        'encontrado' => true,


        /* ======================================================
           IDENTIFICACIÓN
        ====================================================== */

        'id_registro' =>
            (int) $row['id_registro'],

        'nombre' =>
            $nombre,

        'cedula' =>
            $cedulaBD,


        /* ======================================================
           SEGURIDAD SOCIAL
        ====================================================== */

        'arl' =>
            $arl,

        'eps' =>
            $eps,


        /* ======================================================
           VEHÍCULO
        ====================================================== */

        'placa' =>
            $placa,


        /* ======================================================
           FRECUENCIA
        ====================================================== */

        'frecuencia_ingreso' =>
            $frecuencia,


        /* ======================================================
           SG-SST
        ====================================================== */

        'induccion_sst' =>
            $induccion,

        'requiere_sst' =>
            $requiereSST,

        'induccion_realizada' =>
            $induccionRealizada,

        'puede_ingresar' =>
            $puedeIngresar,

        'mensaje_sst' =>
            $mensajeSST

    ], JSON_UNESCAPED_UNICODE);

    exit;


} catch (PDOException $e) {

    /* ==========================================================
       ERROR BASE DE DATOS
    ========================================================== */

    error_log(
        'buscar_vehiculo.php PDOException: ' .
        $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([

        'error' => true,

        'encontrado' => false,

        'mensaje' =>
            'Error al consultar la base de datos.'

    ], JSON_UNESCAPED_UNICODE);

    exit;


} catch (Throwable $e) {

    /* ==========================================================
       ERROR GENERAL
    ========================================================== */

    error_log(
        'buscar_vehiculo.php Throwable: ' .
        $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([

        'error' => true,

        'encontrado' => false,

        'mensaje' =>
            'Error interno del servidor.'

    ], JSON_UNESCAPED_UNICODE);

    exit;
}
