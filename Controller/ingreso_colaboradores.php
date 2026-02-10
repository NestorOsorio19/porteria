<?php
include("../Config/database.php");
$con = connection();

// Verificar que los datos del formulario fueron enviados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $arl = $_POST['arl'];
    $eps = $_POST['eps'];
    $nombre_emergencia = $_POST['nombre_emergencia'];
    $telefono_emergencia = $_POST['telefono_emergencia'];
    $rh = $_POST['rh'];
    $placa = $_POST['placa'];
    $ingreso = $_POST['ingreso'];
    $motivo = $_POST['motivo'];

    // Verificar si todos los campos requeridos fueron llenados
    if (empty($fecha) || empty($nombre) || empty($cedula) || empty($arl) || empty($eps) || empty($nombre_emergencia) || empty($telefono_emergencia) || empty($rh) || empty($ingreso) || empty($motivo)) {
        session_start();
        $_SESSION['error'] = "Por favor, ingrese todos los campos obligatorios.";
        header("Location: ../View/registrocolaboradores.php");
        exit();
    }

    // Si el checkbox de equipo electrónico está marcado, recoger los datos del equipo
    $equipo = isset($_POST['equipo']) ? 'SI' : 'NO';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $serial = isset($_POST['serial']) ? $_POST['serial'] : '';

    // Consulta SQL para insertar los datos en la base de datos
    $sql = "INSERT INTO colaboradores (fecha, nombre, cedula, arl, eps, nombre_emergencia, telefono_emergencia, rh, placa, ingreso, motivo, equipo, tipo, serial) 
            VALUES ('$fecha', '$nombre', '$cedula', '$arl', '$eps', '$nombre_emergencia', '$telefono_emergencia', '$rh', '$placa', '$ingreso', '$motivo', '$equipo', '$tipo', '$serial')";

    if (mysqli_query($con, $sql)) {
        // Establecer mensaje de éxito en la sesión
        session_start();
        $_SESSION['success'] = "Registro guardado exitosamente.";
    } else {
        // En caso de error en la inserción
        session_start();
        $_SESSION['error'] = "Hubo un error al guardar el registro: " . mysqli_error($con);
    }

    // Redirigir de vuelta a la página de registro con el mensaje
    header("Location: ../View/registrocolaboradores.php");
    exit();
}
?>
