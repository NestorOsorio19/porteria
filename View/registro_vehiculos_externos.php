<?php

/* ==========================================================
EVITAR CACHE DEL NAVEGADOR
========================================================== */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* ==========================================================
ARCHIVOS DE CONFIGURACIÓN Y CONEXIÓN
========================================================== */
require_once '../Config/config.php';
require_once '../Config/database.php';

/* ==========================================================
MENSAJES DEL CONTROLLER
========================================================== */

$guardado = isset($_GET['guardado']) && $_GET['guardado'] === '1';

$error = $_GET['error'] ?? '';

$error = htmlspecialchars(
    $error,
    ENT_QUOTES,
    'UTF-8'
);


/* ==========================================================
CREAR CONEXIÓN PDO
========================================================== */
$connection = connection();

/* ==========================================================
CARGAR ARL PARA EL SELECT
========================================================== */
function cargarARL(PDO $connection): string
{
    $stmt = $connection->prepare("
        SELECT id_arl, nom_arl
        FROM arls
        ORDER BY nom_arl ASC
    ");

    $stmt->execute();

    $options = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = htmlspecialchars($row['id_arl'], ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars($row['nom_arl'], ENT_QUOTES, 'UTF-8');

        $options .= "<option value=\"$id\">$nombre</option>";
    }

    return $options;
}

/* ==========================================================
CARGAR EPS PARA EL SELECT
========================================================== */
function cargarEPS(PDO $connection): string
{
    $stmt = $connection->prepare("
        SELECT id_eps, nom_eps
        FROM eps
        ORDER BY nom_eps ASC
    ");

    $stmt->execute();

    $options = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = htmlspecialchars($row['id_eps'], ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars($row['nom_eps'], ENT_QUOTES, 'UTF-8');

        $options .= "<option value=\"$id\">$nombre</option>";
    }

    return $options;
}

/* ==========================================================
VALORES INICIALES
========================================================== */
$fecha_inicial = date('Y-m-d');
$ARL = cargarARL($connection);
$EPS = cargarEPS($connection);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registro Vehiculos</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
        }

        /* ======================================
    GENERAL
    ====================================== */

        body {
            background: linear-gradient(135deg, #eef2f7, #d9e7ff);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* ======================================
    NAVBAR
    ====================================== */

        .navbar {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* ======================================
    BREADCRUMB
    ====================================== */

        .breadcrumb {
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .breadcrumb a {
            text-decoration: none;
            font-weight: 500;
            color: var(--primary);
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* ======================================
    HEADER PRINCIPAL
    ====================================== */

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-weight: 700;
            color: #2c3e50;
            margin-top: 20px;
        }

        .page-header p {
            color: #6c757d;
            margin-bottom: 0;
        }

        .icon-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--danger);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            font-size: 40px;
            box-shadow: 0 10px 25px rgba(220, 53, 69, .25);
        }

        .form-container {
            max-width: 900px;
        }

        /* ======================================
       MINI TARJETAS
    ====================================== */

        .mini-card {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            min-height: 110px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
            transition: all .3s ease;
        }

        .mini-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .12);
        }

        .mini-card:hover {
            transform: translateY(-3px);
        }

        .mini-card h6 {
            color: #6c757d;
            margin-bottom: 10px;
        }

        .mini-card strong {
            color: #212529;
            font-size: 1rem;
        }

        /* ======================================
    SECCIONES DEL FORMULARIO
    ====================================== */

        .form-section {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            transition: all .3s ease;
        }

        .form-section:hover {
            transform: translateY(-2px);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border-light);
        }

        /* ======================================
    INPUTS
    ====================================== */

        .form-label {
            min-height: 48px;
            display: flex;
            align-items: flex-end;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            min-height: 48px;
        }

        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .10);
        }

        #inputsEquipoElectronico .mini-card {
            border-left: 4px solid var(--danger);
        }

        #inputsEquipoElectronico {
            display: none;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid #dee2e6;
            padding: 12px;
            transition: .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--danger);
            box-shadow: 0 0 0 .15rem rgba(220, 53, 69, .15);
        }

        /* ==========================================================
   FRECUENCIA DE INGRESO
========================================================== */

        .frecuencia-card {
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 18px;
            background: #f8f9fa;
            transition: all .3s ease;
        }

        .frecuencia-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 6px 18px rgba(13, 110, 253, .10);
        }

        .frecuencia-card .form-check {
            margin: 0;
        }

        .frecuencia-card .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }

        .frecuencia-card .form-check-label {
            cursor: pointer;
            font-weight: 600;
        }


        /* ==========================================================
   TARJETA SG-SST
========================================================== */

        .sgsst-card {
            display: none;
            margin-top: 20px;
            border-radius: 18px;
            padding: 20px;
            border: 2px solid #ffc107;
            background: linear-gradient(135deg,
                    #fffdf2,
                    #fff8d9);
            box-shadow:
                0 8px 22px rgba(255, 193, 7, .12);

            animation: aparecerSgsst .3s ease;
        }

        @keyframes aparecerSgsst {

            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }


        /* ==========================================================
   ENCABEZADO SG-SST
========================================================== */

        .sgsst-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 15px;
        }

        .sgsst-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #ffc107;
            color: #212529;

            font-size: 22px;
        }

        .sgsst-title {
            margin: 0;
            font-weight: 700;
            color: #664d03;
        }

        .sgsst-description {
            margin: 3px 0 0;
            color: #856404;
            font-size: .9rem;
        }


        /* ==========================================================
   ESTADO DE INDUCCIÓN
========================================================== */

        .sgsst-status {
            border-radius: 12px;
            padding: 12px 15px;
            margin-top: 15px;

            display: flex;
            align-items: center;
            gap: 10px;

            font-size: .9rem;
            font-weight: 600;
        }


        /* ESTADO CORRECTO */

        .sgsst-status.success {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }


        /* ESTADO ERROR */

        .sgsst-status.danger {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }


        /* ==========================================================
   SELECT SG-SST
========================================================== */

        #induccion_sgsst {
            background-color: #fff;
            font-weight: 600;
        }

        #induccion_sgsst:focus {
            border-color: #ffc107;
            box-shadow:
                0 0 0 .15rem rgba(255, 193, 7, .20);
        }


        /* ==========================================================
   FRECUENCIA SELECCIONADA
========================================================== */

        .frecuencia-card.frecuente {
            border-color: #ffc107;
            background: #fffdf2;
        }

        .frecuencia-card.ocasional {
            border-color: #198754;
            background: #f1faf5;
        }


        /* ======================================
    EVIDENCIAS
    ====================================== */

        #previewContainer img {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
        }

        /* ======================================
    BOTONES
    ====================================== */

        .btn {
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-custom {
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
        }

        /* ======================================
    BARRA DE ACCIONES
    ====================================== */

        .barra-acciones {
            position: sticky;
            bottom: 15px;
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 18px;
            margin-top: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .10);
            z-index: 999;
        }

        /* ======================================
    RESPONSIVE
    ====================================== */

        @media (max-width: 768px) {

            .page-header h1 {
                font-size: 1.6rem;
            }

            .icon-circle {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">

        <div class="container">

            <span class="navbar-brand">
                <i class="fas fa-truck me-2"></i>
                Registro Vehiculos
            </span>

        </div>

    </nav>

    <!-- ==================================================
        BREADCRUMB
    =================================================== -->
    <div class="container mt-3">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white p-3 shadow-sm">

                <li class="breadcrumb-item">
                    <a href="../index.php">
                        <i class="fas fa-home"></i>
                        Menu de Registros
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    Vehiculos Externos
                </li>
            </ol>
        </nav>

    </div>

    <main class="d-flex justify-content-center min-vh-100 py-4">
        <div class="container" style="max-width:900px">

            <div class="page-header">

                <div class="icon-circle mb-4">
                    <i class="fas fa-truck"></i>
                </div>

                <h1>Formulario Registro</h1>

                <p>
                    Registro de Visitantes, Contratistas y Proveedores con Vehiculo
                </p>

            </div>

            <form id="formulario_vehiculos" action="../Controller/ingreso_vehiculos_externos.php" method="POST">

                <!-- MINI CARDS -->
                <div class="row g-4 mb-4 justify-content-center">

                    <div class="col-md-4">
                        <div class="mini-card">
                            <h6>Formulario</h6>
                            <strong>PERSONAL CON VEHICULO</strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-card">
                            <h6>Fecha Sistema</h6>
                            <strong><?= date('d/m/Y') ?></strong>
                        </div>
                    </div>

                </div>

                <!-- DATOS GENERALES -->
                <div class="form-section">

                    <div class="section-title">
                        <i class="fa-solid fa-circle-info"></i>
                        Datos Generales
                    </div>

                    <div class="row g-3">

                        <div class="col-md-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input
                                type="date"
                                name="fecha"
                                id="fecha"
                                class="form-control"
                                value="<?= htmlspecialchars($fecha_inicial, ENT_QUOTES, 'UTF-8') ?>"
                                max="<?= htmlspecialchars($fecha_inicial, ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="cedula" class="form-label">Cedula:</label>

                            <input type="text"
                                name="cedula"
                                id="cedula"
                                class="form-control"
                                required>

                            <small id="mensajeCedula"></small>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" min="1" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="arl" class="form-label">ARL:</label>

                            <select name="arl" id="arl" class="form-select" required>
                                <option value="" disabled selected>
                                    Seleccione la ARL...
                                </option>
                                <?= $ARL ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="eps" class="form-label">EPS:</label>

                            <select name="eps" id="eps" class="form-select" required>
                                <option value="" disabled selected>
                                    Seleccione la EPS...
                                </option>
                                <?= $EPS ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="tipo_visita" class="form-label">Tipo de Visita:</label>
                            <input type="text" name="tipo_visita" id="tipo_visita" class="form-control" min="1" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="procedencia" class="form-label">Empresa de Procedencia:</label>
                            <input type="text" name="procedencia" id="procedencia" class="form-control" min="1" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="destino" class="form-label">Area a Donde Se Dirige:</label>
                            <input type="text" name="destino" id="destino" class="form-control" min="1" required>
                        </div>

                    </div>

                </div>

                <div class="form-section">

                    <div class="section-title">
                        <i class="fa-solid fa-circle-info"></i>
                        Control Ingreso Vehiculo
                    </div>

                    <div class="row g-3">

                        <div class="col-12 col-md-3">
                            <label for="placa_vehiculo" class="form-label">Placa:</label>
                            <input type="text" name="placa_vehiculo" id="placa_vehiculo" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_soat" class="form-label">Fecha Vigencia SOAT:</label>
                            <input
                                type="date"
                                name="fecha_soat"
                                id="fecha_soat"
                                class="form-control"
                                value=""
                                max=""
                                required>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_revision" class="form-label">
                                FV Revision Tecnicomecanica:
                            </label>
                            <input type="date" name="fecha_revision" id="fecha_revision"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_licencia" class="form-label">
                                FV Licencia de Conduccion:
                            </label>
                            <input type="date" name="fecha_licencia" id="fecha_licencia"
                                class="form-control" required>
                        </div>

                        <!-- ======================================================
                        FRECUENCIA DE INGRESO DEL VEHÍCULO
                        ======================================================= -->

                        <div class="col-12">

                            <div
                                class="frecuencia-card"
                                id="frecuenciaCard">

                                <div class="row g-3 align-items-center">

                                    <div class="col-12 col-md-5">

                                        <label
                                            for="frecuencia_ingreso"
                                            class="form-label mb-1">

                                            <i class="fas fa-calendar-check text-primary me-2"></i>

                                            Frecuencia de ingreso del vehículo

                                        </label>

                                        <small class="text-muted d-block">

                                            Indique con qué frecuencia ingresa este vehículo
                                            a las instalaciones.

                                        </small>

                                    </div>


                                    <div class="col-12 col-md-4">

                                        <select
                                            name="frecuencia_ingreso"
                                            id="frecuencia_ingreso"
                                            class="form-select"
                                            required>

                                            <option
                                                value=""
                                                selected
                                                disabled>

                                                Seleccione una opción...

                                            </option>

                                            <option value="ocasional">

                                                Ocasional

                                            </option>

                                            <option value="frecuente">

                                                Frecuente

                                            </option>

                                        </select>

                                    </div>


                                    <div class="col-12 col-md-3">

                                        <div
                                            id="frecuenciaInfo"
                                            class="text-muted small">

                                            <i class="fas fa-info-circle me-1"></i>

                                            Seleccione una frecuencia.

                                        </div>

                                    </div>

                                </div>


                                <!-- ==================================================
                                VALIDACIÓN SG-SST
                                =================================================== -->

                                <div
                                    id="sgsstCard"
                                    class="sgsst-card">

                                    <div class="sgsst-header">

                                        <div class="sgsst-icon">

                                            <i class="fas fa-hard-hat"></i>

                                        </div>

                                        <div>

                                            <h5 class="sgsst-title">

                                                Inducción de Seguridad y Salud en el Trabajo

                                            </h5>

                                            <p class="sgsst-description">

                                                Para ingresos frecuentes es necesario contar
                                                con la inducción de SG-SST.

                                            </p>

                                        </div>

                                    </div>


                                    <div class="row g-3 align-items-end">

                                        <div class="col-12 col-md-6">

                                            <label
                                                for="induccion_sgsst"
                                                class="form-label">

                                                ¿Recibió la inducción de SG-SST?

                                            </label>

                                            <select
                                                name="induccion_sgsst"
                                                id="induccion_sgsst"
                                                class="form-select">

                                                <option
                                                    value=""
                                                    selected
                                                    disabled>

                                                    Seleccione su respuesta...

                                                </option>

                                                <option value="1">

                                                    Sí, ya la realizó

                                                </option>

                                                <option value="0">

                                                    No, aún no la ha realizado

                                                </option>

                                            </select>

                                        </div>


                                        <div class="col-12 col-md-6">

                                            <div
                                                id="mensajeInduccion"
                                                class="sgsst-status"
                                                style="display:none;">

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ======================================================
                        FIRMAS
                        ======================================================= -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa-solid fa-signature"></i>
                                Firmas
                            </div>

                            <div class="row g-3 mb-4">

                                <div class="col-12 col-md-6">
                                    <label for="elaborado" class="form-label">Realizó:</label>
                                    <input
                                        type="text"
                                        name="registro"
                                        id="registro"
                                        class="form-control"
                                        placeholder="Nombre de quien realiza el registro"
                                        maxlength="50"
                                        required>
                                </div>

                            </div>
                        </div>

                    </div>



                </div>

                <!-- BOTONES -->
                <div class="barra-acciones">

                    <div class="text-center">

                        <button
                            type="submit"
                            class="btn btn-danger btn-lg px-5">

                            <i class="fas fa-save me-2"></i>
                            Guardar Registro

                        </button>

                        <a href="tabla_vehiculos_externos.php"
                            class="btn btn-danger btn-lg px-5">

                            <i class="fas fa-eye me-2"></i>
                            Ver Registros

                        </a>

                    </div>

                </div>

            </form>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" defer></script>

    <script>
        $(function() {

            /* ==========================================================
            CONFIGURACIÓN
            ========================================================== */

            const $form = $("#formulario_vehiculos");
            const $cedula = $("#cedula");

            let enviandoFormulario = false;
            let ultimaCedulaConsultada = "";


            /* ==========================================================
            LIMPIAR DATOS DEL VEHÍCULO
            ========================================================== */

            function limpiarDatosVehiculo() {

                $("#nombre").val("");
                $("#arl").val("");
                $("#eps").val("");
                $("#placa_vehiculo").val("");

            }


            /* ==========================================================
            LIMPIAR MENSAJE DE CÉDULA
            ========================================================== */

            function limpiarMensajeCedula() {

                $("#mensajeCedula")
                    .removeClass(
                        "text-success text-danger text-primary text-warning fw-semibold"
                    )
                    .text("");

            }


            /* ==========================================================
            SOLO NÚMEROS EN CÉDULA
            ========================================================== */

            $cedula.on("input", function() {

                this.value = this.value.replace(/\D/g, "");

                /*
                 * Permitir una nueva consulta
                 */
                ultimaCedulaConsultada = "";

                limpiarMensajeCedula();

            });


            /* ==========================================================
            CONSULTAR VEHÍCULO POR CÉDULA
            ========================================================== */

            $cedula.on("blur", function() {

                const cedula = $(this).val().trim();


                /* ======================================================
                CÉDULA VACÍA
                ====================================================== */

                if (!cedula) {

                    limpiarMensajeCedula();

                    return;

                }


                /* ======================================================
                VALIDACIÓN MÍNIMA
                ====================================================== */

                if (cedula.length < 5) {

                    $("#mensajeCedula")
                        .removeClass(
                            "text-success text-danger text-primary"
                        )
                        .addClass(
                            "text-warning fw-semibold"
                        )
                        .text(
                            "Ingrese una cédula válida."
                        );

                    return;

                }


                /* ======================================================
                EVITAR CONSULTA REPETIDA
                ====================================================== */

                if (ultimaCedulaConsultada === cedula) {

                    return;

                }


                ultimaCedulaConsultada = cedula;


                /* ======================================================
                MENSAJE DE CONSULTA
                ====================================================== */

                $("#mensajeCedula")
                    .removeClass(
                        "text-success text-danger text-warning"
                    )
                    .addClass(
                        "text-primary fw-semibold"
                    )
                    .html(
                        '<i class="fas fa-spinner fa-spin me-1"></i>' +
                        'Consultando información...'
                    );


                /* ======================================================
                AJAX
                ====================================================== */

                $.ajax({

                    url: "../Controller/buscar_vehiculo.php",

                    type: "POST",

                    data: {
                        cedula: cedula
                    },

                    dataType: "json",


                    /* ==================================================
                    RESPUESTA CORRECTA
                    ================================================== */

                    success: function(response) {

                        console.log(
                            "RESPUESTA BUSCAR VEHICULO:",
                            response
                        );


                        /* ==============================================
                        NO ENCONTRADO
                        ============================================== */

                        if (response.error === true) {

                            limpiarDatosVehiculo();


                            $("#mensajeCedula")
                                .removeClass(
                                    "text-success text-danger text-primary"
                                )
                                .addClass(
                                    "text-warning fw-semibold"
                                )
                                .html(
                                    '<i class="fas fa-triangle-exclamation me-1"></i>' +
                                    (
                                        response.mensaje ||
                                        "Cédula no encontrada. Complete la información."
                                    )
                                );

                            return;

                        }


                        /* ==============================================
                        DATOS PERSONALES
                        ============================================== */

                        $("#nombre").val(
                            response.nombre || ""
                        );


                        /* ==============================================
                        ARL
                        ============================================== */

                        $("#arl").val(
                            response.arl || ""
                        );


                        /* ==============================================
                        EPS
                        ============================================== */

                        $("#eps").val(
                            response.eps || ""
                        );


                        /* ==============================================
                        PLACA
                        ============================================== */

                        $("#placa_vehiculo").val(
                            response.placa || ""
                        );


                        /* ==============================================
                        MENSAJE DE ÉXITO
                        ============================================== */

                        $("#mensajeCedula")
                            .removeClass(
                                "text-primary text-danger text-warning"
                            )
                            .addClass(
                                "text-success fw-semibold"
                            )
                            .html(
                                '<i class="fas fa-check-circle me-1"></i>' +
                                'Vehículo encontrado. Datos cargados correctamente.'
                            );

                    },


                    /* ==================================================
                    ERROR AJAX
                    ================================================== */

                    error: function(xhr, status, error) {

                        console.error(
                            "========== ERROR AJAX =========="
                        );

                        console.error(
                            "HTTP:",
                            xhr.status
                        );

                        console.error(
                            "STATUS:",
                            status
                        );

                        console.error(
                            "ERROR:",
                            error
                        );

                        console.error(
                            "RESPUESTA:",
                            xhr.responseText
                        );

                        console.error(
                            "================================"
                        );


                        /*
                         * Permitir volver a consultar
                         */
                        ultimaCedulaConsultada = "";


                        $("#mensajeCedula")
                            .removeClass(
                                "text-success text-primary text-warning"
                            )
                            .addClass(
                                "text-danger fw-semibold"
                            )
                            .html(
                                '<i class="fas fa-circle-exclamation me-1"></i>' +
                                'Error al consultar la información del vehículo.'
                            );

                    }

                });

            });


            /* ==========================================================
            VALIDACIÓN DE FECHA
            ========================================================== */

            $("#fecha").on("change", function() {

                const valor = this.value;


                if (!valor) {

                    return;

                }


                const partes = valor.split("-");


                if (partes.length !== 3) {

                    this.value = "";

                    return;

                }


                /*
                 * Crear fecha sin problemas de zona horaria
                 */

                const fechaSeleccionada = new Date(
                    Number(partes[0]),
                    Number(partes[1]) - 1,
                    Number(partes[2])
                );


                const hoy = new Date();

                hoy.setHours(
                    0,
                    0,
                    0,
                    0
                );


                if (fechaSeleccionada > hoy) {

                    Swal.fire({

                        icon: "warning",

                        title: "Fecha inválida",

                        text: "No se permiten fechas futuras.",

                        confirmButtonColor: "#ffc107",

                        confirmButtonText: "Aceptar"

                    });


                    this.value = "";

                }

            });


            /* ==========================================================
            FRECUENCIA DE INGRESO DEL VEHÍCULO
            ========================================================== */

            $("#frecuencia_ingreso").on("change", function() {

                const frecuencia = $(this).val();

                const $card =
                    $("#frecuenciaCard");

                const $sgsst =
                    $("#sgsstCard");

                const $induccion =
                    $("#induccion_sgsst");

                const $info =
                    $("#frecuenciaInfo");

                const $mensaje =
                    $("#mensajeInduccion");


                /* ======================================================
                   LIMPIAR ESTADO ANTERIOR
                ====================================================== */

                $card
                    .removeClass(
                        "ocasional frecuente"
                    );


                $info
                    .removeClass(
                        "text-success text-warning text-danger text-muted"
                    );


                $mensaje
                    .stop(true, true)
                    .hide()
                    .removeClass(
                        "success danger"
                    )
                    .html("");


                /* ======================================================
                   SIN SELECCIÓN
                ====================================================== */

                if (!frecuencia) {

                    $sgsst
                        .stop(true, true)
                        .slideUp(200);

                    $induccion
                        .prop("required", false)
                        .val("");

                    return;

                }


                /* ======================================================
                   INGRESO OCASIONAL
                ====================================================== */

                if (frecuencia === "ocasional") {

                    $card
                        .addClass("ocasional");


                    $info
                        .addClass("text-success")
                        .html(
                            '<i class="fas fa-check-circle me-1"></i>' +
                            '<strong>Ingreso ocasional.</strong> ' +
                            'No requiere validación adicional de SG-SST.'
                        );


                    $sgsst
                        .stop(true, true)
                        .slideUp(250);


                    $induccion
                        .prop("required", false)
                        .val("");


                    return;

                }


                /* ======================================================
                   INGRESO FRECUENTE
                ====================================================== */

                if (frecuencia === "frecuente") {

                    $card
                        .addClass("frecuente");


                    $info
                        .addClass("text-warning")
                        .html(
                            '<i class="fas fa-triangle-exclamation me-1"></i>' +
                            '<strong>Ingreso frecuente.</strong> ' +
                            'Este vehículo debe contar con inducción de SG-SST.'
                        );


                    $sgsst
                        .stop(true, true)
                        .slideDown(300);


                    $induccion
                        .prop("required", true);


                    /*
                     * No seleccionar automáticamente
                     */
                    $induccion.val("");


                    return;

                }

            });


            /* ==========================================================
               VALIDAR INDUCCIÓN SG-SST
            ========================================================== */

            $("#induccion_sgsst").on("change", function() {

                const frecuencia =
                    $("#frecuencia_ingreso").val();

                const induccion =
                    $(this).val();

                const $mensaje =
                    $("#mensajeInduccion");


                /* ======================================================
                   SOLO APLICA PARA INGRESO FRECUENTE
                ====================================================== */

                if (frecuencia !== "frecuente") {

                    $mensaje
                        .stop(true, true)
                        .hide();

                    return;

                }


                /* ======================================================
                   INDUCCIÓN REALIZADA
                ====================================================== */

                if (induccion === "1") {

                    $mensaje
                        .stop(true, true)
                        .removeClass("danger")
                        .addClass("success")
                        .html(

                            '<div class="d-flex align-items-start gap-2">' +

                            '<i class="fas fa-circle-check"></i>' +

                            '<div>' +

                            '<strong>Inducción SG-SST validada</strong>' +

                            '<br>' +

                            '<span>' +
                            'La persona cuenta con la inducción requerida. ' +
                            'Puede continuar con el proceso de ingreso.' +
                            '</span>' +

                            '</div>' +

                            '</div>'

                        )
                        .fadeIn(250);

                    return;

                }


                /* ======================================================
                   INDUCCIÓN NO REALIZADA
                ====================================================== */

                if (induccion === "0") {

                    $mensaje
                        .stop(true, true)
                        .removeClass("success")
                        .addClass("danger")
                        .html(

                            '<div class="d-flex align-items-start gap-2">' +

                            '<i class="fas fa-triangle-exclamation"></i>' +

                            '<div>' +

                            '<strong>Ingreso no autorizado</strong>' +

                            '<br>' +

                            '<span>' +
                            'Este vehículo tiene ingreso frecuente y ' +
                            'debe realizar primero la inducción de SG-SST.' +
                            '</span>' +

                            '</div>' +

                            '</div>'

                        )
                        .fadeIn(250);

                }

            });


            /* ==========================================================
               ENVÍO DEL FORMULARIO
            ========================================================== */

            $form.on("submit", function(e) {

                /*
                 * Si ya fue confirmado,
                 * permitir envío normal.
                 */

                if (enviandoFormulario) {

                    return;

                }


                e.preventDefault();


                /* ======================================================
                   VALIDACIÓN HTML5
                ====================================================== */

                if (!this.checkValidity()) {

                    this.reportValidity();

                    return;

                }


                /* ======================================================
                   VALIDAR FRECUENCIA
                ====================================================== */

                const frecuenciaIngreso =
                    $("#frecuencia_ingreso").val();


                const induccionSGSST =
                    $("#induccion_sgsst").val();


                /* ======================================================
                   VALIDAR FRECUENCIA SELECCIONADA
                ====================================================== */

                if (!frecuenciaIngreso) {

                    Swal.fire({

                        icon: "warning",

                        title: "Frecuencia requerida",

                        text: "Debe indicar con qué frecuencia ingresa el vehículo.",

                        confirmButtonColor: "#ffc107",

                        confirmButtonText: "Aceptar"

                    }).then(function() {

                        $("#frecuencia_ingreso").focus();

                    });

                    return;

                }


                /* ======================================================
                   VALIDAR INDUCCIÓN PARA FRECUENTES
                ====================================================== */

                if (
                    frecuenciaIngreso === "frecuente" &&
                    induccionSGSST !== "1"
                ) {

                    /*
                     * Mostrar nuevamente el mensaje visual
                     */
                    $("#mensajeInduccion")
                        .stop(true, true)
                        .removeClass("success")
                        .addClass("danger")
                        .html(

                            '<div class="d-flex align-items-start gap-2">' +

                            '<i class="fas fa-triangle-exclamation"></i>' +

                            '<div>' +

                            '<strong>Debe realizar la inducción SG-SST</strong>' +

                            '<br>' +

                            '<span>' +
                            'No es posible guardar el registro hasta ' +
                            'seleccionar que la inducción fue realizada.' +
                            '</span>' +

                            '</div>' +

                            '</div>'

                        )
                        .fadeIn(250);


                    Swal.fire({

                        icon: "warning",

                        title: "Inducción SG-SST requerida",

                        text: "Este vehículo tiene un ingreso frecuente. " +
                            "Debe realizar y registrar la inducción de SG-SST " +
                            "antes de continuar.",

                        confirmButtonColor: "#ffc107",

                        confirmButtonText: "Entendido"

                    }).then(function() {

                        $("#induccion_sgsst").focus();

                    });

                    return;

                }


                /* ======================================================
                   REFERENCIA AL FORMULARIO
                ====================================================== */

                const formulario = this;


                const $botonGuardar =
                    $form.find(
                        'button[type="submit"]'
                    );


                /* ======================================================
                   CONFIRMACIÓN FINAL
                ====================================================== */

                Swal.fire({

                    title: "¿Guardar registro?",

                    html: "Se registrará el ingreso del vehículo en la base de datos.",

                    icon: "question",

                    showCancelButton: true,

                    confirmButtonColor: "#dc3545",

                    cancelButtonColor: "#6c757d",

                    confirmButtonText: '<i class="fas fa-save me-1"></i> Sí, guardar',

                    cancelButtonText: "Cancelar",

                    reverseButtons: true

                }).then(function(result) {

                    if (!result.isConfirmed) {

                        return;

                    }


                    /* ==================================================
                       PROTECCIÓN CONTRA DOBLE ENVÍO
                    ================================================== */

                    enviandoFormulario = true;


                    /* ==================================================
                       DESACTIVAR BOTÓN
                    ================================================== */

                    $botonGuardar

                        .prop(
                            "disabled",
                            true
                        )

                        .html(

                            '<span class="spinner-border spinner-border-sm me-2"></span>' +

                            'Guardando...'

                        );


                    /* ==================================================
                       SUBMIT NATIVO
                    ================================================== */

                    HTMLFormElement.prototype.submit.call(
                        formulario
                    );

                });

            });


            /* ==========================================================
               MENSAJE DE ÉXITO DESDE PHP
            ========================================================== */

            <?php if ($guardado): ?>

                Swal.fire({

                    icon: "success",

                    title: "¡Registro exitoso!",

                    text: "El vehículo fue registrado correctamente.",

                    confirmButtonColor: "#198754",

                    confirmButtonText: "Aceptar"

                }).then(function() {

                    const url =
                        new URL(
                            window.location.href
                        );

                    url.searchParams.delete(
                        "guardado"
                    );

                    window.history.replaceState({},
                        document.title,
                        url.pathname + url.search
                    );

                });

            <?php endif; ?>


            /* ==========================================================
               MENSAJE DE ERROR DESDE PHP
            ========================================================== */

            <?php if ($error !== ''): ?>

                Swal.fire({

                    icon: "error",

                    title: "No fue posible guardar",

                    text: <?= json_encode(
                                $error,
                                JSON_UNESCAPED_UNICODE
                            ) ?>,

                    confirmButtonColor: "#dc3545",

                    confirmButtonText: "Aceptar"

                }).then(function() {

                    const url =
                        new URL(
                            window.location.href
                        );

                    url.searchParams.delete(
                        "error"
                    );

                    window.history.replaceState({},
                        document.title,
                        url.pathname + url.search
                    );

                });

            <?php endif; ?>


        });
    </script>

</body>

</html>