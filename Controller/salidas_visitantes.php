<?php
include("../Config/database.php");

$con = connection();

// Obtener los parámetros enviados por POST
$id = $_POST['id'];
$hora_salida = $_POST['hora_salida'];

// Asegurarse de que los parámetros existen
if (isset($id) && isset($hora_salida)) {
    // Escapar los datos para prevenir SQL Injection
    $id = mysqli_real_escape_string($con, $id);
    $hora_salida = mysqli_real_escape_string($con, $hora_salida);

    // Actualizar la hora de salida en la base de datos
    $sql = "UPDATE porteria SET salida = '$hora_salida' WHERE id = $id";

    if (mysqli_query($con, $sql)) {
        echo "Salida registrada con éxito";
    } else {
        echo "Error al registrar la salida: " . mysqli_error($con);
    }
} else {
    echo "Faltan datos para registrar la salida.";
}
?>
