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

    <title>Registro Vehiculos Internos</title>

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
                <i class="fas fa-truck-front me-2"></i>
                Registro Vehiculos Internos
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
                    Vehiculos Internos
                </li>
            </ol>
        </nav>

    </div>

    <main class="d-flex justify-content-center min-vh-100 py-4">
        <div class="container" style="max-width:900px">

            <div class="page-header">

                <div class="icon-circle mb-4">
                    <i class="fas fa-truck-front"></i>
                </div>

                <h1>Formulario Registro</h1>

                <p>
                    Registro de Visitantes, Contratistas y Proveedores con Vehiculo
                </p>

            </div>

            <form id="formulario_vehiculos" action="../Controller/ingreso_vehiculos_internos.php" method="POST">

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
                            <label for="tipo_vehiculo" class="form-label">Tipo de Vehiculo:</label>
                            <select name="tipo_vehiculo" id="tipo_vehiculo" class="form-select" required>
                                <option value="" disabled selected>Seleccione su respuesta</option>
                                <option value="1">Pollo en Pie</option>
                                <option value="0">Pollo en Canal</option>
                            </select>
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

                        <div class="col-12 col-md-3">
                            <label for="induccion_sgsst">Recibió inducción de SG-SST:</label>
                            <select name="induccion_sgsst" id="induccion_sgsst" class="form-select" required>
                                <option value="" disabled selected>Seleccione su respuesta</option>
                                <option value="1">SI</option>
                                <option value="0">NO</option>
                            </select>
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

                        <a href="tabla_vehiculos.php"
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

            /* ==========================================================
               LIMPIAR DATOS DEL VEHICULO
            ========================================================== */

            function limpiarDatosVehiculo() {

                $("#nombre").val("");
                $("#arl").val("");
                $("#eps").val("");
                $("#placa_vehiculo").val("");

            }

            /* ==========================================================
            SOLO NÚMEROS
            ========================================================== */

            $("#cedula").on(
                "input",
                function() {
                    this.value = this.value.replace(/\D/g, "");
                }
            );

            // ==========================================================
            // CONSULTAR VEHICULO POR CÉDULA
            // ==========================================================

            let ultimaCedulaConsultada = "";

            $("#cedula").off("blur").on("blur", function() {

                const cedula = $(this).val().trim();


                // ======================================================
                // CÉDULA VACÍA
                // ======================================================

                if (!cedula) {

                    $("#mensajeCedula")
                        .removeClass("text-success text-danger text-primary text-warning")
                        .text("");

                    return;
                }


                // ======================================================
                // VALIDACIÓN MÍNIMA
                // ======================================================

                if (cedula.length < 5) {

                    $("#mensajeCedula")
                        .removeClass("text-success text-danger text-primary")
                        .addClass("text-warning fw-semibold")
                        .text("Ingrese una cédula válida.");

                    return;
                }


                // ======================================================
                // EVITAR CONSULTAR LA MISMA CÉDULA
                // ======================================================

                if (ultimaCedulaConsultada === cedula) {
                    return;
                }

                ultimaCedulaConsultada = cedula;


                // ======================================================
                // MENSAJE DE CONSULTA
                // ======================================================

                $("#mensajeCedula")
                    .removeClass("text-success text-danger text-warning")
                    .addClass("text-primary fw-semibold")
                    .text("Consultando...");


                // ======================================================
                // AJAX
                // ======================================================

                $.ajax({

                    url: "../Controller/buscar_vehiculo.php",

                    type: "POST",

                    data: {
                        cedula: cedula
                    },

                    dataType: "json",


                    // ==================================================
                    // RESPUESTA CORRECTA
                    // ==================================================

                    success: function(response) {

                        console.log(
                            "RESPUESTA BUSCAR VEHICULO:",
                            response
                        );


                        // ==============================================
                        // NO ENCONTRADO
                        // ==============================================

                        if (response.error === true) {

                            $("#nombre").val("");
                            $("#arl").val("");
                            $("#eps").val("");
                            $("#placa_vehiculo").val("");


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


                        // ==============================================
                        // DATOS DE LA PERSONA
                        // ==============================================

                        $("#nombre").val(
                            response.nombre || ""
                        );

                        $("#arl").val(
                            response.arl || ""
                        );

                        $("#eps").val(
                            response.eps || ""
                        );


                        // ==============================================
                        // DATOS DEL VEHÍCULO
                        //
                        // LA BD SE LLAMA "placa"
                        // EL INPUT SE LLAMA "placa_vehiculo"
                        // ==============================================

                        $("#placa_vehiculo").val(
                            response.placa || ""
                        );


                        // ==============================================
                        // MENSAJE
                        // ==============================================

                        $("#mensajeCedula")
                            .removeClass(
                                "text-primary text-danger text-warning"
                            )
                            .addClass(
                                "text-success fw-semibold"
                            )
                            .text(
                                "Vehículo encontrado. Datos cargados."
                            );
                    },


                    // ==================================================
                    // ERROR AJAX
                    // ==================================================

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
                                "Error al consultar el vehículo."
                            );
                    }

                });

            });

            /* ==========================================================
               SI CAMBIA LA CÉDULA
               INVALIDAR CONSULTAS ANTERIORES
            ========================================================== */

            $cedula.on("input", function() {

                // Permitir únicamente números
                this.value = this.value.replace(/\D/g, "");

                // Permitir una nueva consulta
                ultimaCedulaConsultada = "";

                $("#mensajeCedula")
                    .removeClass(
                        "text-success text-danger text-primary text-warning"
                    )
                    .text("");
            });


            /* ==========================================================
               VALIDAR FECHA
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

                        text: "No se permiten fechas futuras."

                    });


                    this.value = "";

                }

            });

            /* ==========================================================
               ENVIAR FORMULARIO
            ========================================================== */

            $form.on("submit", function(e) {

                /*
                 * Si ya fue confirmado,
                 * dejamos que el navegador envíe el formulario.
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


                const formulario = this;

                const $botonGuardar =
                    $form.find('button[type="submit"]');


                /* ======================================================
                   CONFIRMAR
                ====================================================== */

                Swal.fire({

                    title: "¿Guardar registro?",

                    text: "Se registrará el vehiculo en la base de datos.",

                    icon: "question",

                    showCancelButton: true,

                    confirmButtonColor: "#dc3545",

                    cancelButtonColor: "#6c757d",

                    confirmButtonText: "Sí, guardar",

                    cancelButtonText: "Cancelar",

                    reverseButtons: true

                }).then(function(result) {

                    if (!result.isConfirmed) {

                        return;
                    }


                    enviandoFormulario = true;


                    /* ==================================================
                       DESACTIVAR BOTÓN
                    ================================================== */

                    $botonGuardar

                        .prop("disabled", true)

                        .html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>' +
                            'Guardando...'
                        );


                    /*
                     * submit() nativo.
                     *
                     * Esto evita volver a disparar
                     * nuestro evento submit.
                     */
                    HTMLFormElement.prototype.submit.call(
                        formulario
                    );

                });

            });


            /* ==========================================================
               MENSAJE DE ÉXITO
            ========================================================== */

            <?php if ($guardado): ?>

                Swal.fire({

                    icon: "success",

                    title: "¡Registro exitoso!",

                    text: "El vehiculo fue registrado correctamente.",

                    confirmButtonColor: "#198754",

                    confirmButtonText: "Aceptar"

                }).then(function() {

                    /*
                     * Eliminar ?guardado=1 de la URL.
                     *
                     * Así al actualizar la página no vuelve a aparecer
                     * el mensaje.
                     */

                    const url = new URL(
                        window.location.href
                    );

                    url.searchParams.delete("guardado");

                    window.history.replaceState({},
                        document.title,
                        url.pathname +
                        url.search
                    );

                });

            <?php endif; ?>


            /* ==========================================================
               MENSAJE DE ERROR
            ========================================================== */

            <?php if ($error !== ''): ?>

                Swal.fire({

                    icon: "error",

                    title: "No fue posible guardar",

                    text: <?= json_encode($error, JSON_UNESCAPED_UNICODE) ?>,

                    confirmButtonColor: "#dc3545",

                    confirmButtonText: "Aceptar"

                }).then(function() {

                    /*
                     * Eliminar ?error=... de la URL.
                     */

                    const url = new URL(
                        window.location.href
                    );

                    url.searchParams.delete("error");

                    window.history.replaceState({},
                        document.title,
                        url.pathname +
                        url.search
                    );

                });

            <?php endif; ?>

        });
    </script>

</body>

</html>