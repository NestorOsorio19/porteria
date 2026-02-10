<?php
// Incluir el archivo de conexión a la base de datos
include("../Config/database.php");
$con = connection();

// Verificar si se ha enviado la cédula via POST
if (isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];

    // Realizar la consulta para obtener los datos del visitante
    $sql = "SELECT * FROM porteria WHERE cedula = '$cedula' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Si el visitante existe, devolver los datos en formato JSON
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        // Si no se encuentra al visitante, devolver un error
        echo json_encode(['error' => 'Usuario no encontrado.']);
    }
} else {
    echo json_encode(['error' => 'No se recibió la cédula.']);
}
?>
