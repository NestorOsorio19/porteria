<?php
session_start();
require_once '../Config/database.php';

/* =====================================================
 FUNCIÓN CENTRALIZADA DE ERROR
===================================================== */
function responderError(string $codigo): void
{
    $_SESSION['error'] = $codigo;
    header("Location: ../View/registrocontratistas.php");
    exit;
}

/* =====================================================
 FUNCIÓN DE SANITIZACIÓN DE TEXTO
===================================================== */
function clean_text(string $value): string
{
    $value = trim(strip_tags($value));
    return preg_replace('/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.-]/', '', $value);
}

/* =====================================================
 DATOS RECIBIDOS DEL FORMULARIO
===================================================== */
$fecha               = $_POST['fecha'] ?? '';
$cedula              = clean_text($_POST['cedula'] ?? '');
$nombre              = clean_text($_POST['nombre'] ?? '');
$rh                  = clean_text($_POST['rh'] ?? '');
$enfermedad_alergia  = clean_text($_POST['enfermedad_alergia'] ?? '');
$nombre_emergencia   = clean_text($_POST['nombre_emergencia'] ?? '');
$telefono_emergencia = clean_text($_POST['telefono_emergencia'] ?? '');
$marca               = clean_text($_POST['marca'] ?? '');
$serial              = clean_text($_POST['serial'] ?? '');
$ingreso             = $_POST['ingreso'] ?? '';

$arl      = filter_var($_POST['arl'] ?? null, FILTER_VALIDATE_INT);
$eps      = filter_var($_POST['eps'] ?? null, FILTER_VALIDATE_INT);
$empresa  = filter_var($_POST['empresa'] ?? null, FILTER_VALIDATE_INT);
$induccion_sgsst = $_POST['induccion_sgsst'] ?? '0';
$equipo = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
 VALIDACIONES BÁSICAS
===================================================== */
if (!$fecha || !$cedula || !$nombre || !$rh || !$enfermedad_alergia ||
    !$nombre_emergencia || !$telefono_emergencia || !$ingreso || !$arl || !$eps || !$empresa
) {
    responderError('faltan_datos');
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responderError('fecha_invalida');
}

// No permitir fecha futura
if (strtotime($fecha) > strtotime(date('Y-m-d'))) {
    responderError('fecha_futura');
}

// Validar inducción SG-SST
if ($induccion_sgsst === '0') {
    responderError('induccion_obligatoria');
}

// Si no hay equipo electrónico, limpiar marca y serial
if ($equipo === 'NO') {
    $marca = '';
    $serial = '';
} else {
    // Si indicó equipo pero no completó marca o serial
    if (!$marca || !$serial) {
        responderError('datos_equipo_incompletos');
    }
}

/* =====================================================
 INSERTAR EN BASE DE DATOS
===================================================== */
$con = connection();

$sql = "INSERT INTO contratistas 
    (fecha, nombre, cedula, rh, id_arl, id_eps, empresa_fk, enfermedad_alergia, nombre_emergencia, telefono_emergencia, induccion_sgsst, marca, serial, ingreso)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($con, $sql);
if (!$stmt) {
    responderError('error_sql');
}

// Todos como string 's', excepto los IDs que pueden ser 'i'
mysqli_stmt_bind_param(
    $stmt,
    "sssiiissssssss",
    $fecha,
    $nombre,
    $cedula,
    $rh,
    $arl,
    $eps,
    $empresa,
    $enfermedad_alergia,
    $nombre_emergencia,
    $telefono_emergencia,
    $induccion_sgsst,
    $marca,
    $serial,
    $ingreso
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Contratista registrado correctamente.";
} else {
    responderError('error_insert');
}

mysqli_stmt_close($stmt);
mysqli_close($con);

// Redirigir de nuevo al formulario
header("Location: ../View/registrocontratistas.php");
exit;
?>
