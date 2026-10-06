<?php

require_once("../Config/database.php");

header('Content-Type: application/json; charset=utf-8');

/* =====================================================
   FUNCIÓN PARA RESPONDER JSON
===================================================== */
function responderJSON(bool $error, string $mensaje = '', array $datos = []): void
{
    echo json_encode(
        array_merge(
            [
                'error'   => $error,
                'mensaje' => $mensaje
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/* =====================================================
   CONEXIÓN
===================================================== */
try {

    $con = connection();

    if (!$con) {
        responderJSON(
            true,
            'Error de conexión a la base de datos.'
        );
    }

    /* =====================================================
       VALIDAR CÉDULA
    ===================================================== */

    if (
        !isset($_POST['cedula']) ||
        trim($_POST['cedula']) === ''
    ) {

        responderJSON(
            true,
            'No se recibió la cédula.'
        );
    }

    $cedula = trim($_POST['cedula']);

    /*
     * La cédula debe contener únicamente números.
     */

    if (!preg_match('/^\d+$/', $cedula)) {

        responderJSON(
            true,
            'La cédula debe contener únicamente números.'
        );
    }

    /* =====================================================
    CONSULTAR CONTRATISTA
    ===================================================== */

    $sql = "
    SELECT
        nombre,
        telefono,
        id_arl,
        id_eps,
        rh,
        empresa_fk,
        nombre_emergencia,
        telefono_emergencia,
        induccion_sgsst,
        enfermedad_alergia,
        marca,
        serial
    FROM contratistas
    WHERE cedula = :cedula
    ORDER BY
        (
            IF(telefono IS NOT NULL AND telefono <> '', 1, 0) +
            IF(id_arl IS NOT NULL, 1, 0) +
            IF(id_eps IS NOT NULL, 1, 0) +
            IF(rh IS NOT NULL AND rh <> '', 1, 0) +
            IF(empresa_fk IS NOT NULL, 1, 0) +
            IF(nombre_emergencia IS NOT NULL AND nombre_emergencia <> '', 1, 0) +
            IF(telefono_emergencia IS NOT NULL AND telefono_emergencia <> '', 1, 0) +
            IF(induccion_sgsst IS NOT NULL AND induccion_sgsst <> '', 1, 0) +
            IF(enfermedad_alergia IS NOT NULL AND enfermedad_alergia <> '', 1, 0) +
            IF(marca IS NOT NULL AND marca <> '', 1, 0) +
            IF(serial IS NOT NULL AND serial <> '', 1, 0)
        ) DESC,
        id DESC
    LIMIT 1
";

    $stmt = $con->prepare($sql);

    $stmt->bindValue(
        ':cedula',
        (int)$cedula,
        PDO::PARAM_INT
    );

    $stmt->execute();

    $contratista = $stmt->fetch(PDO::FETCH_ASSOC);

    /* =====================================================
       CONTRATISTA NO ENCONTRADO
    ===================================================== */

    if (!$contratista) {

        responderJSON(
            true,
            'Contratista no encontrado.'
        );
    }

    /* =====================================================
       RESPUESTA
    ===================================================== */

    responderJSON(
        false,
        'Contratista encontrado.',
        [
            'nombre'               => $contratista['nombre'] ?? '',
            'telefono'             => $contratista['telefono'] ?? '',
            'arl'                  => $contratista['id_arl'] ?? '',
            'eps'                  => $contratista['id_eps'] ?? '',
            'rh'                   => $contratista['rh'] ?? '',

            'empresa'              => $contratista['empresa_fk'] ?? '',

            'nombre_emergencia'    => $contratista['nombre_emergencia'] ?? '',

            'telefono_emergencia'  => $contratista['telefono_emergencia'] ?? '',

            'induccion_sgsst'      => $contratista['induccion_sgsst'] ?? '',

            'enfermedad_alergia'   => $contratista['enfermedad_alergia'] ?? '',

            'marca'                => $contratista['marca'] ?? '',

            'serial'               => $contratista['serial'] ?? ''
        ]
    );
} catch (PDOException $e) {

    /*
     * Durante desarrollo puedes devolver el error.
     *
     * En producción sería mejor registrar
     * $e->getMessage() en un archivo de logs.
     */

    responderJSON(
        true,
        'Error al consultar el contratista: ' . $e->getMessage()
    );
} catch (Exception $e) {

    responderJSON(
        true,
        $e->getMessage()
    );
}
