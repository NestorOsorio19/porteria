<?php
include("../Config/database.php");
$con = connection();

if(isset($_POST['id']) && isset($_POST['hora_salida'])){
    $id = intval($_POST['id']); // Convertir a número entero
    $hora_salida = $_POST['hora_salida'];

    $sql = "UPDATE visitantes SET salida = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hora_salida, $id);

    if(mysqli_stmt_execute($stmt)){
        echo "ok"; // Solo devolvemos 'ok' si todo salió bien
    } else {
        echo "error";
    }

    mysqli_stmt_close($stmt);
}
mysqli_close($con);
?>
