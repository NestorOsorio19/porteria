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
    return preg_replace('/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.-]/', '', $value);
}

/* =====================================================
 DATOS RECIBIDOS
===================================================== */
$fecha      = $_POST['fecha'] ?? '';
$cedula     = clean_text($_POST['cedula'] ?? '');
$nombre     = clean_text($_POST['nombre'] ?? '');
$rh         = clean_text($_POST['rh'] ?? '');
$telefono   = clean_text($_POST['telefono'] ?? '');
$motivo     = clean_text($_POST['motivo'] ?? '');
$marca      = clean_text($_POST['marca'] ?? '');
$serial     = clean_text($_POST['serial'] ?? '');
$carnet     = clean_text($_POST['carnet'] ?? '');

$ingreso    = $_POST['ingreso'] ?? '';
$arl        = filter_var($_POST['arl'] ?? null, FILTER_VALIDATE_INT);
$eps        = filter_var($_POST['eps'] ?? null, FILTER_VALIDATE_INT);
$empresa    = filter_var($_POST['empresa'] ?? null, FILTER_VALIDATE_INT);
$equipo     = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
 VALIDACIONES
===================================================== */
if (
    !$fecha || !$cedula || !$nombre || !$rh ||
    !$telefono || !$motivo || !$ingreso ||
    !$arl || !$eps || !$empresa || !$carnet
) {
    responderError("Todos los campos obligatorios deben completarse.");
}

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
$stmt = $con->prepare("INSERT INTO visitantes 
    (fecha, cedula, nombre, id_arl, id_eps, rh, telefono, empresa_fk, motivo, marca, serial, carnet, ingreso)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    "sssiississsss",
    $fecha,
    $cedula,
    $nombre,
    $id_arl,
    $id_eps,
    $rh,
    $telefono,
    $empresa_fk,
    $motivo,
    $marca,
    $serial,
    $carnet,
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
header("Location: ../index.php");
exit;
?>

