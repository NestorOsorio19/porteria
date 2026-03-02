<?php
session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

/* =====================================================
 CONEXIÓN
===================================================== */
$con = connection();
if (!$con) {
    $_SESSION['error'] = "Error al conectar a la base de datos";
    header("Location: ../index.php");
    exit;
}

/* =====================================================
 FUNCIÓN PARA RESPONDER ERRORES
===================================================== */
function responderError($mensaje)
{
    $_SESSION['error'] = $mensaje;
    header("Location: ../index.php");
    exit;
}

/* =====================================================
 FUNCIÓN DE SANITIZACIÓN
===================================================== */
function clean_text(string $value): string
{
    $value = trim(strip_tags($value));
    return preg_replace('/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.+-]/', '', $value);
}

/* =====================================================
 DATOS RECIBIDOS
===================================================== */
$fecha                  = $_POST['fecha'] ?? '';
$cedula                 = clean_text($_POST['cedula'] ?? '');
$nombre                 = clean_text($_POST['nombre'] ?? '');
$rh                     = clean_text($_POST['rh'] ?? '');
$nombre_emergencia      = clean_text($_POST['nombre_emergencia'] ?? '');
$telefono_emergencia    = clean_text($_POST['telefono_emergencia'] ?? '');
$marca                  = clean_text($_POST['marca'] ?? '');
$serial                 = clean_text($_POST['serial'] ?? '');

$ingreso                = $_POST['ingreso'] ?? '';
$arl                    = $_POST['arl'] ?? null;
$area                    = $_POST['area'] ?? null;
$eps                    = $_POST['eps'] ?? null; // Puede ser ID o "otra"
$equipo                 = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
 VALIDACIONES
===================================================== */
if (!$fecha || !$cedula || !$nombre || !$rh ||  !$telefono_emergencia || !$nombre_emergencia ||  !$ingreso ||  !$arl || !$eps) {
    responderError("Todos los campos obligatorios deben completarse.");
}

// Convertir a enteros
$arl = intval($arl);
$eps = intval($eps);

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responderError("Formato de fecha inválido.");
}

if (strtotime($fecha) > strtotime(date('Y-m-d'))) {
    responderError("No se permite fecha futura.");
}

/* Validación equipo electrónico */
if ($equipo === 'NO') {
    $marca = '';
    $serial = '';
} else {
    if (!$marca || !$serial) {
        responderError("Debe completar marca y serial del equipo.");
    }
}

/* =====================================================
 INSERTAR CON PREPARED STATEMENT
===================================================== */
$stmt = $con->prepare("INSERT INTO colaboradores 
    (fecha, cedula, nombre, id_arl, id_eps, rh, nom_eme, tel_eme, id_area, marca, serial, ingreso)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    "sisiisssisss",
    $fecha,
    $cedula,
    $nombre,
    $arl,
    $eps,
    $rh,
    $nombre_emergencia,
    $telefono_emergencia,
    $area,
    $marca,
    $serial,
    $ingreso
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Visitante registrado correctamente.";
} else {
    responderError('error_insert');
}

mysqli_stmt_close($stmt);
mysqli_close($con);

// Redirigir de nuevo al formulario
header("Location: ../View/registrocolaboradores.php");
exit;
?>
