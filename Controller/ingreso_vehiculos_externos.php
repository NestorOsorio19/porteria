<?php

session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

/* ==========================================================
   VALIDAR MÉTODO
========================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: ../View/registro_vehiculos_externos.php');
    exit;
}


/* ==========================================================
   CONEXIÓN
========================================================== */

$connection = connection();


try {

    /* ==========================================================
       CAPTURAR DATOS GENERALES
    ========================================================== */

    $fecha = trim(
        $_POST['fecha'] ?? ''
    );

    $nombre = trim(
        $_POST['nombre'] ?? ''
    );

    $cedula = trim(
        $_POST['cedula'] ?? ''
    );

    $eps = (int) (
        $_POST['eps'] ?? 0
    );

    $arl = (int) (
        $_POST['arl'] ?? 0
    );

    $tipo_visita = trim(
        $_POST['tipo_visita'] ?? ''
    );

    $procedencia = trim(
        $_POST['procedencia'] ?? ''
    );

    $destino = trim(
        $_POST['destino'] ?? ''
    );


    /* ==========================================================
       DATOS DEL VEHÍCULO
    ========================================================== */

    $placa = strtoupper(
        trim(
            $_POST['placa_vehiculo'] ?? ''
        )
    );

    $fecha_soat = trim(
        $_POST['fecha_soat'] ?? ''
    );

    $revision_tecn = trim(
        $_POST['fecha_revision'] ?? ''
    );

    $licencia = trim(
        $_POST['fecha_licencia'] ?? ''
    );


    /* ==========================================================
       FRECUENCIA DE INGRESO
    ========================================================== */

    $frecuencia_ingreso = trim(
        $_POST['frecuencia_ingreso'] ?? ''
    );


    /* ==========================================================
       INDUCCIÓN SG-SST
    ========================================================== */

    $induccion_sst = trim(
        $_POST['induccion_sgsst'] ?? ''
    );


    /* ==========================================================
       PERSONA QUE REALIZA EL REGISTRO
    ========================================================== */

    $registro = trim(
        $_POST['registro'] ?? ''
    );


    /* ==========================================================
       HORAS
    ========================================================== */

    $hora_ingreso = date('H:i:s');

    $hora_salida = '00:00:00';


    /* ==========================================================
       VALIDAR CAMPOS OBLIGATORIOS
    ========================================================== */

    if (
        empty($fecha) ||
        empty($nombre) ||
        empty($cedula) ||
        empty($tipo_visita) ||
        empty($procedencia) ||
        empty($destino) ||
        empty($placa) ||
        empty($fecha_soat) ||
        empty($revision_tecn) ||
        empty($licencia) ||
        empty($frecuencia_ingreso) ||
        empty($registro)
    ) {

        throw new Exception(
            'Todos los campos obligatorios deben estar completos.'
        );
    }


    /* ==========================================================
       VALIDAR FRECUENCIA
    ========================================================== */

    if (
        $frecuencia_ingreso !== 'ocasional' &&
        $frecuencia_ingreso !== 'frecuente'
    ) {

        throw new Exception(
            'La frecuencia de ingreso seleccionada no es válida.'
        );
    }


    /* ==========================================================
       VALIDAR INDUCCIÓN SG-SST
    ========================================================== */

    if ($frecuencia_ingreso === 'ocasional') {

        /*
         * Para un vehículo ocasional no es obligatorio
         * realizar la inducción.
         *
         * Se guarda automáticamente como 0.
         */

        $induccion_sst = '0';
    }


    if ($frecuencia_ingreso === 'frecuente') {

        /*
         * Un vehículo frecuente DEBE tener
         * inducción SG-SST.
         */

        if ($induccion_sst !== '1') {

            throw new Exception(
                'El vehículo tiene ingreso frecuente. Debe realizar y registrar la inducción de SG-SST antes de continuar.'
            );
        }

        $induccion_sst = '1';
    }


    /* ==========================================================
       VALIDAR CÉDULA
    ========================================================== */

    if (!preg_match('/^[0-9]+$/', $cedula)) {

        throw new Exception(
            'La cédula solamente puede contener números.'
        );
    }


    if (strlen($cedula) < 5) {

        throw new Exception(
            'La cédula ingresada no es válida.'
        );
    }


    /* ==========================================================
       VALIDAR PLACA
    ========================================================== */

    if (!preg_match('/^[A-Z0-9\-]+$/', $placa)) {

        throw new Exception(
            'La placa contiene caracteres no válidos.'
        );
    }


    /* ==========================================================
       VALIDAR FECHA
    ========================================================== */

    $hoy = date('Y-m-d');


    if ($fecha > $hoy) {

        throw new Exception(
            'La fecha del registro no puede ser futura.'
        );
    }


    /* ==========================================================
       INSERTAR REGISTRO
    ========================================================== */

    $sql = "

        INSERT INTO vehiculos (

            fecha,
            nombre,
            cedula,
            eps,
            arl,
            tipo_visita,
            hora_ingreso,
            hora_salida,
            procedencia,
            destino,
            placa,
            fecha_soat,
            revision_tecn,
            licencia,
            frecuencia_ingreso,
            induccion_sst,
            registro

        )

        VALUES (

            :fecha,
            :nombre,
            :cedula,
            :eps,
            :arl,
            :tipo_visita,
            :hora_ingreso,
            :hora_salida,
            :procedencia,
            :destino,
            :placa,
            :fecha_soat,
            :revision_tecn,
            :licencia,
            :frecuencia_ingreso,
            :induccion_sst,
            :registro

        )

    ";


    $stmt = $connection->prepare(
        $sql
    );


    $stmt->execute([

        ':fecha' =>
            $fecha,

        ':nombre' =>
            $nombre,

        ':cedula' =>
            $cedula,

        ':eps' =>
            $eps,

        ':arl' =>
            $arl,

        ':tipo_visita' =>
            $tipo_visita,

        ':hora_ingreso' =>
            $hora_ingreso,

        ':hora_salida' =>
            $hora_salida,

        ':procedencia' =>
            $procedencia,

        ':destino' =>
            $destino,

        ':placa' =>
            $placa,

        ':fecha_soat' =>
            $fecha_soat,

        ':revision_tecn' =>
            $revision_tecn,

        ':licencia' =>
            $licencia,

        ':frecuencia_ingreso' =>
            $frecuencia_ingreso,

        ':induccion_sst' =>
            $induccion_sst,

        ':registro' =>
            $registro

    ]);


    /* ==========================================================
       REGISTRO EXITOSO
    ========================================================== */

    header(
        'Location: ../View/registro_vehiculos_externos.php?guardado=1'
    );

    exit;


}


/* ==========================================================
   ERROR BASE DE DATOS
========================================================== */

catch (PDOException $e) {

    $mensaje = urlencode(
        'Error de base de datos: ' .
        $e->getMessage()
    );

    header(
        "Location: ../View/registro_vehiculos_externos.php?error={$mensaje}"
    );

    exit;

}


/* ==========================================================
   ERROR DE VALIDACIÓN
========================================================== */

catch (Exception $e) {

    $mensaje = urlencode(
        $e->getMessage()
    );

    header(
        "Location: ../View/registro_vehiculos_externos.php?error={$mensaje}"
    );

    exit;
}