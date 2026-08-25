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
            nombre_emergencia,
            telefono_emergencia
        FROM contratistas
        WHERE cedula = :cedula
        LIMIT 1
    ";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':cedula', (int)$cedula, PDO::PARAM_INT);
    $stmt->execute();

    $contratista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contratista) {

        echo json_encode([
            'error' => true,
            'mensaje' => 'Contratista no encontrado'
        ]);
        exit;
    }

    echo json_encode([
        'error'             => false,
        'nombre'            => $contratista['nombre'],
        'telefono'          => $contratista['telefono'],
        'arl'               => $contratista['id_arl'],
        'eps'               => $contratista['id_eps'],
        'rh'                => $contratista['rh'],
        'nombre_emergencia' => $contratista['nombre_emergencia'],
        'telefono_emergencia' => $contratista['telefono_emergencia'],
    ]);

} catch (Exception $e) {

    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}