<?php
include("../Config/database.php");
$con = connection();

if (isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];
    $sql = "SELECT nombre, id_arl, id_eps, rh, nom_eme, tel_eme FROM colaboradores WHERE cedula = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Cambia las claves para que JS sea claro
        $response = [
            "nombre" => $row['nombre'],
            "arl" => $row['id_arl'],
            "eps" => $row['id_eps'],
            "rh" => $row['rh'],
            "nombre_emergencia" => $row['nom_eme'],
            "telefono_emergencia" => $row['tel_eme'],
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
