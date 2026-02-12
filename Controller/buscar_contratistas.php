<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../Config/database.php");
$con = connection();

if (!$con) {
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit;
}

if (isset($_POST['cedula'])) {
    $cedula = mysqli_real_escape_string($con, $_POST['cedula']);
    $sql = "SELECT c.nombre, c.rh, c.nombre_emergencia, c.telefono_emergencia, c.ingreso,
                   c.enfermedad_alergia, c.induccion_sgsst,
                   c.empresa_fk AS empresa_fk,
                   c.id_arl AS id_arl,
                   c.id_eps AS id_eps
            FROM contratistas c
            WHERE c.cedula = '$cedula'";

    $query = mysqli_query($con, $sql);

    if (!$query) {
        echo json_encode(["error" => "Error en la consulta: " . mysqli_error($con)]);
        exit;
    }

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        echo json_encode([
            "nombre" => $row["nombre"],
            "id_arl" => $row["id_arl"],
            "id_eps" => $row["id_eps"],
            "empresa_fk" => $row["empresa_fk"],
            "rh" => $row["rh"],
            "nombre_emergencia" => $row["nombre_emergencia"],
            "telefono_emergencia" => $row["telefono_emergencia"],
            "ingreso" => $row["ingreso"],
            "enfermedad_alergia" => $row["enfermedad_alergia"],
            "induccion_sgsst" => $row["induccion_sgsst"]
        ]);
    } else {
        echo json_encode(["error" => "No encontrado"]);
    }
}
?>
