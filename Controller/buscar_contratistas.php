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
