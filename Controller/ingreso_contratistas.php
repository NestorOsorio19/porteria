<?php
session_start();
include("../Config/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener datos del formulario
    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $rh = $_POST['rh'];
    $arl_id = $_POST['arl']; // ahora es ID
    $eps_id = $_POST['eps']; // ahora es ID
    $empresa_id = $_POST['empresa']; // ID de empresa
    $enfermedad_alergia = $_POST['enfermedad_alergia'];
    $nombre_emergencia = $_POST['nombre_emergencia'];
    $telefono_emergencia = $_POST['telefono_emergencia'];
    $induccion_sgsst = $_POST['induccion_sgsst'];
    $ingreso = $_POST['ingreso'];

    // Validación básica
    if (empty($fecha) || empty($nombre) || empty($cedula) || empty($rh) || empty($arl_id) || empty($eps_id) || empty($empresa_id) || empty($nombre_emergencia) || empty($telefono_emergencia) || empty($induccion_sgsst) || empty($ingreso)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../View/registrocontratistas.php");
        exit();
    }

    $con = connection();

    // SQL con claves foráneas
    $sql = "INSERT INTO contratistas 
        (fecha, nombre, cedula, rh, arl_id, eps_id, empresa_fk, enfermedad_alergia, nombre_emergencia, telefono_emergencia, induccion_sgsst, ingreso)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($con, $sql)) {

        // Vincular parámetros: s=string, i=int
        mysqli_stmt_bind_param(
            $stmt,
            "ssssiiisssss",
            $fecha,
            $nombre,
            $cedula,
            $rh,
            $arl_id,
            $eps_id,
            $empresa_id,
            $enfermedad_alergia,
            $nombre_emergencia,
            $telefono_emergencia,
            $induccion_sgsst,
            $ingreso
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "El contratista ha sido registrado exitosamente.";
        } else {
            $_SESSION['error'] = "Hubo un error al registrar los datos: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);

    } else {
        $_SESSION['error'] = "Error en la consulta SQL: " . mysqli_error($con);
    }

    mysqli_close($con);

    // Redirigir siempre al formulario
    header("Location: ../View/registrocontratistas.php");
    exit();
}
?>