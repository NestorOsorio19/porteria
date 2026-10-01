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
        nom_eme,
        tel_eme,
        id_area
    FROM colaboradores
    WHERE cedula = :cedula
    ORDER BY
        telefono IS NULL,
        id_registro DESC
    LIMIT 1
";

    $stmt = $con->prepare($sql);
    $stmt->bindValue(':cedula', (int)$cedula, PDO::PARAM_INT);
    $stmt->execute();

    $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$colaborador) {

        echo json_encode([
            'error' => true,
            'mensaje' => 'Colaborador no encontrado'
        ]);
        exit;
    }

    echo json_encode([
        'error'             => false,
        'nombre'            => $colaborador['nombre'],
        'telefono'          => $colaborador['telefono'],
        'arl'               => $colaborador['id_arl'],
        'eps'               => $colaborador['id_eps'],
        'rh'                => $colaborador['rh'],
        'contacto'          => $colaborador['nom_eme'],
        'numero_emergencia' => $colaborador['tel_eme'],
        'area'              => $colaborador['id_area']
    ]);
} catch (Exception $e) {

    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}
