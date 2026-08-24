<?php

session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

/* ==========================================================
VALIDAR MÉTODO
========================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: ../View/registro_vehiculos.php');
    exit;
}

/* ==========================================================
CONEXIÓN
========================================================== */

$connection = connection();

try {

    /* ==========================================================
    CAPTURAR DATOS
    ========================================================== */

    $fecha           = trim($_POST['fecha'] ?? '');
    $nombre          = trim($_POST['nombre'] ?? '');
    $cedula          = trim($_POST['cedula'] ?? '');

    $eps             = (int) ($_POST['eps'] ?? 0);
    $arl             = (int) ($_POST['arl'] ?? 0);

    $tipo_visita     = trim($_POST['tipo_visita'] ?? '');
    $procedencia     = trim($_POST['procedencia'] ?? '');
    $destino         = trim($_POST['destino'] ?? '');

    $placa           = strtoupper(trim($_POST['placa_vehiculo'] ?? ''));

    $fecha_soat      = trim($_POST['fecha_soat'] ?? '');
    $revision_tecn   = trim($_POST['fecha_revision'] ?? '');
    $licencia        = trim($_POST['fecha_licencia'] ?? '');

    $induccion_sst   = trim($_POST['induccion_sgsst'] ?? '0');

    /* ==========================================================
    HORAS
    ========================================================== */

    $hora_ingreso = date('H:i:s');
    $hora_salida  = '00:00:00';

    /* ==========================================================
    USUARIO REGISTRO
    ========================================================== */

    $registro = trim($_POST['registro'] ?? '');

    /* ==========================================================
    VALIDACIONES
    ========================================================== */

    if (
        empty($fecha) ||
        empty($nombre) ||
        empty($cedula) ||
        empty($tipo_visita) ||
        empty($procedencia) ||
        empty($destino) ||
        empty($placa) ||
        empty($fecha_soat) ||
        empty($revision_tecn) ||
        empty($licencia) ||
        empty($registro)
    ) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    /* ==========================================================
    INSERT
    ========================================================== */

    $sql = "
        INSERT INTO vehiculos (
            fecha,
            nombre,
            cedula,
            eps,
            arl,
            tipo_visita,
            hora_ingreso,
            hora_salida,
            procedencia,
            destino,
            placa,
            fecha_soat,
            revision_tecn,
            licencia,
            induccion_sst,
            registro
        )
        VALUES (
            :fecha,
            :nombre,
            :cedula,
            :eps,
            :arl,
            :tipo_visita,
            :hora_ingreso,
            :hora_salida,
            :procedencia,
            :destino,
            :placa,
            :fecha_soat,
            :revision_tecn,
            :licencia,
            :induccion_sst,
            :registro
        )
    ";

    $stmt = $connection->prepare($sql);

    $stmt->execute([

        ':fecha'          => $fecha,
        ':nombre'         => $nombre,
        ':cedula'         => $cedula,
        ':eps'            => $eps,
        ':arl'            => $arl,
        ':tipo_visita'    => $tipo_visita,
        ':hora_ingreso'   => $hora_ingreso,
        ':hora_salida'    => $hora_salida,
        ':procedencia'    => $procedencia,
        ':destino'        => $destino,
        ':placa'          => $placa,
        ':fecha_soat'     => $fecha_soat,
        ':revision_tecn'  => $revision_tecn,
        ':licencia'       => $licencia,
        ':induccion_sst'  => $induccion_sst,
        ':registro'       => $registro

    ]);

    header('Location: ../View/registro_vehiculos.php?guardado=1');
    exit;
} catch (PDOException $e) {

    $mensaje = urlencode($e->getMessage());

    header("Location: ../View/registro_vehiculos.php?error={$mensaje}");
    exit;
} catch (Exception $e) {

    $mensaje = urlencode($e->getMessage());

    header("Location: ../View/registro_vehiculos.php?error={$mensaje}");
    exit;
}
