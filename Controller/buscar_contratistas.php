<?php
include("../Config/database.php");
$con = connection();

if (isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];
    $sql = "SELECT nombre, rh, id_arl, id_eps, enfermedad_alergia, nombre_emergencia, telefono_emergencia, empresa_fk, ingreso FROM contratistas WHERE cedula = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Cambia las claves para que JS sea claro
        $response = [
            "nombre" => $row['nombre'],
            "rh" => $row['rh'],
            "arl" => $row['id_arl'],
            "eps" => $row['id_eps'],
            "enfermedad_alergia" => $row['enfermedad_alergia'],
            "nombre_emergencia" => $row['nombre_emergencia'],
            "telefono_emergencia" => $row['telefono_emergencia'],
            "empresa" => $row['empresa_fk'],
        ];
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado.']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'No se recibió la cédula.']);
}
?>
