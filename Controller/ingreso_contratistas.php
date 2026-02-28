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
 FUNCIÓN DE SANITIZACIÓN
===================================================== */
function clean_text(string $value): string
{
    $value = trim(strip_tags($value));
    return preg_replace('/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.+-]/', '', $value);
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
$empresa_post = $_POST['empresa'] ?? null; // Puede ser ID o "otra"

$induccion_sgsst = $_POST['induccion_sgsst'] ?? '0';
$equipo = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
 CONEXIÓN A BASE DE DATOS
===================================================== */
$con = connection();

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
 VALIDACIONES BÁSICAS
===================================================== */
if (
    !$fecha || !$cedula || !$nombre || !$rh ||
    !$enfermedad_alergia || !$nombre_emergencia ||
    !$telefono_emergencia || !$ingreso ||
    !$arl || !$eps || !$empresa
) {
    responderError('faltan_datos');
}

// Validar formato fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responderError('fecha_invalida');
}

// No permitir fecha futura
if (strtotime($fecha) > strtotime(date('Y-m-d'))) {
    responderError('fecha_futura');
}

// Validar inducción obligatoria
if ($induccion_sgsst === '0') {
    responderError('induccion_obligatoria');
}

// Validar equipo electrónico
if ($equipo === 'NO') {
    $marca = '';
    $serial = '';
} else {
    if (!$marca || !$serial) {
        responderError('datos_equipo_incompletos');
    }
}

/* =====================================================
 INSERTAR CONTRATISTA
===================================================== */
$sql = "INSERT INTO contratistas 
    (fecha, nombre, cedula, rh, id_arl
    , id_eps, empresa_fk, enfermedad_alergia, 
     nombre_emergencia, telefono_emergencia, induccion_sgsst, marca, serial, ingreso)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    responderError('error_sql');
}

/*
Tipos:
s = string
i = integer

Orden:
fecha (s)
nombre (s)
cedula (s)
rh (s)
arl (i)
eps (i)
empresa (i)
enfermedad_alergia (s)
nombre_emergencia (s)
telefono_emergencia (s)
induccion_sgsst (s)
marca (s)
serial (s)
ingreso (s)
*/

mysqli_stmt_bind_param(
    $stmt,
    "ssssiiisssssss",
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

/* =====================================================
 REDIRECCIÓN FINAL
===================================================== */
header("Location: ../View/registrocontratistas.php");
exit;
?>
