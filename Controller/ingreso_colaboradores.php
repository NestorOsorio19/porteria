<?php

session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

/* =====================================================
   CONEXIÓN
===================================================== */

$con = connection();

if (!$con instanceof PDO) {

    header("Location: ../View/registro_colaboradores.php?error=" . urlencode(
        "No fue posible conectar con la base de datos."
    ));

    exit;
}


/* =====================================================
   FUNCIÓN PARA REDIRECCIONAR CON ERROR
===================================================== */

function responderError(string $mensaje): void
{
    header(
        "Location: ../View/registro_colaboradores.php?error=" .
        urlencode($mensaje)
    );

    exit;
}


/* =====================================================
   FUNCIÓN PARA LIMPIAR TEXTO
===================================================== */

function clean_text(string $value): string
{
    $value = trim(strip_tags($value));

    return preg_replace(
        '/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.+-]/u',
        '',
        $value
    );
}


/* =====================================================
   VALIDAR MÉTODO
===================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    responderError("Solicitud no válida.");
}


/* =====================================================
   DATOS RECIBIDOS
===================================================== */

$fecha = $_POST['fecha'] ?? '';

$cedula = clean_text(
    $_POST['cedula'] ?? ''
);

$nombre = clean_text(
    $_POST['nombre'] ?? ''
);

$telefono = clean_text(
    $_POST['telefono'] ?? ''
);

$rh = clean_text(
    $_POST['rh'] ?? ''
);

$nombre_emergencia = clean_text(
    $_POST['contacto'] ?? ''
);

$telefono_emergencia = clean_text(
    $_POST['numero_emergencia'] ?? ''
);

$marca = clean_text(
    $_POST['marca'] ?? ''
);

$serial = clean_text(
    $_POST['serial'] ?? ''
);

$ingreso = $_POST['ingreso'] ?? '';

$arl = $_POST['arl'] ?? '';

$eps = $_POST['eps'] ?? '';

$area = $_POST['area'] ?? '';

$equipo = isset($_POST['equipo']) ? 'SI' : 'NO';


/* =====================================================
   VALIDAR CAMPOS OBLIGATORIOS
===================================================== */

if (
    $fecha === '' ||
    $cedula === '' ||
    $nombre === '' ||
    $telefono === '' ||
    $rh === '' ||
    $nombre_emergencia === '' ||
    $telefono_emergencia === '' ||
    $ingreso === '' ||
    $arl === '' ||
    $eps === '' ||
    $area === ''
) {

    responderError(
        "Todos los campos obligatorios deben completarse."
    );
}


/* =====================================================
   VALIDAR CÉDULA
===================================================== */

if (!preg_match('/^\d+$/', $cedula)) {

    responderError(
        "La cédula solamente puede contener números."
    );
}


/* =====================================================
   VALIDAR TELÉFONOS
===================================================== */

if (!preg_match('/^\d+$/', $telefono)) {

    responderError(
        "El número de teléfono solamente puede contener números."
    );
}

if (!preg_match('/^\d+$/', $telefono_emergencia)) {

    responderError(
        "El número de emergencia solamente puede contener números."
    );
}


/* =====================================================
   CONVERTIR IDs
===================================================== */

$arl = filter_var($arl, FILTER_VALIDATE_INT);

$eps = filter_var($eps, FILTER_VALIDATE_INT);

$area = filter_var($area, FILTER_VALIDATE_INT);


if ($arl === false || $eps === false || $area === false) {

    responderError(
        "Los datos de ARL, EPS o área no son válidos."
    );
}


/* =====================================================
   VALIDAR FECHA
===================================================== */

$fechaObj = DateTime::createFromFormat(
    'Y-m-d',
    $fecha
);

if (
    !$fechaObj ||
    $fechaObj->format('Y-m-d') !== $fecha
) {

    responderError(
        "El formato de fecha no es válido."
    );
}


/* =====================================================
   NO PERMITIR FECHAS FUTURAS
===================================================== */

$hoy = new DateTime();

$hoy->setTime(0, 0, 0);

$fechaObj->setTime(0, 0, 0);

if ($fechaObj > $hoy) {

    responderError(
        "No se permiten fechas futuras."
    );
}


/* =====================================================
   VALIDAR HORA
===================================================== */

$horaObj = DateTime::createFromFormat(
    'H:i',
    $ingreso
);

if (
    !$horaObj ||
    $horaObj->format('H:i') !== $ingreso
) {

    responderError(
        "La hora de ingreso no es válida."
    );
}


/* =====================================================
   VALIDAR EQUIPO ELECTRÓNICO
===================================================== */

if ($equipo === 'NO') {

    $marca = '';

    $serial = '';

} else {

    if ($marca === '' || $serial === '') {

        responderError(
            "Debe completar la marca y el serial del equipo."
        );
    }
}


/* =====================================================
   INSERTAR
===================================================== */

try {

    $sql = "
        INSERT INTO colaboradores (
            fecha,
            cedula,
            nombre,
            telefono,
            id_arl,
            id_eps,
            rh,
            nom_eme,
            tel_eme,
            id_area,
            marca,
            serial,
            ingreso
        )
        VALUES (
            :fecha,
            :cedula,
            :nombre,
            :telefono,
            :arl,
            :eps,
            :rh,
            :nom_eme,
            :tel_eme,
            :area,
            :marca,
            :serial,
            :ingreso
        )
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute([

        ':fecha' => $fecha,

        ':cedula' => $cedula,

        ':nombre' => $nombre,

        ':telefono' => $telefono,

        ':arl' => $arl,

        ':eps' => $eps,

        ':rh' => $rh,

        ':nom_eme' => $nombre_emergencia,

        ':tel_eme' => $telefono_emergencia,

        ':area' => $area,

        ':marca' => $marca,

        ':serial' => $serial,

        ':ingreso' => $ingreso

    ]);


    /* =================================================
       GUARDADO CORRECTO
    ================================================= */

    header(
        "Location: ../View/registro_colaboradores.php?guardado=1"
    );

    exit;


} catch (PDOException $e) {

    /*
     * Para desarrollo podemos mostrar el error.
     * En producción sería mejor registrar el error
     * en un archivo y mostrar un mensaje genérico.
     */

    responderError(
        "No fue posible guardar el registro: " .
        $e->getMessage()
    );
}
