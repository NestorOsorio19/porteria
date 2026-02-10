<?php
// Incluir la conexión a la base de datos
include("../Config/database.php");

// Establecer la conexión con la base de datos
$con = connection();

// Recibir los parámetros desde la solicitud AJAX
$id = $_POST['id'];
$hora_salida = $_POST['hora_salida'];

// Validar que se recibieron los parámetros necesarios
if (isset($id) && isset($hora_salida)) {
    // Actualizar la hora de salida en la base de datos
    $sql = "UPDATE colaboradores SET salida = '$hora_salida' WHERE id = $id";
    if (mysqli_query($con, $sql)) {
        echo "Salida registrada con éxito";
    } else {
        echo "Error al registrar la salida: " . mysqli_error($con);
    }
} else {
    echo "Parámetros no válidos.";
}
?>