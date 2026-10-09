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
CARGAR ARL
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
CARGAR EPS
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
CARGAR ÁREA
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
CARGAR EMPRESAS
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

/* ==========================================================
QUIÉN REGISTRA
========================================================== */
$realizo = $_SESSION['nombre']
    ?? $_SESSION['usuario']
    ?? '';

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
    BOOTSTRAP
    ================================================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <!-- ==================================================
    FONT AWESOME
    ================================================== -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- ==================================================
    JQUERY
    ================================================== -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ==================================================
    SWEETALERT2
    ================================================== -->
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

            background:
                linear-gradient(135deg,
                    #eef2f7,
                    #d9e7ff);

            min-height: 100vh;

            font-family:
                'Segoe UI',
                sans-serif;
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
        HEADER
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
        SECCIONES
        ================================================== */

        .form-section {

            background: #fff;

            border-radius: 20px;

            padding: 30px;

            margin-bottom: 25px;

            border: none;

            box-shadow:
                0 10px 25px rgba(0, 0, 0, .08);
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

            border-color:
                var(--danger);

            box-shadow:
                0 0 0 .15rem rgba(220, 53, 69, .15);
        }


        textarea.form-control {

            min-height: 100px;

            resize: vertical;
        }


        /* ==================================================
        NUEVA EMPRESA
        ================================================== */

        #nuevaEmpresaContainer {

            display: none;

            animation:
                aparecerEmpresa .25s ease;
        }


        @keyframes aparecerEmpresa {

            from {

                opacity: 0;

                transform:
                    translateY(-10px);
            }

            to {

                opacity: 1;

                transform:
                    translateY(0);
            }
        }


        #nueva_empresa {

            border:
                2px solid #ffc107;

            background:
                #fffdf5;
        }


        #nueva_empresa:focus {

            border-color:
                #dc3545;

            background:
                #fff;
        }


        /* ==================================================
        INDUCCIÓN SST
        ================================================== */

        .induccion-card {

            display: flex;

            align-items: center;

            gap: 20px;

            padding: 22px;

            background:
                linear-gradient(135deg,
                    #fff8e1,
                    #fff3cd);

            border:
                2px solid #ffc107;

            border-left:
                6px solid #dc3545;

            border-radius: 18px;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .08);

            margin-top: 15px;
        }


        .induccion-icon {

            min-width: 65px;

            height: 65px;

            border-radius: 50%;

            background: #dc3545;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 28px;

            box-shadow:
                0 5px 15px rgba(220, 53, 69, .25);
        }


        .induccion-content {

            flex: 1;
        }


        .induccion-content h5 {

            margin-bottom: 8px;

            font-weight: 700;

            color: #343a40;
        }


        .induccion-content p {

            margin-bottom: 15px;

            color: #6c757d;
        }


        .induccion-content .badge {

            font-size: .7rem;

            margin-left: 8px;

            vertical-align: middle;
        }


        .induccion-content .form-check {

            background: white;

            padding: 12px 15px;

            border-radius: 10px;

            border: 1px solid #dee2e6;
        }


        .induccion-content .form-check-input {

            cursor: pointer;
        }


        .induccion-content .form-check-label {

            cursor: pointer;

            font-weight: 600;

            color: #495057;
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


        /* ==================================================
        BARRA DE ACCIONES
        ================================================== */

        .barra-acciones {

            position: sticky;

            bottom: 15px;

            background:
                rgba(255,
                    255,
                    255,
                    .97);

            backdrop-filter:
                blur(10px);

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


        @media (max-width: 576px) {

            .induccion-card {

                flex-direction: column;

                text-align: center;
            }


            .induccion-content .form-check {

                text-align: left;
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
CONTENIDO
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
            DATOS GENERALES
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


                            <small id="mensajeCedula"></small>

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

                                <option value="O-">O -</option>
                                <option value="O+">O +</option>
                                <option value="A-">A -</option>
                                <option value="A+">A +</option>
                                <option value="B-">B -</option>
                                <option value="B+">B +</option>
                                <option value="AB-">AB -</option>
                                <option value="AB+">AB +</option>

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

                        <!-- ==================================================
                        EMPRESA
                        ================================================== -->

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

                                <option value="" selected disabled>
                                    Seleccione la Empresa...
                                </option>

                                <?= $empresa ?>

                                <option value="otra">
                                    OTRA...
                                </option>

                            </select>

                        </div>

                        <!-- ==================================================
                        NUEVA EMPRESA
                        ================================================== -->

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
                                autocomplete="off"
                                placeholder="Ingrese el nombre de la empresa"
                                disabled
                                readonly>

                            <div class="form-text">

                                <i class="fas fa-info-circle me-1"></i>

                                Este campo solamente se utiliza cuando seleccione
                                <strong>OTRA...</strong>

                            </div>

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


                        <!-- ==================================================
                    INDUCCIÓN SST
                    ================================================== -->

                        <div class="col-12">

                            <div class="induccion-card">

                                <div class="induccion-icon">

                                    <i class="fas fa-hard-hat"></i>

                                </div>


                                <div class="induccion-content">

                                    <h5>

                                        Inducción de Seguridad y Salud
                                        en el Trabajo

                                        <span class="badge bg-danger">
                                            OBLIGATORIA
                                        </span>

                                    </h5>


                                    <p>

                                        Antes de ingresar a las instalaciones,
                                        el contratista debe haber realizado
                                        la inducción SST.

                                    </p>


                                    <div class="form-check form-switch">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="induccion_sgsst"
                                            id="induccion_sgsst"
                                            value="1">


                                        <label
                                            class="form-check-label"
                                            for="induccion_sgsst">

                                            Confirmo que la inducción SST
                                            fue realizada.

                                        </label>

                                    </div>

                                </div>

                            </div>

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

                                    <i
                                        class="fas fa-laptop me-2 text-danger">
                                    </i>

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

                                            <i
                                                class="fas fa-laptop me-2 text-danger">
                                            </i>

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


                        <!-- QUIÉN REGISTRA -->
                        <div class="col-12 col-md-3">
                            <label
                                for="realizo"
                                class="form-label"> Usuario que Registra: </label>
                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="fas fa-user-shield"></i>

                                </span>

                                <input
                                    type="text"
                                    id="realizo"
                                    class="form-control bg-light"
                                    value="<?= htmlspecialchars($realizo, ENT_QUOTES, 'UTF-8') ?>"
                                    readonly>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- ==================================================
            BARRA DE ACCIONES
            ================================================== -->

                <div class="barra-acciones">

                    <div
                        class="
                    text-center
                    d-flex
                    flex-wrap
                    justify-content-center
                    gap-2
                    ">


                        <button
                            type="submit"
                            class="btn btn-danger btn-lg px-5">

                            <i class="fas fa-save me-2"></i>

                            Guardar Registro

                        </button>


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

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
    </script>


    <script>
        $(document).ready(function() {

            /* ==========================================================
            ELEMENTOS PRINCIPALES
            ========================================================== */

            const $form = $("#formContratista");

            const $cedula = $("#cedula");

            const $empresa = $("#empresa");

            const $nuevaEmpresaContainer =
                $("#nuevaEmpresaContainer");

            const $nuevaEmpresa =
                $("#nueva_empresa");

            $nuevaEmpresa
                .prop("disabled", true)
                .prop("readonly", true);

            $nuevaEmpresaContainer.hide();

            const $induccion =
                $("#induccion_sgsst");

            const $equipo =
                $("#equipoElectronicoCheckbox");

            const $inputsEquipo =
                $("#inputsEquipoElectronico");

            let ultimaCedulaConsultada = "";

            let enviandoFormulario = false;


            /* ==========================================================
            MOSTRAR / OCULTAR NUEVA EMPRESA
            ESTA ES LA PARTE IMPORTANTE
            ========================================================== */

            function controlarNuevaEmpresa() {

                let valor = $("#empresa").val();

                console.log("Empresa seleccionada:", valor);

                if (valor === "otra") {

                    $("#nuevaEmpresaContainer")
                        .stop(true, true)
                        .slideDown(250);

                    $("#nueva_empresa")
                        .prop("disabled", false)
                        .prop("readonly", false)
                        .prop("required", true);

                    setTimeout(() => {

                        $("#nueva_empresa").focus();

                    }, 300);

                } else {

                    $("#nuevaEmpresaContainer")
                        .stop(true, true)
                        .slideUp(250);

                    $("#nueva_empresa")
                        .val("")
                        .prop("disabled", true)
                        .prop("readonly", true)
                        .prop("required", false);
                }
            }

            /* ==========================================================
            CAMBIO DE EMPRESA
            ========================================================== */

            $("#empresa").on("change", function() {

                console.log(
                    "Cambio detectado:",
                    $(this).val()
                );

                controlarNuevaEmpresa();

                console.log("Script cargado correctamente");

            });

            /* ==========================================================
            EJECUTAR AL CARGAR
            ========================================================== */

            controlarNuevaEmpresa();


            /* ==========================================================
            CÉDULA - SOLO NÚMEROS
            ========================================================== */

            $cedula.on("input", function() {

                this.value =
                    this.value.replace(/\D/g, "");


                ultimaCedulaConsultada = "";


                $("#mensajeCedula")
                    .removeClass(
                        "text-success " +
                        "text-danger " +
                        "text-primary " +
                        "text-warning"
                    )
                    .text("");

            });


            /* ==========================================================
            CONSULTAR CONTRATISTA
            ========================================================== */

            $cedula.on("blur", function() {

                const cedula =
                    $(this).val().trim();


                if (!cedula) {

                    return;

                }


                if (cedula.length < 5) {

                    $("#mensajeCedula")
                        .removeClass(
                            "text-success " +
                            "text-danger " +
                            "text-primary"
                        )
                        .addClass(
                            "text-warning fw-semibold"
                        )
                        .text(
                            "Ingrese una cédula válida."
                        );

                    return;

                }


                if (
                    ultimaCedulaConsultada === cedula
                ) {

                    return;

                }


                ultimaCedulaConsultada =
                    cedula;


                $("#mensajeCedula")
                    .removeClass(
                        "text-success " +
                        "text-danger " +
                        "text-warning"
                    )
                    .addClass(
                        "text-primary fw-semibold"
                    )
                    .text(
                        "Consultando..."
                    );


                $.ajax({

                    url: "../Controller/buscar_contratistas.php",

                    method: "POST",

                    data: {
                        cedula: cedula
                    },

                    dataType: "json",


                    /* ==================================================
                    ÉXITO
                    ================================================== */

                    success: function(response) {

                        console.log(
                            "RESPUESTA:",
                            response
                        );


                        /* ==========================================
                        NO ENCONTRADO
                        ========================================== */

                        if (
                            response.error === true
                        ) {

                            limpiarDatosContratista();


                            $("#mensajeCedula")
                                .removeClass(
                                    "text-success " +
                                    "text-danger " +
                                    "text-primary"
                                )
                                .addClass(
                                    "text-warning fw-semibold"
                                )
                                .text(
                                    response.mensaje ||
                                    "Cédula no encontrada. Complete la información."
                                );


                            /*
                             * MUY IMPORTANTE:
                             * después de limpiar los datos,
                             * dejamos la empresa en su estado
                             * inicial.
                             */

                            controlarNuevaEmpresa();


                            return;

                        }


                        /* ==========================================
                        DATOS PERSONALES
                        ========================================== */

                        $("#nombre")
                            .val(
                                response.nombre || ""
                            );


                        $("#rh")
                            .val(
                                response.rh || ""
                            );


                        /* ==========================================
                        SEGURIDAD SOCIAL
                        ========================================== */

                        $("#arl")
                            .val(
                                response.arl || ""
                            );


                        $("#eps")
                            .val(
                                response.eps || ""
                            );


                        /* ==========================================
                        EMPRESA
                        ========================================== */

                        let empresaRespuesta =
                            String(
                                response.empresa || ""
                            ).trim();


                        /*
                         * Primero intentamos seleccionar
                         * la empresa existente.
                         */

                        const existeEmpresa =
                            $empresa.find(
                                'option[value="' +
                                empresaRespuesta +
                                '"]'
                            ).length > 0;


                        if (
                            empresaRespuesta === "otra"
                        ) {

                            $empresa.val("otra");

                            controlarNuevaEmpresa();

                            if (
                                response.nueva_empresa
                            ) {

                                $nuevaEmpresa.val(
                                    response.nueva_empresa
                                );

                            }

                        } else if (
                            existeEmpresa
                        ) {

                            $empresa.val(
                                empresaRespuesta
                            );

                            controlarNuevaEmpresa();

                        } else {

                            /*
                             * Si la empresa devuelta por PHP
                             * no existe como option, dejamos
                             * el selector vacío.
                             */

                            $empresa.val("");

                            controlarNuevaEmpresa();

                        }


                        /* ==========================================
                        CONTACTO EMERGENCIA
                        ========================================== */

                        $("#nombre_emergencia")
                            .val(
                                response.nombre_emergencia || ""
                            );


                        $("#telefono_emergencia")
                            .val(
                                response.telefono_emergencia || ""
                            );


                        /* ==========================================
                        INDUCCIÓN SST
                        ========================================== */

                        if (
                            $induccion.is(":checkbox")
                        ) {

                            $induccion.prop(
                                "checked",
                                response.induccion_sgsst == "1"
                            );

                        } else {

                            $induccion.val(
                                response.induccion_sgsst || ""
                            );

                        }


                        /* ==========================================
                        ENFERMEDADES
                        ========================================== */

                        $("#enfermedad_alergia")
                            .val(
                                response.enfermedad_alergia || ""
                            );


                        /* ==========================================
                        HORA INGRESO
                        ========================================== */

                        $("#ingreso")
                            .val(
                                response.ingreso || ""
                            );


                        /* ==========================================
                        MENSAJE
                        ========================================== */

                        $("#mensajeCedula")
                            .removeClass(
                                "text-primary " +
                                "text-danger " +
                                "text-warning"
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
                            "ERROR AJAX:",
                            xhr.status,
                            status,
                            error
                        );


                        console.error(
                            xhr.responseText
                        );


                        ultimaCedulaConsultada =
                            "";


                        $("#mensajeCedula")
                            .removeClass(
                                "text-success " +
                                "text-primary " +
                                "text-warning"
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
               LIMPIAR DATOS
            ========================================================== */

            function limpiarDatosContratista() {

                $("#nombre").val("");

                $("#rh").val("");

                $("#arl").val("");

                $("#eps").val("");

                $("#empresa").val("");

                $("#nueva_empresa")
                    .val("")
                    .prop("disabled", true)
                    .prop("readonly", true)
                    .prop("required", false);

                $("#nuevaEmpresaContainer")
                    .hide();


                $("#nombre_emergencia").val("");

                $("#telefono_emergencia").val("");

                $("#induccion_sgsst")
                    .prop("checked", false);

                $("#enfermedad_alergia").val("");

                $("#ingreso").val("");

                $("#marca").val("");

                $("#serial").val("");

                $("#equipoElectronicoCheckbox")
                    .prop("checked", false);

                $("#inputsEquipoElectronico")
                    .hide();

            }


            /* ==========================================================
               EQUIPO ELECTRÓNICO
            ========================================================== */

            $equipo.on(
                "change",
                function() {

                    if (this.checked) {

                        $inputsEquipo
                            .stop(true, true)
                            .slideDown(250);

                    } else {

                        $inputsEquipo
                            .stop(true, true)
                            .slideUp(250);


                        $("#marca").val("");

                        $("#serial").val("");

                    }

                }
            );


            /* ==========================================================
               FECHA
            ========================================================== */

            $("#fecha").on(
                "change",
                function() {

                    const valor =
                        this.value;


                    if (!valor) {

                        return;

                    }


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

                }
            );


            /* ==========================================================
               ENVÍO DEL FORMULARIO
            ========================================================== */

            $form.on(
                "submit",
                function(e) {

                    if (
                        enviandoFormulario
                    ) {

                        return;

                    }


                    e.preventDefault();


                    /* ==============================================
                    VALIDACIÓN HTML
                    ============================================== */

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


                    /* ==============================================
                    VALIDAR OTRA EMPRESA
                    ============================================== */

                    if (
                        $empresa.val() === "otra"
                    ) {

                        const nombreNuevaEmpresa =
                            $nuevaEmpresa
                            .val()
                            .trim();


                        if (
                            nombreNuevaEmpresa === ""
                        ) {

                            Swal.fire({

                                icon: "warning",

                                title: "Empresa requerida",

                                text: "Debe indicar el nombre de la nueva empresa.",

                                confirmButtonColor: "#ffc107"

                            });


                            controlarNuevaEmpresa();


                            $nuevaEmpresa.focus();


                            return;

                        }

                    }


                    /* ==============================================
                    VALIDAR EQUIPO
                    ============================================== */

                    if (
                        $equipo.is(":checked")
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


                    /* ==============================================
                    VALIDAR INDUCCIÓN
                    ============================================== */

                    let induccionRealizada =
                        false;


                    if (
                        $induccion.is(":checkbox")
                    ) {

                        induccionRealizada =
                            $induccion.is(":checked");

                    } else {

                        induccionRealizada =
                            $induccion.val() === "1";

                    }


                    if (
                        !induccionRealizada
                    ) {

                        Swal.fire({

                            icon: "warning",

                            title: "Inducción SST obligatoria",

                            html: `

                        <div style="
                            text-align:center;
                            padding:5px 10px;
                        ">

                            <div style="
                                width:75px;
                                height:75px;
                                margin:0 auto 18px;
                                border-radius:50%;
                                background:#fff3cd;
                                border:3px solid #ffc107;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                color:#dc3545;
                                font-size:34px;
                                box-shadow:
                                    0 5px 15px
                                    rgba(0,0,0,.10);
                            ">

                                <i class="fas fa-hard-hat"></i>

                            </div>


                            <h5 style="
                                font-weight:700;
                                color:#343a40;
                                margin-bottom:12px;
                            ">

                                La inducción de SST
                                es obligatoria

                            </h5>


                            <p style="
                                font-size:16px;
                                line-height:1.6;
                                color:#495057;
                            ">

                                Para registrar el ingreso del
                                contratista es necesario haber
                                realizado la

                                <strong>
                                    Inducción de Seguridad y Salud
                                    en el Trabajo (SST)
                                </strong>.

                            </p>


                            <div style="
                                background:#f8f9fa;
                                border-left:4px solid #dc3545;
                                border-radius:8px;
                                padding:12px;
                                margin-top:15px;
                                text-align:left;
                            ">

                                <strong style="color:#dc3545;">

                                    <i
                                        class="fas fa-circle-exclamation me-1">
                                    </i>

                                    Importante

                                </strong>


                                <div style="
                                    margin-top:5px;
                                    color:#6c757d;
                                    font-size:14px;
                                ">

                                    No es posible continuar con
                                    el registro hasta completar
                                    y confirmar la inducción.

                                </div>

                            </div>

                        </div>

                    `,

                            confirmButtonText: "Entendido",

                            confirmButtonColor: "#dc3545",

                            allowOutsideClick: false,

                            allowEscapeKey: false,

                            width: "500px"

                        }).then(function() {

                            const elemento =
                                document.getElementById(
                                    "induccion_sgsst"
                                );


                            if (elemento) {

                                elemento.scrollIntoView({

                                    behavior: "smooth",

                                    block: "center"

                                });


                                setTimeout(
                                    function() {

                                        elemento.focus();

                                    },
                                    500
                                );

                            }

                        });


                        return;

                    }


                    /* ==============================================
                    CONFIRMACIÓN FINAL
                    ============================================== */

                    Swal.fire({

                        title: "¿Guardar registro?",

                        html: `

                    <p style="
                        font-size:16px;
                        color:#495057;
                    ">

                        Se registrará el contratista
                        en la base de datos.

                    </p>


                    <div style="
                        margin-top:15px;
                        padding:10px;
                        background:#e8f5e9;
                        border-radius:10px;
                        color:#198754;
                        font-weight:600;
                    ">

                        <i
                            class="fas fa-circle-check me-2">
                        </i>

                        Inducción SST confirmada

                    </div>

                `,

                        icon: "question",

                        showCancelButton: true,

                        confirmButtonColor: "#dc3545",

                        cancelButtonColor: "#6c757d",

                        confirmButtonText: "Sí, guardar",

                        cancelButtonText: "Cancelar",

                        reverseButtons: true

                    }).then(
                        function(result) {

                            if (
                                !result.isConfirmed
                            ) {

                                return;

                            }


                            enviandoFormulario =
                                true;


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
                             * Envío nativo del formulario.
                             */

                            HTMLFormElement.prototype.submit.call(
                                formulario
                            );

                        }
                    );

                }
            );


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

                const errorServidor =
                    <?= json_encode(
                        $_SESSION['error'],
                        JSON_UNESCAPED_UNICODE
                    ) ?>;


                <?php unset($_SESSION['error']); ?>


                if (
                    errorServidor ===
                    "induccion_obligatoria"
                ) {

                    Swal.fire({

                        icon: "warning",

                        title: "Inducción SST obligatoria",

                        html: `

                    <div style="
                        text-align:center;
                        padding:5px 10px;
                    ">

                        <div style="
                            width:85px;
                            height:85px;
                            margin:0 auto 20px;
                            border-radius:50%;
                            background:#fff3cd;
                            border:3px solid #ffc107;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#dc3545;
                            font-size:40px;
                            box-shadow:
                                0 6px 18px
                                rgba(0,0,0,.12);
                        ">

                            <i class="fas fa-hard-hat"></i>

                        </div>


                        <h4 style="
                            font-weight:700;
                            color:#343a40;
                            margin-bottom:15px;
                        ">

                            No se puede registrar
                            el ingreso

                        </h4>


                        <p style="
                            font-size:17px;
                            line-height:1.6;
                            color:#495057;
                        ">

                            La

                            <strong>
                                Inducción de Seguridad y Salud
                                en el Trabajo (SST)
                            </strong>

                            es

                            <span style="
                                color:#dc3545;
                                font-weight:700;
                            ">

                                obligatoria

                            </span>

                            para realizar el registro.

                        </p>


                        <div style="
                            background:#fff8e1;
                            border:1px solid #ffc107;
                            border-radius:10px;
                            padding:14px;
                            margin-top:18px;
                            text-align:left;
                        ">

                            <div style="
                                color:#856404;
                                font-weight:700;
                                margin-bottom:5px;
                            ">

                                <i
                                    class="fas fa-triangle-exclamation me-2">
                                </i>

                                Acción requerida

                            </div>


                            <div style="
                                color:#6c757d;
                                font-size:14px;
                            ">

                                Realice la inducción SST
                                y confirme su realización
                                en el formulario antes
                                de continuar.

                            </div>

                        </div>

                    </div>

                `,

                        confirmButtonText: "Entendido",

                        confirmButtonColor: "#dc3545",

                        allowOutsideClick: false,

                        allowEscapeKey: false,

                        width: "520px"

                    }).then(function() {

                        const elemento =
                            document.getElementById(
                                "induccion_sgsst"
                            );


                        if (elemento) {

                            elemento.scrollIntoView({

                                behavior: "smooth",

                                block: "center"

                            });


                            setTimeout(
                                function() {

                                    elemento.focus();

                                },
                                500
                            );

                        }

                    });

                } else {

                    Swal.fire({

                        icon: "error",

                        title: "¡Error!",

                        text: errorServidor,

                        confirmButtonColor: "#dc3545",

                        confirmButtonText: "Aceptar"

                    });

                }

            <?php endif; ?>

        });
    </script>

</body>

</html>