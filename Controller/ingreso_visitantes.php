<?php

session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

/* =====================================================
   CONEXIÓN
===================================================== */

$con = connection();

if (!$con) {

    header(
        "Location: ../View/registro_visitantes.php?error=" .
        urlencode("Error al conectar a la base de datos")
    );

    exit;
}

/* =====================================================
   FUNCIÓN ERROR
===================================================== */

function responderError($mensaje)
{
    header(
        "Location: ../View/registro_visitantes.php?error=" .
        urlencode($mensaje)
    );
    exit;
}

/* =====================================================
   SANITIZACIÓN
===================================================== */

function clean_text(string $value): string
{
    $value = trim(strip_tags($value));

    return preg_replace(
        '/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.+,\-]/u',
        '',
        $value
    );
}

/* =====================================================
   DATOS
===================================================== */

$fecha              = $_POST['fecha'] ?? '';
$cedula             = intval($_POST['cedula'] ?? 0);
$nombre             = clean_text($_POST['nombre'] ?? '');
$rh                 = clean_text($_POST['rh'] ?? '');
$telefono           = clean_text($_POST['telefono'] ?? '');
$contacto           = clean_text($_POST['contacto'] ?? '');
$numero_emergencia  = clean_text($_POST['numero_emergencia'] ?? '');
$motivo             = clean_text($_POST['motivo'] ?? '');

$marca              = clean_text($_POST['marca'] ?? '');
$serial             = clean_text($_POST['serial'] ?? '');

$carnet             = intval($_POST['carnet'] ?? 0);

$ingreso            = $_POST['ingreso'] ?? '';
$realizo            = clean_text($_POST['realizo'] ?? '');

$arl                = intval($_POST['arl'] ?? 0);
$eps                = intval($_POST['eps'] ?? 0);

$empresa_post       = $_POST['empresa'] ?? null;

$equipo             = isset($_POST['equipo'])
    ? 'SI'
    : 'NO';

/* =====================================================
   VALIDACIONES GENERALES
===================================================== */

if (
    !$fecha ||
    !$cedula ||
    !$nombre ||
    !$rh ||
    !$telefono ||
    !$contacto ||
    !$numero_emergencia ||
    !$motivo ||
    !$ingreso ||
    !$empresa_post ||
    !$carnet ||
    !$arl ||
    !$eps
) {

    responderError(
        "Todos los campos obligatorios deben completarse."
    );
}

/* =====================================================
   VALIDAR FECHA
===================================================== */

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {

    responderError(
        "Formato de fecha inválido."
    );
}

if (strtotime($fecha) > strtotime(date('Y-m-d'))) {

    responderError(
        "No se permiten fechas futuras."
    );
}

/* =====================================================
   VALIDAR HORA
===================================================== */

if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $ingreso)) {

    responderError(
        "Formato de hora inválido."
    );
}

/* =====================================================
   EQUIPO ELECTRÓNICO
===================================================== */

if ($equipo === 'NO') {

    $marca = '';
    $serial = '';

} else {

    if (!$marca || !$serial) {

        responderError(
            "Debe completar marca y serial del equipo."
        );
    }
}

/* =====================================================
   EMPRESA
===================================================== */

if ($empresa_post === 'otra') {

    $nueva_empresa = clean_text(
        $_POST['nueva_empresa'] ?? ''
    );

    if (!$nueva_empresa) {

        responderError(
            "Debe ingresar el nombre de la nueva empresa."
        );
    }

    try {

        $stmt = $con->prepare("
            SELECT id_registro
            FROM empresas
            WHERE UPPER(TRIM(nom_empresa))
                = UPPER(TRIM(?))
        ");

        $stmt->execute([
            $nueva_empresa
        ]);

        $empresaExistente = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        if ($empresaExistente) {

            $empresa = (int)
                $empresaExistente['id_registro'];

        } else {

            $stmt = $con->prepare("
                INSERT INTO empresas
                (
                    nom_empresa
                )
                VALUES
                (
                    ?
                )
            ");

            $stmt->execute([
                $nueva_empresa
            ]);

            $empresa = (int)
                $con->lastInsertId();
        }

    } catch (PDOException $e) {

        responderError(
            "Error registrando empresa: " .
            $e->getMessage()
        );
    }

} else {

    $empresa = (int) $empresa_post;

    $stmt = $con->prepare("
        SELECT id_registro
        FROM empresas
        WHERE id_registro = ?
    ");

    $stmt->execute([
        $empresa
    ]);

    if (!$stmt->fetch()) {

        responderError(
            "La empresa seleccionada no existe."
        );
    }
}

/* =====================================================
   VALIDAR EMPRESA
===================================================== */

if (!$empresa) {

    responderError(
        "Empresa inválida."
    );
}

/* =====================================================
INSERTAR VISITANTE
===================================================== */

try {

    $sql = "

        INSERT INTO visitantes
        (

            fecha,
            nombre,
            cedula,

            id_arl,
            id_eps,
            rh,

            telefono,
            contacto,
            numero_emergencia,

            empresa_fk,

            motivo,

            ingreso,
            realizo,

            carnet,

            marca,
            serial

        )
        VALUES
        (

            :fecha,
            :nombre,
            :cedula,

            :id_arl,
            :id_eps,
            :rh,

            :telefono,
            :contacto,
            :numero_emergencia,

            :empresa_fk,

            :motivo,

            :ingreso,
            :realizo,

            :carnet,

            :marca,
            :serial

        )

    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([

        ':fecha' => $fecha,
        ':nombre' => $nombre,
        ':cedula' => $cedula,

        ':id_arl' => $arl,
        ':id_eps' => $eps,
        ':rh' => $rh,

        ':telefono' => $telefono,

        ':contacto' => $contacto,

        ':numero_emergencia' => $numero_emergencia,

        ':empresa_fk' => $empresa,

        ':motivo' => $motivo,

        ':ingreso' => $ingreso,
        ':realizo' => $realizo,

        ':carnet' => $carnet,

        ':marca' => $marca,
        ':serial' => $serial

    ]);

    header(
        "Location: ../View/registro_visitantes.php?guardado=1"
    );

    exit;

} catch (PDOException $e) {

    responderError(
        "No fue posible guardar el registro: " .
        $e->getMessage()
    );
}