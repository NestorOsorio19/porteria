<?php

/* ==========================================================
INICIAR SESIÓN
========================================================== */
session_start();

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

        $id = htmlspecialchars(
            (string)$row['id_arl'],
            ENT_QUOTES,
            'UTF-8'
        );

        $nombre = htmlspecialchars(
            (string)$row['nom_arl'],
            ENT_QUOTES,
            'UTF-8'
        );

        $options .= "<option value=\"{$id}\">{$nombre}</option>";
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

        $id = htmlspecialchars(
            (string)$row['id_eps'],
            ENT_QUOTES,
            'UTF-8'
        );

        $nombre = htmlspecialchars(
            (string)$row['nom_eps'],
            ENT_QUOTES,
            'UTF-8'
        );

        $options .= "<option value=\"{$id}\">{$nombre}</option>";
    }

    return $options;
}

/* ==========================================================
CARGAR ÁREA PARA EL SELECT
========================================================== */
function cargarArea(PDO $connection): string
{
    $stmt = $connection->prepare("
        SELECT id_area, nom_area
        FROM areas
        ORDER BY nom_area ASC
    ");

    $stmt->execute();

    $options = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = htmlspecialchars(
            (string)$row['id_area'],
            ENT_QUOTES,
            'UTF-8'
        );

        $nombre = htmlspecialchars(
            (string)$row['nom_area'],
            ENT_QUOTES,
            'UTF-8'
        );

        $options .= "<option value=\"{$id}\">{$nombre}</option>";
    }

    return $options;
}

/* ==========================================================
CARGAR EMPRESAS PARA EL SELECT
========================================================== */
function cargarEmpresa(PDO $connection): string
{
    $stmt = $connection->prepare("
        SELECT id_registro, nom_empresa
        FROM empresas
        ORDER BY nom_empresa ASC
    ");

    $stmt->execute();

    $options = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = htmlspecialchars(
            (string)$row['id_registro'],
            ENT_QUOTES,
            'UTF-8'
        );

        $nombre = htmlspecialchars(
            (string)$row['nom_empresa'],
            ENT_QUOTES,
            'UTF-8'
        );

        $options .= "<option value=\"{$id}\">{$nombre}</option>";
    }

    return $options;
}

/* ==========================================================
VALORES INICIALES
========================================================== */
$fecha_inicial = date('Y-m-d');

$ARL = cargarARL($connection);
$EPS = cargarEPS($connection);
$Area = cargarArea($connection);
$empresa = cargarEmpresa($connection);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Registro Contratistas</title>

    <!-- ==================================================
    BOOTSTRAP CSS
    =================================================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <!-- ==================================================
    FONT AWESOME
    =================================================== -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- ==================================================
    JQUERY
    =================================================== -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ==================================================
    SWEETALERT2
    =================================================== -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ==================================================
        VARIABLES
        ================================================== */

        :root {
            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --border-light: #e9ecef;
        }


        /* ==================================================
        GENERAL
        ================================================== */

        body {
            background: linear-gradient(135deg,
                    #eef2f7,
                    #d9e7ff);

            min-height: 100vh;

            font-family: 'Segoe UI', sans-serif;
        }


        label {
            font-weight: 600;
            margin-bottom: 8px;
        }


        /* ==================================================
        NAVBAR
        ================================================== */

        .navbar {
            box-shadow:
                0 4px 12px rgba(0, 0, 0, .1);
        }


        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
        }


        /* ==================================================
        BREADCRUMB
        ================================================== */

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


        /* ==================================================
        HEADER PRINCIPAL
        ================================================== */

        .form-label {
            min-height: 48px;

            display: flex;

            align-items: flex-end;

            font-weight: 600;

            margin-bottom: 8px;
        }


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

            box-shadow:
                0 10px 25px rgba(220, 53, 69, .25);
        }


        /* ==================================================
        CONTENEDOR
        ================================================== */

        .form-container {
            max-width: 900px;
        }


        /* ==================================================
        MINI CARDS
        ================================================== */

        .mini-card {
            background: #fff;

            border-radius: 20px;

            padding: 20px;

            text-align: center;

            min-height: 110px;

            display: flex;

            flex-direction: column;

            justify-content: center;

            box-shadow:
                0 6px 18px rgba(0, 0, 0, .08);

            transition: all .3s ease;
        }


        .mini-card:hover {
            transform: translateY(-3px);

            box-shadow:
                0 12px 28px rgba(0, 0, 0, .12);
        }


        .mini-card h6 {
            color: #6c757d;
            margin-bottom: 10px;
        }


        .mini-card strong {
            color: #212529;
            font-size: 1rem;
        }


        /* ==================================================
        SECCIONES DEL FORMULARIO
        ================================================== */

        .form-section {
            background: #fff;

            border-radius: 20px;

            padding: 30px;

            margin-bottom: 25px;

            border: none;

            box-shadow:
                0 10px 25px rgba(0, 0, 0, .08);

            transition: all .3s ease;
        }


        .form-section:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 30px rgba(0, 0, 0, .10);
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

            border-bottom:
                2px solid var(--border-light);
        }


        /* ==================================================
        INPUTS
        ================================================== */

        .form-control,
        .form-select {
            min-height: 48px;

            border-radius: 12px;

            border: 1px solid #dee2e6;

            padding: 12px;

            transition: .2s;
        }


        .form-control:focus,
        .form-select:focus {
            border-color: var(--danger);

            box-shadow:
                0 0 0 .15rem rgba(220, 53, 69, .15);
        }


        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }


        /* ==================================================
        EQUIPO ELECTRÓNICO
        ================================================== */

        #inputsEquipoElectronico {
            display: none;
        }


        #inputsEquipoElectronico .mini-card {
            border-left:
                4px solid var(--danger);
        }


        /* ==================================================
        BOTONES
        ================================================== */

        .btn {
            border-radius: 12px;
            font-weight: 600;
        }


        .btn-custom {
            border-radius: 30px;

            padding: 12px 25px;

            font-weight: 600;
        }


        /* ==================================================
        BARRA DE ACCIONES
        ================================================== */

        .barra-acciones {
            position: sticky;

            bottom: 15px;

            background:
                rgba(255, 255, 255, .97);

            backdrop-filter: blur(10px);

            border-radius: 20px;

            padding: 18px;

            margin-top: 30px;

            box-shadow:
                0 10px 25px rgba(0, 0, 0, .10);

            z-index: 999;
        }


        /* ==================================================
        MENSAJES
        ================================================== */

        #mensajeCedula {
            display: block;
            margin-top: 5px;
            font-weight: 600;
        }


        /* ==================================================
        RESPONSIVE
        ================================================== */

        @media (max-width: 768px) {

            .page-header h1 {
                font-size: 1.6rem;
            }


            .icon-circle {
                width: 80px;
                height: 80px;

                font-size: 30px;
            }


            .barra-acciones .btn {
                width: 100%;
                margin-bottom: 10px;
            }


            .barra-acciones .btn:last-child {
                margin-bottom: 0;
            }

        }
    </style>

</head>


<body>


    <!-- ==================================================
    NAVBAR
    ================================================== -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">

        <div class="container">

            <span class="navbar-brand">

                <i class="fas fa-briefcase me-2"></i>

                Registro Contratistas

            </span>

        </div>

    </nav>


    <!-- ==================================================
    BREADCRUMB
    ================================================== -->

    <div class="container mt-3">

        <nav aria-label="breadcrumb">

            <ol class="breadcrumb bg-white p-3 shadow-sm">

                <li class="breadcrumb-item">

                    <a href="../index.php">

                        <i class="fas fa-briefcase"></i>

                        Menú de Registros

                    </a>

                </li>


                <li class="breadcrumb-item active">

                    Contratistas

                </li>

            </ol>

        </nav>

    </div>


    <!-- ==================================================
    CONTENIDO PRINCIPAL
    ================================================== -->

    <main class="d-flex justify-content-center min-vh-100 py-4">

        <div
            class="container"
            style="max-width:900px;">


            <!-- ==================================================
            HEADER
            ================================================== -->

            <div class="page-header">

                <div class="icon-circle mb-4">

                    <i class="fas fa-briefcase"></i>

                </div>


                <h1>
                    Formulario Registro Contratistas
                </h1>


                <p>
                    Registro y Seguimiento de los Contratistas
                    que Ingresan a Planta
                </p>

            </div>


            <!-- ==================================================
            FORMULARIO
            ================================================== -->

            <form
                action="../Controller/ingreso_contratistas.php"
                method="POST"
                id="formContratista">


                <!-- ==================================================
                MINI CARDS
                ================================================== -->

                <div class="row g-4 mb-4 justify-content-center">


                    <div class="col-12 col-md-4">

                        <div class="mini-card">

                            <h6>
                                Formulario
                            </h6>

                            <strong>
                                CONTRATISTAS
                            </strong>

                        </div>

                    </div>


                    <div class="col-12 col-md-4">

                        <div class="mini-card">

                            <h6>
                                Fecha Sistema
                            </h6>

                            <strong>
                                <?= date('d/m/Y') ?>
                            </strong>

                        </div>

                    </div>


                </div>


                <!-- ==================================================
                SECCIÓN DATOS GENERALES
                ================================================== -->

                <div class="form-section">


                    <div class="section-title">

                        <i class="fa-solid fa-circle-info"></i>

                        Datos Generales

                    </div>


                    <div class="row g-3">


                        <!-- FECHA -->

                        <div class="col-12 col-md-3">

                            <label
                                for="fecha"
                                class="form-label">

                                Fecha

                            </label>


                            <input
                                type="date"
                                name="fecha"
                                id="fecha"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                            $fecha_inicial,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                max="<?= htmlspecialchars(
                                            $fecha_inicial,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                required>

                        </div>


                        <!-- CÉDULA -->

                        <div class="col-12 col-md-3">

                            <label
                                for="cedula"
                                class="form-label">

                                Cédula:

                            </label>


                            <input
                                type="text"
                                name="cedula"
                                id="cedula"
                                class="form-control"
                                inputmode="numeric"
                                pattern="[0-9]+"
                                maxlength="15"
                                autocomplete="off"
                                required>


                            <small
                                id="mensajeCedula">
                            </small>

                        </div>


                        <!-- NOMBRE -->

                        <div class="col-12 col-md-3">

                            <label
                                for="nombre"
                                class="form-label">

                                Nombre:

                            </label>


                            <input
                                type="text"
                                name="nombre"
                                id="nombre"
                                class="form-control"
                                minlength="2"
                                maxlength="100"
                                required>

                        </div>


                        <!-- RH -->

                        <div class="col-12 col-md-3">

                            <label
                                for="rh"
                                class="form-label">

                                Tipo de Sangre:

                            </label>


                            <select
                                name="rh"
                                id="rh"
                                class="form-select"
                                required>

                                <option
                                    value=""
                                    disabled
                                    selected>

                                    Tipo de Sangre...

                                </option>


                                <option value="O-">
                                    O -
                                </option>

                                <option value="O+">
                                    O +
                                </option>

                                <option value="A-">
                                    A -
                                </option>

                                <option value="A+">
                                    A +
                                </option>

                                <option value="B-">
                                    B -
                                </option>

                                <option value="B+">
                                    B +
                                </option>

                                <option value="AB-">
                                    AB -
                                </option>

                                <option value="AB+">
                                    AB +
                                </option>

                            </select>

                        </div>


                        <!-- ARL -->

                        <div class="col-12 col-md-3">

                            <label
                                for="arl"
                                class="form-label">

                                ARL:

                            </label>


                            <select
                                name="arl"
                                id="arl"
                                class="form-select"
                                required>

                                <option
                                    value=""
                                    disabled
                                    selected>

                                    Seleccione la ARL...

                                </option>


                                <?= $ARL ?>

                            </select>

                        </div>


                        <!-- EPS -->

                        <div class="col-12 col-md-3">

                            <label
                                for="eps"
                                class="form-label">

                                EPS:

                            </label>


                            <select
                                name="eps"
                                id="eps"
                                class="form-select"
                                required>

                                <option
                                    value=""
                                    disabled
                                    selected>

                                    Seleccione la EPS...

                                </option>


                                <?= $EPS ?>

                            </select>

                        </div>


                        <!-- EMPRESA -->

                        <div class="col-12 col-md-3">

                            <label
                                for="empresa"
                                class="form-label">

                                Empresa:

                            </label>


                            <select
                                name="empresa"
                                id="empresa"
                                class="form-select"
                                required>

                                <option
                                    value=""
                                    disabled
                                    selected>

                                    Seleccione la Empresa...

                                </option>


                                <?= $empresa ?>


                                <option value="otra">
                                    OTRA...
                                </option>

                            </select>

                        </div>


                        <!-- NUEVA EMPRESA -->

                        <div
                            class="col-12 col-md-6"
                            id="nuevaEmpresaContainer"
                            style="display:none;">

                            <label
                                for="nueva_empresa"
                                class="form-label">

                                Nombre de la nueva empresa:

                            </label>


                            <input
                                type="text"
                                name="nueva_empresa"
                                id="nueva_empresa"
                                class="form-control"
                                maxlength="150"
                                placeholder="Ingrese el nombre de la empresa">

                        </div>


                        <!-- ENFERMEDADES / ALERGIAS -->

                        <div class="col-12">

                            <label
                                for="enfermedad_alergia"
                                class="form-label">

                                Enfermedades o alergias:

                            </label>


                            <textarea
                                name="enfermedad_alergia"
                                id="enfermedad_alergia"
                                class="form-control"
                                rows="3"
                                placeholder="Descríbalas acá..."
                                required></textarea>

                        </div>


                        <!-- CONTACTO EMERGENCIA -->

                        <div class="col-12 col-md-3">

                            <label
                                for="nombre_emergencia"
                                class="form-label">

                                Contacto de Emergencia:

                            </label>


                            <input
                                type="text"
                                name="nombre_emergencia"
                                id="nombre_emergencia"
                                class="form-control"
                                minlength="2"
                                maxlength="100"
                                required>

                        </div>


                        <!-- TELÉFONO EMERGENCIA -->

                        <div class="col-12 col-md-3">

                            <label
                                for="telefono_emergencia"
                                class="form-label">

                                Número de Emergencia:

                            </label>


                            <input
                                type="tel"
                                name="telefono_emergencia"
                                id="telefono_emergencia"
                                class="form-control"
                                inputmode="numeric"
                                pattern="[0-9]+"
                                maxlength="15"
                                required>

                        </div>


                        <!-- INDUCCIÓN SG-SST -->

                        <div class="col-12 col-md-3">

                            <label
                                for="induccion_sgsst"
                                class="form-label">

                                Recibió inducción de SG-SST:

                            </label>


                            <select
                                name="induccion_sgsst"
                                id="induccion_sgsst"
                                class="form-select"
                                required>

                                <option
                                    value=""
                                    disabled
                                    selected>

                                    Seleccione su respuesta

                                </option>


                                <option value="1">
                                    SI
                                </option>


                                <option value="0">
                                    NO
                                </option>

                            </select>

                        </div>


                        <!-- ==================================================
                        EQUIPO ELECTRÓNICO
                        ================================================== -->

                        <div class="col-12">


                            <div class="form-check form-switch mt-3">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="equipo"
                                    id="equipoElectronicoCheckbox"
                                    value="SI">


                                <label
                                    class="form-check-label fw-semibold"
                                    for="equipoElectronicoCheckbox">

                                    <i class="fas fa-laptop me-2 text-danger"></i>

                                    Ingreso de equipo electrónico

                                </label>

                            </div>


                            <div
                                id="inputsEquipoElectronico"
                                class="row g-3 mt-1">


                                <div class="col-12">

                                    <div
                                        class="mini-card text-start w-100"
                                        style="max-width:700px;">


                                        <h6 class="mb-3">

                                            <i class="fas fa-laptop me-2 text-danger"></i>

                                            Información del Equipo

                                        </h6>


                                        <div class="row g-3">


                                            <!-- MARCA -->

                                            <div class="col-12 col-md-6">

                                                <label
                                                    for="marca"
                                                    class="form-label">

                                                    Marca

                                                </label>


                                                <input
                                                    type="text"
                                                    name="marca"
                                                    id="marca"
                                                    class="form-control"
                                                    maxlength="100">

                                            </div>


                                            <!-- SERIAL -->

                                            <div class="col-12 col-md-6">

                                                <label
                                                    for="serial"
                                                    class="form-label">

                                                    Serial

                                                </label>


                                                <input
                                                    type="text"
                                                    name="serial"
                                                    id="serial"
                                                    class="form-control"
                                                    maxlength="100">

                                            </div>


                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- HORA INGRESO -->

                        <div class="col-12 col-md-3">

                            <label
                                for="ingreso"
                                class="form-label">

                                Hora de ingreso:

                            </label>


                            <input
                                type="time"
                                name="ingreso"
                                id="ingreso"
                                class="form-control"
                                required>

                        </div>


                    </div>

                </div>


                <!-- ==================================================
                BARRA DE ACCIONES
                ================================================== -->

                <div class="barra-acciones">

                    <div
                        class="text-center d-flex flex-wrap justify-content-center gap-2">


                        <!-- GUARDAR -->

                        <button
                            type="submit"
                            class="btn btn-danger btn-lg px-5">

                            <i class="fas fa-save me-2"></i>

                            Guardar Registro

                        </button>


                        <!-- VER REGISTROS -->

                        <a
                            href="tabla_contratistas.php"
                            class="btn btn-danger btn-lg px-5">

                            <i class="fas fa-eye me-2"></i>

                            Ver Registros

                        </a>


                    </div>

                </div>


            </form>


        </div>

    </main>


    <!-- ==================================================
    BOOTSTRAP JS
    ================================================== -->

    <script rc="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        $(function() {

            /* ==========================================================
               CONFIGURACIÓN
            ========================================================== */

            const $form = $("#formContratista");
            const $cedula = $("#cedula");

            let ultimaCedulaConsultada = "";
            let enviandoFormulario = false;


            /* ==========================================================
               LIMPIAR DATOS DEL CONTRATISTA
            ========================================================== */

            function limpiarDatosContratista() {

                $("#nombre").val("");

                $("#rh").val("");

                $("#arl").val("");

                $("#eps").val("");

                $("#empresa").val("");

                $("#nombre_emergencia").val("");

                $("#telefono_emergencia").val("");

                $("#induccion_sgsst").val("");

                $("#enfermedad_alergia").val("");

                $("#ingreso").val("");

                $("#nueva_empresa")
                    .val("")
                    .prop("required", false);

                $("#nuevaEmpresaContainer").hide();

            }


            /* ==========================================================
               CÉDULA - SOLO NÚMEROS
            ========================================================== */

            $cedula.on("input", function() {

                this.value =
                    this.value.replace(/\D/g, "");


                /*
                 * Al modificar la cédula,
                 * permitimos realizar una nueva consulta.
                 */

                ultimaCedulaConsultada = "";


                /*
                 * Limpiar mensaje anterior.
                 */

                $("#mensajeCedula")
                    .removeClass(
                        "text-success text-danger text-primary text-warning"
                    )
                    .text("");

            });


            /* ==========================================================
               CONSULTAR CONTRATISTA POR CÉDULA
            ========================================================== */

            $cedula.on("blur", function() {

                const cedula =
                    $(this).val().trim();


                /* ======================================================
                   CÉDULA VACÍA
                ====================================================== */

                if (!cedula) {

                    $("#mensajeCedula")
                        .removeClass(
                            "text-success text-danger text-primary text-warning"
                        )
                        .text("");

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
                   EVITAR CONSULTAR LA MISMA CÉDULA
                ====================================================== */

                if (
                    ultimaCedulaConsultada === cedula
                ) {

                    return;

                }


                /*
                 * Guardamos la cédula consultada.
                 */

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
                    .text(
                        "Consultando..."
                    );


                /* ======================================================
                   AJAX
                ====================================================== */

                $.ajax({

                    url: "../Controller/buscar_contratistas.php",

                    method: "POST",

                    data: {
                        cedula: cedula
                    },

                    dataType: "json",


                    /* ==================================================
                       RESPUESTA CORRECTA
                    ================================================== */

                    success: function(response) {

                        console.log(
                            "RESPUESTA BUSCAR CONTRATISTA:",
                            response
                        );


                        /* ==============================================
                           CONTRATISTA NO ENCONTRADO
                        ============================================== */

                        if (
                            response.error === true
                        ) {


                            /*
                             * Limpiar datos anteriores.
                             */

                            limpiarDatosContratista();


                            /*
                             * Mostrar mensaje debajo de la cédula.
                             */

                            $("#mensajeCedula")
                                .removeClass(
                                    "text-success text-danger text-primary"
                                )
                                .addClass(
                                    "text-warning fw-semibold"
                                )
                                .text(
                                    response.mensaje ||
                                    "Cédula no encontrada. Complete la información."
                                );


                            /*
                             * NO mostramos SweetAlert.
                             *
                             * El mensaje queda directamente
                             * debajo de la cédula.
                             */

                            return;
                        }


                        /* ==============================================
                           DATOS PERSONALES
                        ============================================== */

                        $("#nombre")
                            .val(
                                response.nombre || ""
                            );


                        $("#rh")
                            .val(
                                response.rh || ""
                            );


                        /* ==============================================
                           SEGURIDAD SOCIAL
                        ============================================== */

                        $("#arl")
                            .val(
                                response.arl || ""
                            );


                        $("#eps")
                            .val(
                                response.eps || ""
                            );


                        /* ==============================================
                           EMPRESA
                        ============================================== */

                        $("#empresa")
                            .val(
                                response.empresa || ""
                            );


                        /* ==============================================
                           CONTACTO DE EMERGENCIA
                        ============================================== */

                        $("#nombre_emergencia")
                            .val(
                                response.nombre_emergencia || ""
                            );


                        $("#telefono_emergencia")
                            .val(
                                response.telefono_emergencia || ""
                            );


                        /* ==============================================
                           INDUCCIÓN SG-SST
                        ============================================== */

                        $("#induccion_sgsst")
                            .val(
                                response.induccion_sgsst || ""
                            );


                        /* ==============================================
                           ENFERMEDADES / ALERGIAS
                        ============================================== */

                        $("#enfermedad_alergia")
                            .val(
                                response.enfermedad_alergia || ""
                            );


                        /* ==============================================
                           HORA DE INGRESO
                        ============================================== */

                        $("#ingreso")
                            .val(
                                response.ingreso || ""
                            );


                        /* ==============================================
                           EMPRESA "OTRA"
                        ============================================== */

                        if (
                            response.empresa === "otra"
                        ) {

                            $("#nuevaEmpresaContainer")
                                .stop(true, true)
                                .slideDown();

                            $("#nueva_empresa")
                                .prop(
                                    "required",
                                    true
                                );

                        } else {

                            $("#nuevaEmpresaContainer")
                                .stop(true, true)
                                .slideUp();

                            $("#nueva_empresa")
                                .prop(
                                    "required",
                                    false
                                )
                                .val("");

                        }


                        /* ==============================================
                           MENSAJE DE ÉXITO DE CONSULTA
                        ============================================== */

                        $("#mensajeCedula")
                            .removeClass(
                                "text-primary text-danger text-warning"
                            )
                            .addClass(
                                "text-success fw-semibold"
                            )
                            .text(
                                "Contratista encontrado. Datos cargados."
                            );

                    },


                    /* ==================================================
                       ERROR AJAX
                    ================================================== */

                    error: function(
                        xhr,
                        status,
                        error
                    ) {

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
                         * la misma cédula si hubo error.
                         */

                        ultimaCedulaConsultada = "";


                        $("#mensajeCedula")
                            .removeClass(
                                "text-success text-primary text-warning"
                            )
                            .addClass(
                                "text-danger fw-semibold"
                            )
                            .text(
                                "Error al consultar el contratista."
                            );


                        Swal.fire({

                            icon: "error",

                            title: "Error",

                            text: "Hubo un error al consultar la base de datos.",

                            confirmButtonColor: "#dc3545",

                            confirmButtonText: "Aceptar"

                        });

                    }

                });

            });


            /* ==========================================================
               MOSTRAR / OCULTAR NUEVA EMPRESA
            ========================================================== */

            $("#empresa").on("change", function() {

                const empresa =
                    $(this).val();


                if (
                    empresa === "otra"
                ) {

                    $("#nuevaEmpresaContainer")
                        .stop(true, true)
                        .slideDown();


                    $("#nueva_empresa")
                        .prop(
                            "required",
                            true
                        );

                } else {

                    $("#nuevaEmpresaContainer")
                        .stop(true, true)
                        .slideUp();


                    $("#nueva_empresa")
                        .prop(
                            "required",
                            false
                        )
                        .val("");

                }

            });


            /* ==========================================================
               VALIDACIÓN DE FECHA
            ========================================================== */

            $("#fecha").on("change", function() {

                const valor =
                    this.value;


                if (!valor) {

                    return;

                }


                /*
                 * Evitamos problemas de zona horaria
                 * usando directamente año, mes y día.
                 */

                const partes =
                    valor.split("-");


                if (
                    partes.length !== 3
                ) {

                    this.value = "";

                    return;

                }


                const fechaSeleccionada =
                    new Date(
                        Number(partes[0]),
                        Number(partes[1]) - 1,
                        Number(partes[2])
                    );


                const hoy =
                    new Date();


                hoy.setHours(
                    0,
                    0,
                    0,
                    0
                );


                if (
                    fechaSeleccionada > hoy
                ) {

                    Swal.fire({

                        icon: "warning",

                        title: "Fecha inválida",

                        text: "No se permiten fechas futuras.",

                        confirmButtonColor: "#ffc107"

                    });


                    this.value = "";

                }

            });


            /* ==========================================================
               EQUIPO ELECTRÓNICO
            ========================================================== */

            $("#equipoElectronicoCheckbox")
                .on("change", function() {

                    if (this.checked) {

                        $("#inputsEquipoElectronico")
                            .stop(true, true)
                            .slideDown();

                    } else {

                        $("#inputsEquipoElectronico")
                            .stop(true, true)
                            .slideUp();


                        $("#marca")
                            .val("");


                        $("#serial")
                            .val("");

                    }

                });


            /* ==========================================================
               ENVÍO DEL FORMULARIO
            ========================================================== */

            $form.on("submit", function(e) {

                /*
                 * Si ya fue confirmado,
                 * permitimos el envío normal.
                 */

                if (
                    enviandoFormulario
                ) {

                    return;

                }


                e.preventDefault();


                /* ======================================================
                   VALIDACIÓN HTML5
                ====================================================== */

                if (
                    !this.checkValidity()
                ) {

                    this.reportValidity();

                    return;

                }


                const formulario =
                    this;


                const $botonGuardar =
                    $form.find(
                        'button[type="submit"]'
                    );


                /* ======================================================
                   VALIDAR EMPRESA "OTRA"
                ====================================================== */

                if (
                    $("#empresa").val() === "otra"
                ) {

                    const nuevaEmpresa =
                        $("#nueva_empresa")
                        .val()
                        .trim();


                    if (
                        nuevaEmpresa === ""
                    ) {

                        Swal.fire({

                            icon: "warning",

                            title: "Empresa requerida",

                            text: "Debe indicar el nombre de la nueva empresa.",

                            confirmButtonColor: "#ffc107"

                        });


                        $("#nueva_empresa")
                            .focus();


                        return;

                    }

                }


                /* ======================================================
                   VALIDAR EQUIPO ELECTRÓNICO
                ====================================================== */

                if (
                    $("#equipoElectronicoCheckbox")
                    .is(":checked")
                ) {

                    const marca =
                        $("#marca")
                        .val()
                        .trim();


                    const serial =
                        $("#serial")
                        .val()
                        .trim();


                    if (
                        marca === "" ||
                        serial === ""
                    ) {

                        Swal.fire({

                            icon: "warning",

                            title: "Datos del equipo",

                            text: "Debe ingresar la marca y el serial del equipo electrónico.",

                            confirmButtonColor: "#ffc107"

                        });


                        return;

                    }

                }


                /* ======================================================
                   CONFIRMACIÓN
                ====================================================== */

                Swal.fire({

                    title: "¿Guardar registro?",

                    text: "Se registrará el contratista en la base de datos.",

                    icon: "question",

                    showCancelButton: true,

                    confirmButtonColor: "#dc3545",

                    cancelButtonColor: "#6c757d",

                    confirmButtonText: "Sí, guardar",

                    cancelButtonText: "Cancelar",

                    reverseButtons: true

                }).then(function(result) {

                    if (
                        !result.isConfirmed
                    ) {

                        return;

                    }


                    enviandoFormulario =
                        true;


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


                    /*
                     * Submit nativo.
                     *
                     * Evita volver a disparar
                     * nuestro evento submit.
                     */

                    HTMLFormElement.prototype.submit.call(
                        formulario
                    );

                });

            });


            /* ==========================================================
               MENSAJE DE ÉXITO DESDE PHP
            ========================================================== */

            <?php if (isset($_SESSION['success'])): ?>

                Swal.fire({

                    icon: "success",

                    title: "¡Éxito!",

                    text: <?= json_encode(
                                $_SESSION['success'],
                                JSON_UNESCAPED_UNICODE
                            ) ?>,

                    confirmButtonColor: "#198754",

                    confirmButtonText: "Aceptar"

                });

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>


            /* ==========================================================
               MENSAJE DE ERROR DESDE PHP
            ========================================================== */

            <?php if (isset($_SESSION['error'])): ?>

                Swal.fire({

                    icon: "error",

                    title: "¡Error!",

                    text: <?= json_encode(
                                $_SESSION['error'],
                                JSON_UNESCAPED_UNICODE
                            ) ?>,

                    confirmButtonColor: "#dc3545",

                    confirmButtonText: "Aceptar"

                });

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>


        });
    </script>
</body>
</html>