<?php
session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

/* =====================================================
 ACTIVAR ERRORES MYSQL (IMPORTANTE)
===================================================== */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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
 FUNCIÓN ERROR
===================================================== */
function responderError($mensaje)
{
    $_SESSION['error'] = $mensaje;
    header("Location: ../index.php");
    exit;
}

/* =====================================================
 SANITIZACIÓN
===================================================== */
function clean_text(string $value): string
{
    $value = trim(strip_tags($value));
    return preg_replace('/[^A-Za-z0-9 áéíóúÁÉÍÓÚñÑ.+-]/', '', $value);
}

/* =====================================================
 DATOS
===================================================== */
$fecha      = $_POST['fecha'] ?? '';
$cedula     = intval($_POST['cedula'] ?? 0);
$nombre     = clean_text($_POST['nombre'] ?? '');
$rh         = clean_text($_POST['rh'] ?? '');
$telefono   = clean_text($_POST['telefono'] ?? '');
$motivo     = clean_text($_POST['motivo'] ?? '');
$marca      = clean_text($_POST['marca'] ?? '');
$serial     = clean_text($_POST['serial'] ?? '');
$carnet     = intval($_POST['carnet'] ?? 0);

$ingreso    = $_POST['ingreso'] ?? '';
$arl        = intval($_POST['arl'] ?? 0);
$eps        = intval($_POST['eps'] ?? 0);
$empresa_post = $_POST['empresa'] ?? null;
$equipo     = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
 VALIDACIONES
===================================================== */
if (!$fecha || !$cedula || !$nombre || !$rh || !$telefono || !$motivo || !$ingreso || !$empresa_post || !$carnet || !$arl || !$eps) {
    responderError("Todos los campos obligatorios deben completarse.");
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responderError("Formato de fecha inválido.");
}

if (strtotime($fecha) > strtotime(date('Y-m-d'))) {
    responderError("No se permite fecha futura.");
}

/* Validar hora */
if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $ingreso)) {
    responderError("Formato de hora inválido.");
}

/* Equipo */
if ($equipo === 'NO') {
    $marca = '';
    $serial = '';
} else {
    if (!$marca || !$serial) {
        responderError("Debe completar marca y serial del equipo.");
    }
}

/* =====================================================
 EMPRESA
===================================================== */
if ($empresa_post === "otra") {

    $nueva_empresa = clean_text($_POST['nueva_empresa'] ?? '');

    if (!$nueva_empresa) {
        responderError('Debe ingresar la nueva empresa');
    }

    $sql_check = "SELECT id_registro FROM empresas WHERE nom_empresa = ?";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param("s", $nueva_empresa);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {
        $empresa = intval($row['id_registro']);
    } else {
        $sql_insert_empresa = "INSERT INTO empresas (nom_empresa) VALUES (?)";
        $stmt_insert = $con->prepare($sql_insert_empresa);
        $stmt_insert->bind_param("s", $nueva_empresa);
        $stmt_insert->execute();

        $empresa = $con->insert_id;
        $stmt_insert->close();
    }

    $stmt_check->close();

} else {
    $empresa = intval($empresa_post);
}

if (!$empresa) {
    responderError("Empresa inválida.");
}

/* =====================================================
 INSERT
===================================================== */
$stmt = $con->prepare("INSERT INTO visitantes 
(fecha, cedula, nombre, id_arl, id_eps, rh, telefono, empresa_fk, motivo, marca, serial, carnet, ingreso)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    "sisiississsis",
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

$stmt->execute();

$_SESSION['success'] = "Visitante registrado correctamente.";

/* =====================================================
 CIERRE
===================================================== */
$stmt->close();
$con->close();

header("Location: ../index.php");
exit;