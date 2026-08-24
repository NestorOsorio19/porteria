<?php
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
CARGAR AREA PARA EL SELECT
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

        $id = htmlspecialchars($row['id_area'], ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars($row['nom_area'], ENT_QUOTES, 'UTF-8');

        $options .= "<option value=\"$id\">$nombre</option>";
    }

    return $options;
}

/* ==========================================================
CARGAR EMPRESA PARA EL SELECT
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

        $id = htmlspecialchars($row['id_registro'], ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars($row['nom_empresa'], ENT_QUOTES, 'UTF-8');

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
$Area = cargarArea($connection);
$empresa = cargarEmpresa($connection);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registro Visirantes</title>

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
                <i class="fas fa-person me-2"></i>
                Registro Visitantes
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
                    Visitantes
                </li>
            </ol>
        </nav>

    </div>

    <main class="d-flex justify-content-center min-vh-100 py-4">
        <div class="container" style="max-width:900px">

            <div class="page-header">

                <div class="icon-circle mb-4">
                    <i class="fas fa-person"></i>
                </div>

                <h1>Formulario Registro Visitantes</h1>

                <p>
                    Registro y Seguimiento de los Visitantes que Ingresan a Planta
                </p>

            </div>
            <!-- Formulario para agregar un visitante -->
            <form action="Controller/ingreso_visitantes.php" method="POST">

                <!-- MINI CARDS -->
                <div class="row g-4 mb-4 justify-content-center">

                    <div class="col-md-4">
                        <div class="mini-card">
                            <h6>Formulario</h6>
                            <strong>VISITANTES</strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mini-card">
                            <h6>Fecha Sistema</h6>
                            <strong><?= date('d/m/Y') ?></strong>
                        </div>
                    </div>

                </div>

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
                            <label for="rh" class="form-label">Tipo de Sangre:</label>
                            <select name="rh" id="rh" class="form-select" required>
                                <option value="" disabled selected>Tipo de Sangre...</option>
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

                        <div class="col-12 col-md-3">
                            <label for="contacto" class="form-label">Contacto de Emergencia:</label>
                            <input type="text" name="contacto" id="contacto" class="form-control" min="1" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="numero_emergencia" class="form-label">Numero de Emergencia:</label>
                            <input type="text" name="numero_emergencia" id="numero_emergencia" class="form-control" min="1" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="empresa" class="form-label">Empresa:</label>

                            <select name="empresa" id="empresa" class="form-select" required>
                                <option value="" disabled selected>
                                    Seleccione la Empresa...
                                </option>
                                <?= $empresa ?>
                                <option value="otra">OTRA...</option>
                            </select>
                        </div>

                        <!-- Input oculto -->
                        <div id="nuevaEmpresaContainer" style="display:none;">
                            <label for="nueva_empresa">Nombre de la nueva empresa:</label>
                            <input type="text" name="nueva_empresa" id="nueva_empresa" placeholder="Ingrese el nombre de la empresa">
                        </div>

                        <!-- Campo para ingresar el motivo del ingreso -->
                        <label for="motivo">Motivo de Ingreso:</label>
                        <textarea class="form-control" name="motivo" id="motivo" placeholder="Motivo de ingreso..." required></textarea>

                        <!-- EQUIPO ELECTRÓNICO -->
                        <div class="col-12">

                            <div class="form-check form-switch mt-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="equipo"
                                    id="equipoElectronicoCheckbox"
                                    value="SI">

                                <label class="form-check-label fw-semibold" for="equipoElectronicoCheckbox">
                                    <i class="fas fa-laptop me-2 text-danger"></i>
                                    Ingreso de equipo electrónico
                                </label>
                            </div>

                        </div>

                        <div id="inputsEquipoElectronico"
                            class="row g-3 mt-1"
                            style="display:none;">

                            <div class="col-12">
                                <div class="mini-card text-start w-100" style="max-width:700px;">

                                    <h6 class="mb-3">
                                        <i class="fas fa-laptop me-2 text-danger"></i>
                                        Información del Equipo
                                    </h6>

                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="marca" class="form-label">
                                                Marca
                                            </label>

                                            <input
                                                type="text"
                                                name="marca"
                                                id="marca"
                                                class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="serial" class="form-label">
                                                Serial
                                            </label>

                                            <input
                                                type="text"
                                                name="serial"
                                                id="serial"
                                                class="form-control">
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="col-12 col-md-3">
                            <label for="carnet" class="form-label">Número del Carnet:</label>
                            <input type="text" name="carnet" id="carnet" class="form-control" placeholder="N° Carnet" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="ingreso" class="form-label">Hora de ingreso:</label>
                            <input type="time" name="ingreso" id="ingreso" class="form-control" required>
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

                        <a href="tablacolaboradores.php"
                            class="btn btn-danger btn-lg px-5">

                            <i class="fas fa-eye me-2"></i>
                            Ver Registros

                        </a>

                    </div>

                </div>

            </form>
        </div>
    </main>
    <script>
    $(function() {

        /* ==========================================================
           CONFIGURACIÓN
        ========================================================== */

        const $cedula = $("#cedula");

        let ultimaCedulaConsultada = "";


        /* ==========================================================
           SOLO NÚMEROS EN CÉDULA
        ========================================================== */

        $cedula.on("input", function() {

            this.value = this.value.replace(/\D/g, "");

            /*
             * Si el usuario modifica la cédula,
             * permitimos una nueva consulta.
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
           CONSULTAR VISITANTE POR CÉDULA
        ========================================================== */

        $cedula.off("blur").on("blur", function() {

            const cedula = $(this).val().trim();


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

            if (ultimaCedulaConsultada === cedula) {
                return;
            }

            ultimaCedulaConsultada = cedula;


            /* ======================================================
               MENSAJE CONSULTANDO
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

                url: "Controller/buscar_visitantes.php",

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
                        "RESPUESTA BUSCAR VISITANTE:",
                        response
                    );


                    /* ==============================================
                       VALIDAR RESPUESTA
                    ============================================== */

                    if (!response || typeof response !== "object") {

                        $("#mensajeCedula")
                            .removeClass(
                                "text-success text-primary text-warning"
                            )
                            .addClass(
                                "text-danger fw-semibold"
                            )
                            .text(
                                "Respuesta inválida del servidor."
                            );

                        return;
                    }


                    /* ==============================================
                       VISITANTE NO ENCONTRADO
                    ============================================== */

                    if (response.error === true) {

                        /*
                         * Limpiar los campos que podrían
                         * contener información anterior.
                         */

                        $("#nombre").val("");

                        $("#arl").val("");

                        $("#eps").val("");

                        $("#rh").val("");

                        $("#contacto").val("");

                        $("#numero_emergencia").val("");

                        $("#empresa").val("");

                        $("#motivo").val("");

                        $("#carnet").val("");

                        /*
                         * Ocultar empresa nueva.
                         */

                        $("#nuevaEmpresaContainer").hide();

                        $("#nueva_empresa")
                            .val("")
                            .prop("required", false);


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

                        return;
                    }


                    /* ==============================================
                       VISITANTE ENCONTRADO
                    ============================================== */

                    $("#nombre").val(
                        response.nombre || ""
                    );


                    $("#arl").val(
                        response.arl || ""
                    );


                    $("#eps").val(
                        response.eps || ""
                    );


                    $("#rh").val(
                        response.rh || ""
                    );


                    /*
                     * IMPORTANTE:
                     *
                     * Tu formulario NO tiene un input llamado
                     * "telefono".
                     *
                     * Tiene:
                     *
                     * contacto
                     * numero_emergencia
                     */

                    $("#contacto").val(
                        response.contacto || ""
                    );


                    $("#numero_emergencia").val(
                        response.numero_emergencia || ""
                    );


                    $("#empresa").val(
                        response.empresa || ""
                    );


                    $("#motivo").val(
                        response.motivo || ""
                    );


                    $("#carnet").val(
                        response.carnet || ""
                    );


                    /* ==============================================
                       EMPRESA
                    ============================================== */

                    /*
                     * Si la empresa encontrada existe
                     * dentro del SELECT, la seleccionamos.
                     */

                    if (
                        response.empresa &&
                        $("#empresa option[value='" + response.empresa + "']").length > 0
                    ) {

                        $("#empresa").val(
                            response.empresa
                        );

                        $("#nuevaEmpresaContainer").hide();

                        $("#nueva_empresa")
                            .val("")
                            .prop("required", false);

                    } else if (
                        response.empresa &&
                        response.empresa !== ""
                    ) {

                        /*
                         * Si la empresa no existe en el SELECT,
                         * dejamos el SELECT vacío.
                         */

                        $("#empresa").val("");

                    }


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
                        .text(
                            "Visitante encontrado. Datos cargados."
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


                    $("#mensajeCedula")
                        .removeClass(
                            "text-success text-primary text-warning"
                        )
                        .addClass(
                            "text-danger fw-semibold"
                        )
                        .text(
                            "Error al consultar el visitante."
                        );

                }

            });

        });


        /* ==========================================================
           MOSTRAR INPUT "OTRA EMPRESA"
        ========================================================== */

        $("#empresa").on("change", function() {

            const valor = $(this).val();


            if (valor === "otra") {

                $("#nuevaEmpresaContainer").show();

                $("#nueva_empresa")
                    .prop("required", true);

            } else {

                $("#nuevaEmpresaContainer").hide();

                $("#nueva_empresa")
                    .prop("required", false)
                    .val("");

            }

        });


        /* ==========================================================
           VALIDACIÓN DE FECHA
        ========================================================== */

        $("#fecha").on("change", function() {

            const valor = this.value;


            if (!valor) {
                return;
            }


            /*
             * Evitamos problemas de zona horaria
             * utilizando directamente las partes de la fecha.
             */

            const partes = valor.split("-");


            if (partes.length !== 3) {

                this.value = "";

                return;
            }


            const fechaSeleccionada = new Date(
                Number(partes[0]),
                Number(partes[1]) - 1,
                Number(partes[2])
            );


            const hoy = new Date();

            hoy.setHours(0, 0, 0, 0);


            if (fechaSeleccionada > hoy) {

                Swal.fire({

                    icon: "warning",

                    title: "Fecha inválida",

                    text: "No se permiten fechas futuras.",

                    confirmButtonColor: "#dc3545",

                    confirmButtonText: "Aceptar"

                });


                this.value = "";

            }

        });


        /* ==========================================================
           MOSTRAR / OCULTAR EQUIPO ELECTRÓNICO
        ========================================================== */

        $("#equipoElectronicoCheckbox").on(
            "change",
            function() {

                if (this.checked) {

                    $("#inputsEquipoElectronico").slideDown(200);

                } else {

                    $("#inputsEquipoElectronico").slideUp(200);

                    /*
                     * Limpiar datos del equipo cuando
                     * se desmarca.
                     */

                    $("#marca").val("");

                    $("#serial").val("");

                }

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