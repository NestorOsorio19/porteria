<?php

require_once("../Config/database.php");

header('Content-Type: application/json; charset=utf-8');

try {

    $con = connection();

    if (!$con) {
        throw new Exception("Error de conexión a la base de datos");
    }

    if (!isset($_POST['cedula']) || empty(trim($_POST['cedula']))) {

        echo json_encode([
            'error' => true,
            'mensaje' => 'No se recibió la cédula'
        ]);
        exit;
    }

    $cedula = trim($_POST['cedula']);

    $sql = "
        SELECT
            nombre,
            telefono,
            id_arl,
            id_eps,
            rh,
            contacto,
            numero_emergencia
        FROM visitantes
        WHERE cedula = :cedula
        LIMIT 1
    ";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':cedula', (int)$cedula, PDO::PARAM_INT);
    $stmt->execute();

    $visitante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$visitante) {

        echo json_encode([
            'error' => true,
            'mensaje' => 'Visitante no encontrado'
        ]);
        exit;
    }

    echo json_encode([
        'error'             => false,
        'nombre'            => $visitante['nombre'],
        'telefono'          => $visitante['telefono'],
        'arl'               => $visitante['id_arl'],
        'eps'               => $visitante['id_eps'],
        'rh'                => $visitante['rh'],
        'contacto'          => $visitante['contacto'],
        'numero_emergencia' => $visitante['numero_emergencia'],
    ]);

} catch (Exception $e) {

    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}