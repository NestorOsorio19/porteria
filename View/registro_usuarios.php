<?php

session_start();

/* ==========================================================
   EVITAR CACHE
========================================================== */

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");


/* ==========================================================
   CONFIGURACIÓN Y CONEXIÓN
========================================================== */

require_once "../Config/config.php";
require_once "../Config/database.php";


/* ==========================================================
   COMPROBAR SESIÓN
========================================================== */

if (
    !isset($_SESSION["autenticado"]) ||
    $_SESSION["autenticado"] !== true
) {

    header("Location: ../login.php");
    exit;
}



/* ==========================================================
   SOLO ADMINISTRADORES
========================================================== */

if (
    !isset($_SESSION["role"]) ||
    strtolower($_SESSION["role"]) !== "super"
) {

    header("Location: ../index.php");
    exit;
}

/* ==========================================================
   CONEXIÓN PDO
========================================================== */

$connection = connection();


/* ==========================================================
   CARGAR ROLES
========================================================== */

function cargarRoles(PDO $connection): string
{

    $stmt = $connection->prepare("
        SELECT
            id_rol,
            nombre_rol,
            descripcion

        FROM roles

        ORDER BY nombre_rol ASC
    ");

    $stmt->execute();

    $options = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = htmlspecialchars(
            $row["id_rol"],
            ENT_QUOTES,
            "UTF-8"
        );

        $nombre = htmlspecialchars(
            $row["nombre_rol"],
            ENT_QUOTES,
            "UTF-8"
        );

        $descripcion = htmlspecialchars(
            $row["descripcion"] ?? "",
            ENT_QUOTES,
            "UTF-8"
        );

        $options .= "
            <option
                value=\"$id\"
                data-descripcion=\"$descripcion\">

                $nombre

            </option>
        ";
    }

    return $options;
}


$roles = cargarRoles($connection);


/* ==========================================================
   MENSAJES DEL CONTROLLER
========================================================== */

$guardado =
    isset($_GET["guardado"]) &&
    $_GET["guardado"] === "1";

$error = $_GET["error"] ?? "";

$error = htmlspecialchars(
    $error,
    ENT_QUOTES,
    "UTF-8"
);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Registrar Usuario</title>


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- =====================================================
         JQUERY
    ====================================================== -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js">
    </script>


    <!-- =====================================================
         SWEET ALERT
    ====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>


    <style>

        :root {

            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --border-light: #e9ecef;

        }


        /* ======================================
           GENERAL
        ====================================== */

        body {

            background:
                linear-gradient(
                    135deg,
                    #eef2f7,
                    #d9e7ff
                );

            min-height: 100vh;

            font-family:
                'Segoe UI',
                sans-serif;

        }


        label {

            font-weight: 600;

            margin-bottom: 8px;

        }


        /* ======================================
           NAVBAR
        ====================================== */

        .navbar {

            box-shadow:
                0 4px 12px
                rgba(0, 0, 0, .1);

        }


        .navbar-brand {

            font-weight: 700;

            font-size: 1.1rem;

        }


        .usuario-navbar {

            color: #fff;

            font-size: .9rem;

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

            color:
                var(--primary);

        }


        .breadcrumb a:hover {

            text-decoration: underline;

        }


        /* ======================================
           HEADER
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

            background:
                var(--primary);

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: auto;

            font-size: 40px;

            box-shadow:
                0 10px 25px
                rgba(13, 110, 253, .25);

        }


        /* ======================================
           MINI CARDS
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

            box-shadow:
                0 6px 18px
                rgba(0, 0, 0, .08);

            transition: all .3s ease;

        }


        .mini-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 12px 28px
                rgba(0, 0, 0, .12);

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
           SECCIONES
        ====================================== */

        .form-section {

            background: #fff;

            border-radius: 20px;

            padding: 30px;

            margin-bottom: 25px;

            border: none;

            box-shadow:
                0 10px 25px
                rgba(0, 0, 0, .08);

            transition: all .3s ease;

        }


        .form-section:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 30px
                rgba(0, 0, 0, .10);

        }


        .section-title {

            display: flex;

            align-items: center;

            gap: 10px;

            font-size: 1.1rem;

            font-weight: 700;

            color:
                var(--primary);

            margin-bottom: 25px;

            padding-bottom: 12px;

            border-bottom:
                2px solid
                var(--border-light);

        }


        /* ======================================
           INPUTS
        ====================================== */

        .form-control,
        .form-select {

            min-height: 48px;

            border-radius: 12px;

            border:
                1px solid #dee2e6;

            padding: 12px;

            transition: .2s;

        }


        .form-control:focus,
        .form-select:focus {

            border-color:
                var(--primary);

            box-shadow:
                0 0 0 .15rem
                rgba(13, 110, 253, .15);

        }


        /* ======================================
           PASSWORD
        ====================================== */

        .password-wrapper {

            position: relative;

        }


        .password-wrapper .form-control {

            padding-right: 50px;

        }


        .btn-password {

            position: absolute;

            right: 10px;

            top: 50%;

            transform:
                translateY(-50%);

            border: none;

            background: transparent;

            color: #6c757d;

        }


        /* ======================================
           INDICADOR PASSWORD
        ====================================== */

        .password-strength {

            height: 5px;

            border-radius: 5px;

            background: #e9ecef;

            margin-top: 8px;

            overflow: hidden;

        }


        .password-strength-bar {

            height: 100%;

            width: 0%;

            transition: .3s;

        }


        .password-help {

            font-size: .8rem;

            color: #6c757d;

            margin-top: 5px;

        }


        /* ======================================
           ROL
        ====================================== */

        .rol-info {

            display: none;

            margin-top: 10px;

            padding: 10px 14px;

            border-radius: 10px;

            background: #eef5ff;

            color: #0d6efd;

            font-size: .9rem;

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
           BARRA ACCIONES
        ====================================== */

        .barra-acciones {

            position: sticky;

            bottom: 15px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .97
                );

            backdrop-filter:
                blur(10px);

            border-radius: 20px;

            padding: 18px;

            margin-top: 30px;

            box-shadow:
                0 10px 25px
                rgba(0, 0, 0, .10);

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


<!-- ==========================================================
     NAVBAR
========================================================== -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">

    <div class="container">

        <span class="navbar-brand">

            <i class="fas fa-users-cog me-2"></i>

            Administración de Usuarios

        </span>


        <span class="usuario-navbar">

            <i class="fas fa-user me-1"></i>

            <?= htmlspecialchars(
                $_SESSION["nombre"] ?? $_SESSION["usuario"],
                ENT_QUOTES,
                "UTF-8"
            ) ?>

        </span>

    </div>

</nav>


<!-- ==========================================================
     BREADCRUMB
========================================================== -->

<div class="container mt-3">

    <nav aria-label="breadcrumb">

        <ol class="breadcrumb bg-white p-3 shadow-sm">

            <li class="breadcrumb-item">

                <a href="../index.php">

                    <i class="fas fa-home"></i>

                    Menú Principal

                </a>

            </li>


            <li class="breadcrumb-item active">

                Registrar Usuario

            </li>

        </ol>

    </nav>

</div>


<!-- ==========================================================
     CONTENIDO
========================================================== -->

<main
    class="d-flex justify-content-center min-vh-100 py-4">

    <div
        class="container"
        style="max-width: 900px;">


        <!-- ==================================================
             HEADER
        =================================================== -->

        <div class="page-header">

            <div class="icon-circle mb-4">

                <i class="fas fa-user-plus"></i>

            </div>


            <h1>

                Registrar Nuevo Usuario

            </h1>


            <p>

                Cree una nueva cuenta y asigne los permisos
                correspondientes.

            </p>

        </div>


        <!-- ==================================================
             MINI CARDS
        =================================================== -->

        <div
            class="row g-4 mb-4 justify-content-center">


            <div class="col-md-4">

                <div class="mini-card">

                    <h6>

                        Módulo

                    </h6>

                    <strong>

                        USUARIOS

                    </strong>

                </div>

            </div>


            <div class="col-md-4">

                <div class="mini-card">

                    <h6>

                        Usuario conectado

                    </h6>

                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION["usuario"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </strong>

                </div>

            </div>


        </div>


        <!-- ==================================================
             FORMULARIO
        =================================================== -->

        <form
            id="formulario_usuario"
            action="../Controller/registrar_usuario.php"
            method="POST">


            <!-- =================================================
                 DATOS PERSONALES
            ================================================== -->

            <div class="form-section">

                <div class="section-title">

                    <i class="fa-solid fa-circle-info"></i>

                    Datos del Usuario

                </div>


                <div class="row g-3">


                    <!-- NOMBRE -->

                    <div class="col-12 col-md-6">

                        <label
                            for="nombre"
                            class="form-label">

                            Nombre completo

                        </label>


                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control"
                            placeholder="Ingrese el nombre completo"
                            maxlength="50"
                            autocomplete="name"
                            required>

                    </div>


                    <!-- CÉDULA -->

                    <div class="col-12 col-md-6">

                        <label
                            for="cedula"
                            class="form-label">

                            Cédula

                        </label>


                        <input
                            type="text"
                            name="cedula"
                            id="cedula"
                            class="form-control"
                            placeholder="Número de cédula"
                            maxlength="15"
                            inputmode="numeric"
                            autocomplete="off"
                            required>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 DATOS DE ACCESO
            ================================================== -->

            <div class="form-section">

                <div class="section-title">

                    <i class="fa-solid fa-lock"></i>

                    Datos de Acceso

                </div>


                <div class="row g-3">


                    <!-- USUARIO -->

                    <div class="col-12">

                        <label
                            for="username"
                            class="form-label">

                            Nombre de usuario

                        </label>


                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="form-control"
                            placeholder="Ejemplo: j.perez"
                            maxlength="50"
                            autocomplete="username"
                            required>


                        <small
                            class="text-muted">

                            Este será el usuario utilizado
                            para iniciar sesión.

                        </small>

                    </div>


                    <!-- CONTRASEÑA -->

                    <div class="col-12 col-md-6">

                        <label
                            for="password"
                            class="form-label">

                            Contraseña

                        </label>


                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Ingrese una contraseña"
                                minlength="6"
                                maxlength="250"
                                autocomplete="new-password"
                                required>


                            <button
                                type="button"
                                class="btn-password"
                                id="mostrarPassword">

                                <i
                                    class="fas fa-eye">
                                </i>

                            </button>

                        </div>


                        <div class="password-strength">

                            <div
                                id="passwordStrengthBar"
                                class="password-strength-bar">
                            </div>

                        </div>


                        <div
                            id="passwordStrengthText"
                            class="password-help">

                            Mínimo 6 caracteres.

                        </div>

                    </div>


                    <!-- CONFIRMAR -->

                    <div class="col-12 col-md-6">

                        <label
                            for="password_confirm"
                            class="form-label">

                            Confirmar contraseña

                        </label>


                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="password_confirm"
                                id="password_confirm"
                                class="form-control"
                                placeholder="Repita la contraseña"
                                minlength="6"
                                maxlength="250"
                                autocomplete="new-password"
                                required>


                            <button
                                type="button"
                                class="btn-password"
                                id="mostrarPasswordConfirm">

                                <i
                                    class="fas fa-eye">
                                </i>

                            </button>

                        </div>


                        <small
                            id="mensajePassword"
                            class="text-muted">

                        </small>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 ROL
            ================================================== -->

            <div class="form-section">

                <div class="section-title">

                    <i class="fa-solid fa-user-shield"></i>

                    Rol y Permisos

                </div>


                <div class="row g-3">


                    <div class="col-12 col-md-6">

                        <label
                            for="id_rol"
                            class="form-label">

                            Rol del usuario

                        </label>


                        <select
                            name="id_rol"
                            id="id_rol"
                            class="form-select"
                            required>

                            <option
                                value=""
                                disabled
                                selected>

                                Seleccione un rol...

                            </option>


                            <?= $roles ?>

                        </select>


                        <div
                            id="rolInfo"
                            class="rol-info">

                        </div>

                    </div>


                    <div class="col-12 col-md-6">

                        <div
                            class="alert alert-info h-100 mb-0 d-flex align-items-center">

                            <div>

                                <i
                                    class="fas fa-info-circle me-2">
                                </i>

                                <strong>
                                    Importante
                                </strong>

                                <br>

                                El rol determina las funciones
                                que podrá utilizar el usuario
                                dentro de la plataforma.

                            </div>

                        </div>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 BOTONES
            ================================================== -->

            <div class="barra-acciones">

                <div class="text-center">


                    <button
                        type="submit"
                        id="btnGuardar"
                        class="btn btn-primary btn-lg px-5">

                        <i
                            class="fas fa-save me-2">
                        </i>

                        Registrar Usuario

                    </button>


                    <a
                        href="../index.php"
                        class="btn btn-secondary btn-lg px-5">

                        <i
                            class="fas fa-arrow-left me-2">
                        </i>

                        Cancelar

                    </a>


                </div>

            </div>


        </form>

    </div>

</main>


<!-- ==========================================================
     JAVASCRIPT
========================================================== -->

<script>

$(function () {


    /* ======================================================
       VARIABLES
    ====================================================== */

    const $form =
        $("#formulario_usuario");

    const $password =
        $("#password");

    const $passwordConfirm =
        $("#password_confirm");

    let enviandoFormulario = false;


    /* ======================================================
       SOLO NÚMEROS EN CÉDULA
    ====================================================== */

    $("#cedula").on(
        "input",
        function () {

            this.value =
                this.value.replace(/\D/g, "");

        }
    );


    /* ======================================================
       MOSTRAR / OCULTAR PASSWORD
    ====================================================== */

    $("#mostrarPassword").on(
        "click",
        function () {

            const tipo =
                $password.attr("type") === "password"
                    ? "text"
                    : "password";

            $password.attr(
                "type",
                tipo
            );


            $(this)
                .find("i")
                .toggleClass(
                    "fa-eye",
                    tipo === "password"
                )
                .toggleClass(
                    "fa-eye-slash",
                    tipo === "text"
                );

        }
    );


    $("#mostrarPasswordConfirm").on(
        "click",
        function () {

            const tipo =
                $passwordConfirm.attr("type") === "password"
                    ? "text"
                    : "password";

            $passwordConfirm.attr(
                "type",
                tipo
            );


            $(this)
                .find("i")
                .toggleClass(
                    "fa-eye",
                    tipo === "password"
                )
                .toggleClass(
                    "fa-eye-slash",
                    tipo === "text"
                );

        }
    );


    /* ======================================================
       FUERZA DE CONTRASEÑA
    ====================================================== */

    $password.on(
        "input",
        function () {

            const valor =
                $(this).val();

            let fuerza = 0;


            if (valor.length >= 6) {

                fuerza += 25;

            }


            if (/[A-Z]/.test(valor)) {

                fuerza += 25;

            }


            if (/[a-z]/.test(valor)) {

                fuerza += 25;

            }


            if (/[0-9]/.test(valor)) {

                fuerza += 25;

            }


            const $bar =
                $("#passwordStrengthBar");

            const $text =
                $("#passwordStrengthText");


            $bar.css(
                "width",
                fuerza + "%"
            );


            if (fuerza <= 25) {

                $bar.css(
                    "background",
                    "#dc3545"
                );

                $text.text(
                    "Contraseña débil."
                );

            }
            else if (fuerza <= 50) {

                $bar.css(
                    "background",
                    "#ffc107"
                );

                $text.text(
                    "Contraseña regular."
                );

            }
            else if (fuerza <= 75) {

                $bar.css(
                    "background",
                    "#0dcaf0"
                );

                $text.text(
                    "Contraseña buena."
                );

            }
            else {

                $bar.css(
                    "background",
                    "#198754"
                );

                $text.text(
                    "Contraseña fuerte."
                );

            }

        }
    );


    /* ======================================================
       CONFIRMAR PASSWORD
    ====================================================== */

    function validarPasswords() {

        const password =
            $password.val();

        const confirm =
            $passwordConfirm.val();


        const $mensaje =
            $("#mensajePassword");


        if (!confirm) {

            $mensaje
                .text("");

            return false;

        }


        if (password !== confirm) {

            $mensaje
                .text(
                    "Las contraseñas no coinciden."
                )
                .css(
                    "color",
                    "#dc3545"
                );

            return false;

        }


        $mensaje
            .text(
                "✓ Las contraseñas coinciden."
            )
            .css(
                "color",
                "#198754"
            );

        return true;

    }


    $passwordConfirm.on(
        "input",
        validarPasswords
    );


    $password.on(
        "input",
        function () {

            if ($passwordConfirm.val()) {

                validarPasswords();

            }

        }
    );


    /* ======================================================
       INFORMACIÓN DEL ROL
    ====================================================== */

    $("#id_rol").on(
        "change",
        function () {

            const descripcion =
                $(this)
                    .find(":selected")
                    .data("descripcion");


            if (descripcion) {

                $("#rolInfo")
                    .html(
                        "<i class='fas fa-shield-halved me-2'></i>" +
                        descripcion
                    )
                    .slideDown(200);

            } else {

                $("#rolInfo")
                    .slideUp(200);

            }

        }
    );


    /* ======================================================
       SUBMIT
    ====================================================== */

    $form.on(
        "submit",
        function (e) {


            if (enviandoFormulario) {

                return;

            }


            e.preventDefault();


            /* ==============================================
               VALIDACIÓN HTML
            ============================================== */

            if (!this.checkValidity()) {

                this.reportValidity();

                return;

            }


            /* ==============================================
               PASSWORD
            ============================================== */

            if (!validarPasswords()) {

                Swal.fire({

                    icon: "warning",

                    title: "Contraseñas diferentes",

                    text:
                        "Las contraseñas deben coincidir.",

                    confirmButtonColor:
                        "#0d6efd"

                });

                return;

            }


            const formulario =
                this;


            const $boton =
                $("#btnGuardar");


            /* ==============================================
               CONFIRMACIÓN
            ============================================== */

            Swal.fire({

                title:
                    "¿Registrar usuario?",

                text:
                    "Se creará una nueva cuenta en el sistema.",

                icon:
                    "question",

                showCancelButton:
                    true,

                confirmButtonColor:
                    "#0d6efd",

                cancelButtonColor:
                    "#6c757d",

                confirmButtonText:
                    "Sí, registrar",

                cancelButtonText:
                    "Cancelar",

                reverseButtons:
                    true

            }).then(
                function (result) {

                    if (!result.isConfirmed) {

                        return;

                    }


                    enviandoFormulario =
                        true;


                    /* ======================================
                       DESACTIVAR BOTÓN
                    ====================================== */

                    $boton

                        .prop(
                            "disabled",
                            true
                        )

                        .html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>' +
                            "Registrando..."
                        );


                    /* ======================================
                       SUBMIT NATIVO
                    ====================================== */

                    HTMLFormElement
                        .prototype
                        .submit
                        .call(
                            formulario
                        );

                }
            );

        }
    );


    /* ======================================================
       MENSAJE DE ÉXITO
    ====================================================== */

    <?php if ($guardado): ?>

    Swal.fire({

        icon:
            "success",

        title:
            "¡Usuario registrado!",

        text:
            "El usuario fue creado correctamente.",

        confirmButtonColor:
            "#198754",

        confirmButtonText:
            "Aceptar"

    }).then(
        function () {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.delete(
                "guardado"
            );


            window.history.replaceState(
                {},
                document.title,
                url.pathname +
                url.search
            );

        }
    );

    <?php endif; ?>


    /* ======================================================
       MENSAJE DE ERROR
    ====================================================== */

    <?php if ($error !== ""): ?>

    Swal.fire({

        icon:
            "error",

        title:
            "No fue posible registrar",

        text:
            <?= json_encode(
                $error,
                JSON_UNESCAPED_UNICODE
            ) ?>,

        confirmButtonColor:
            "#dc3545",

        confirmButtonText:
            "Aceptar"

    }).then(
        function () {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.delete(
                "error"
            );


            window.history.replaceState(
                {},
                document.title,
                url.pathname +
                url.search
            );

        }
    );

    <?php endif; ?>


});

</script>


</body>

</html>
