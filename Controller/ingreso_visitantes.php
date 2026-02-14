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
$arl        = $_POST['arl'] ?? null;
$eps        = $_POST['eps'] ?? null;
$empresa_post = $_POST['empresa'] ?? null; // Puede ser ID o "otra"
$equipo     = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
 VALIDACIONES
===================================================== */
if (!$fecha || !$cedula || !$nombre || !$rh || !$telefono || !$motivo || !$ingreso || !$empresa_post || !$carnet || !$arl || !$eps) {
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
 MANEJO DE EMPRESA (NORMAL U "OTRA")
===================================================== */
if ($empresa_post === "otra") {

    $nueva_empresa = clean_text($_POST['nueva_empresa'] ?? '');

    if (!$nueva_empresa) {
        responderError('nueva_empresa_vacia');
    }

    // Verificar si la empresa ya existe
    $sql_check = "SELECT id_registro FROM empresas WHERE nom_empresa = ?";
    $stmt_check = mysqli_prepare($con, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $nueva_empresa);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);

    if ($row = mysqli_fetch_assoc($result)) {
        // Si existe, usar el ID existente
        $empresa = $row['id_registro'];
    } else {
        // Si no existe, insertarla
        $sql_insert_empresa = "INSERT INTO empresas (nom_empresa) VALUES (?)";
        $stmt_insert = mysqli_prepare($con, $sql_insert_empresa);
        mysqli_stmt_bind_param($stmt_insert, "s", $nueva_empresa);
        mysqli_stmt_execute($stmt_insert);

        $empresa = mysqli_insert_id($con);
        mysqli_stmt_close($stmt_insert);
    }

    mysqli_stmt_close($stmt_check);

} else {
    // Empresa seleccionada normalmente
    $empresa = filter_var($empresa_post, FILTER_VALIDATE_INT);
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
    $arl,
    $eps,
    $rh,
    $telefono,
    $empresa,
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

