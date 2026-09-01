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

$arl          = filter_var($_POST['arl'] ?? null, FILTER_VALIDATE_INT);
$eps          = filter_var($_POST['eps'] ?? null, FILTER_VALIDATE_INT);
$empresa_post = $_POST['empresa'] ?? null;

$induccion_sgsst = $_POST['induccion_sgsst'] ?? '0';
$equipo = isset($_POST['equipo']) ? 'SI' : 'NO';

/* =====================================================
   CONEXIÓN A BASE DE DATOS
===================================================== */
$con = connection();

try {

    /* =====================================================
       MANEJO DE EMPRESA (NORMAL U "OTRA")
    ===================================================== */
    if ($empresa_post === "otra") {

        $nueva_empresa = clean_text($_POST['nueva_empresa'] ?? '');

        if (!$nueva_empresa) {
            responderError('nueva_empresa_vacia');
        }

        // Verificar si la empresa ya existe
        $sql_check = "SELECT id_registro 
                      FROM empresas 
                      WHERE nom_empresa = ?";

        $stmt_check = $con->prepare($sql_check);
        $stmt_check->execute([$nueva_empresa]);

        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($row) {

            // Existe
            $empresa = $row['id_registro'];

        } else {

            // Insertar nueva empresa
            $sql_insert_empresa = "INSERT INTO empresas (nom_empresa)
                                   VALUES (?)";

            $stmt_insert = $con->prepare($sql_insert_empresa);
            $stmt_insert->execute([$nueva_empresa]);

            $empresa = $con->lastInsertId();
        }

    } else {

        // Empresa seleccionada del combo
        $empresa = filter_var($empresa_post, FILTER_VALIDATE_INT);

    }

    /* =====================================================
       VALIDACIONES BÁSICAS
    ===================================================== */
    if (
        !$fecha ||
        !$cedula ||
        !$nombre ||
        !$rh ||
        !$enfermedad_alergia ||
        !$nombre_emergencia ||
        !$telefono_emergencia ||
        !$ingreso ||
        !$arl ||
        !$eps ||
        !$empresa
    ) {
        responderError('faltan_datos');
    }

    // Validar fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        responderError('fecha_invalida');
    }

    // No permitir fechas futuras
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
            (
                fecha,
                nombre,
                cedula,
                rh,
                id_arl,
                id_eps,
                empresa_fk,
                enfermedad_alergia,
                nombre_emergencia,
                telefono_emergencia,
                induccion_sgsst,
                marca,
                serial,
                ingreso
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = $con->prepare($sql);

    $ok = $stmt->execute([
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
    ]);

    if ($ok) {
        $_SESSION['success'] = "Contratista registrado correctamente.";
    } else {
        responderError('error_insert');
    }

} catch (PDOException $e) {

    // Para depuración puedes usar:
    // die($e->getMessage());

    $_SESSION['error'] = 'error_bd';

}

$con = null;

/* =====================================================
   REDIRECCIÓN FINAL
===================================================== */
header("Location: ../View/registro_contratistas.php");
exit;
?>