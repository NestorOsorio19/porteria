<?php

include("../Config/database.php");

header('Content-Type: application/json; charset=utf-8');

try {

    $con = connection();

    if (!isset($_POST['id'])) {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'ID no recibido.'
        ]);

        exit;
    }

    $id = (int) $_POST['id'];

    $hora_salida = date('H:i:s');

    $sql = "
        UPDATE contratistas
        SET salida = :salida
        WHERE id = :id
    ";

    $stmt = $con->prepare($sql);

    $resultado = $stmt->execute([
        ':salida' => $hora_salida,
        ':id' => $id
    ]);

    if ($resultado) {

        echo json_encode([
            'ok' => true,
            'hora_salida' => $hora_salida,
            'mensaje' => 'Salida registrada correctamente.'
        ]);

    } else {

        echo json_encode([
            'ok' => false,
            'mensaje' => 'No fue posible registrar la salida.'
        ]);
    }

    $stmt = null;
    $con = null;

} catch (PDOException $e) {

    echo json_encode([
        'ok' => false,
        'mensaje' => $e->getMessage()
    ]);
}