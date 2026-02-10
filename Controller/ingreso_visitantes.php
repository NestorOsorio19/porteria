<?php 
include("../Config/database.php");
$con = connection(); // Establecer la conexión a la base de datos

// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $fecha    = mysqli_real_escape_string($con, $_POST['fecha']);
    $nombre   = mysqli_real_escape_string($con, $_POST['nombre']);
    $cedula   = mysqli_real_escape_string($con, $_POST['cedula']);
    $arl      = mysqli_real_escape_string($con, $_POST['arl']);
    $eps      = mysqli_real_escape_string($con, $_POST['eps']);
    $rh       = mysqli_real_escape_string($con, $_POST['rh']);
    $telefono = mysqli_real_escape_string($con, $_POST['telefono']);
    $empresa  = mysqli_real_escape_string($con, $_POST['empresa']);
    $motivo   = mysqli_real_escape_string($con, $_POST['motivo']);
    $ingreso  = mysqli_real_escape_string($con, $_POST['ingreso']);
    $carnet   = mysqli_real_escape_string($con, $_POST['carnet']);

    // Si seleccionaron "otra", usar el campo empresa_otro
    if ($empresa === "otra" && !empty($_POST['empresa_otro'])) {
        $empresa_otro = mysqli_real_escape_string($con, $_POST['empresa_otro']);
        $empresa = $empresa_otro;
    }

    // Preparar la consulta de inserción
    $sql = "INSERT INTO porteria (fecha, nombre, cedula, arl, eps, rh, telefono, empresa, motivo, ingreso, carnet)
            VALUES ('$fecha', '$nombre', '$cedula', '$arl', '$eps', '$rh', '$telefono', '$empresa', '$motivo', '$ingreso', '$carnet')";

    // Ejecutar la consulta y manejar errores
    session_start();
    if (mysqli_query($con, $sql)) {
        $_SESSION['success'] = "Registro exitoso";
    } else {
        $_SESSION['error'] = "Error al registrar los datos: " . mysqli_error($con);
    }

    header("Location: ../index.php");
    exit();
}
?>
