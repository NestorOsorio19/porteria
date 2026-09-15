<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Si ya inició sesión
if (
    isset($_SESSION['usuario']) &&
    isset($_SESSION['role'])
) {

    $role = strtolower(trim($_SESSION['role']));

    switch ($role) {

    case 'super':
        header("Location: View/menu_administrador.php");
        exit;

    default:
        header("Location: View/menu_principal.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Inicio de Sesión</title>

    <!-- Bootstrap -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>

    <style>
        :root {

            --primary: #0d6efd;
            --danger: #dc3545;
            --dark: #212529;

        }

        /* =====================================================
           GENERAL
        ===================================================== */

        body {

            min-height: 100vh;

            background:
                linear-gradient(135deg,
                    #eef2f7,
                    #d9e7ff);

            font-family:
                'Segoe UI',
                sans-serif;

            display: flex;
            flex-direction: column;

        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar {

            box-shadow:
                0 4px 12px rgba(0, 0, 0, .12);

        }


        .navbar-brand {

            font-weight: 700;
            font-size: 1.1rem;

        }


        /* =====================================================
           CONTENEDOR
        ===================================================== */

        .login-container {

            flex: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            padding:
                30px 15px;

        }


        /* =====================================================
           TARJETA
        ===================================================== */

        .login-card {

            width: 100%;

            max-width: 430px;

            background: #fff;

            border-radius: 25px;

            padding: 40px;

            box-shadow:
                0 15px 40px rgba(0, 0, 0, .12);

            transition:
                all .3s ease;

        }


        .login-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 20px 45px rgba(0, 0, 0, .16);

        }

        /* =====================================================
           ICONO
        ===================================================== */

        .icon-circle {

            width: 100px;

            height: 100px;

            border-radius: 50%;

            background:
                var(--danger);

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 25px;

            font-size: 40px;

            box-shadow:
                0 10px 25px rgba(220, 53, 69, .25);

        }

        /* =====================================================
           TITULO
        ===================================================== */

        .login-title {

            text-align: center;

            font-weight: 700;

            color: #2c3e50;

            margin-bottom: 8px;

        }

        .login-subtitle {

            text-align: center;

            color: #6c757d;

            margin-bottom: 30px;

        }

        /* =====================================================
           LABEL
        ===================================================== */

        .form-label {

            font-weight: 600;

            margin-bottom: 8px;

        }

        /* =====================================================
           INPUTS
        ===================================================== */

        .form-control {

            min-height: 50px;

            border-radius: 12px;

            border:
                1px solid #dee2e6;

            padding:
                12px 15px;

            transition:
                .2s;

        }

        .form-control:focus {

            border-color:
                var(--danger);

            box-shadow:
                0 0 0 .15rem rgba(220, 53, 69, .15);

        }

        /* =====================================================
           INPUT CON ICONO
        ===================================================== */

        .input-group-text {

            background: #f8f9fa;

            border:
                1px solid #dee2e6;

            border-radius:
                12px 0 0 12px;

        }

        .input-group .form-control {

            border-radius:
                0 12px 12px 0;

        }


        /* =====================================================
           BOTON
        ===================================================== */

        .btn-login {

            width: 100%;

            min-height: 52px;

            border-radius: 30px;

            font-weight: 600;

            font-size: 1rem;

            background:
                var(--danger);

            border:
                none;

            transition:
                all .2s ease;

        }


        .btn-login:hover {

            background:
                #bb2d3b;

            transform:
                translateY(-1px);

        }


        .btn-login:disabled {

            opacity: .75;

            transform: none;

        }


        /* =====================================================
           PIE
        ===================================================== */

        .login-footer {

            text-align: center;

            color: #6c757d;

            font-size: .85rem;

            margin-top: 25px;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 576px) {

            .login-card {

                padding: 30px 22px;

            }

            .icon-circle {

                width: 85px;

                height: 85px;

                font-size: 32px;

            }

        }
    </style>

</head>

<body>

    <!-- ==========================================================
    NAVBAR
    =========================================================== -->

    <nav class="navbar navbar-dark bg-dark py-3">

        <div class="container">

            <span class="navbar-brand">

                <i class="fas fa-boxes-stacked me-2"></i>

                Sistema de Ingreso Porteria

            </span>

        </div>

    </nav>

    <!-- ==========================================================
    LOGIN
    =========================================================== -->

    <main class="login-container">

        <div class="login-card">

            <!-- ICONO -->

            <div class="icon-circle">

                <i class="fas fa-user-lock"></i>

            </div>

            <!-- TITULO -->

            <h1 class="login-title">

                Inicio de Sesión

            </h1>

            <p class="login-subtitle">

                Acceso al sistema de ingreso portería

            </p>


            <!-- ==================================================
            FORMULARIO
            =================================================== -->

            <form
                id="loginForm"
                autocomplete="off">


                <!-- USUARIO -->

                <div class="mb-4">

                    <label
                        for="usuario"
                        class="form-label">

                        Usuario

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="fas fa-user"></i>

                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="usuario"
                            name="username"
                            placeholder="Ingrese su usuario"
                            autocomplete="username"
                            required>

                    </div>

                </div>


                <!-- CONTRASEÑA -->

                <div class="mb-4">

                    <label
                        for="password"
                        class="form-label">

                        Contraseña

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="fas fa-lock"></i>

                        </span>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Ingrese su contraseña"
                            autocomplete="current-password"
                            required>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="mostrarPassword">

                            <i class="fas fa-eye"></i>

                        </button>

                    </div>

                </div>


                <!-- BOTON -->

                <button
                    type="submit"
                    class="btn btn-danger btn-login"
                    id="btnLogin">

                    <i class="fas fa-right-to-bracket me-2"></i>

                    Ingresar

                </button>


            </form>


            <!-- PIE -->

            <div class="login-footer">

                <i class="fas fa-shield-halved me-1"></i>

                Acceso autorizado al sistema

            </div>


        </div>

    </main>


    <!-- ==========================================================
     JAVASCRIPT
=========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                const form =
                    document.getElementById("loginForm");

                const usuario =
                    document.getElementById("usuario");

                const password =
                    document.getElementById("password");

                const boton =
                    document.getElementById("btnLogin");

                const mostrarPassword =
                    document.getElementById("mostrarPassword");


                /* =====================================================
                   MOSTRAR / OCULTAR CONTRASEÑA
                ===================================================== */

                mostrarPassword.addEventListener(
                    "click",
                    function() {

                        if (
                            password.type === "password"
                        ) {

                            password.type =
                                "text";

                            this.innerHTML =
                                '<i class="fas fa-eye-slash"></i>';

                        } else {

                            password.type =
                                "password";

                            this.innerHTML =
                                '<i class="fas fa-eye"></i>';

                        }

                    }
                );


                /* =====================================================
                   LOGIN
                ===================================================== */

                form.addEventListener(
                    "submit",
                    async function(event) {

                        event.preventDefault();


                        const user =
                            usuario.value.trim();

                        const pass =
                            password.value;


                        if (!user || !pass) {

                            Swal.fire({

                                icon: "warning",

                                title: "Datos incompletos",

                                text: "Ingrese usuario y contraseña.",

                                confirmButtonColor: "#dc3545"

                            });

                            return;

                        }


                        /* =============================================
                           ESTADO DEL BOTON
                        ============================================= */

                        boton.disabled = true;

                        boton.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>' +
                            'Verificando...';


                        try {


                            /* =========================================
                               PETICION AL PHP
                            ========================================= */

                            const response =
                                await fetch(
                                    "Controller/login.php", {
                                        method: "POST",

                                        headers: {
                                            "Content-Type": "application/json"
                                        },

                                        body: JSON.stringify({

                                            username: user,

                                            password: pass

                                        })
                                    }
                                );

                            /* =========================================
                               COMPROBAR RESPUESTA HTTP
                            ========================================= */

                            if (!response.ok) {

                                throw new Error(
                                    "HTTP " +
                                    response.status
                                );

                            }


                            /* =========================================
                               LEER JSON
                            ========================================= */

                            const texto = await response.text();

                            console.log("RESPUESTA DEL SERVIDOR:");
                            console.log(texto);

                            const data = JSON.parse(texto);


                            console.log(
                                "Respuesta login:",
                                data
                            );

                            if (data.success === true) {

                                window.location.href = data.redirect;

                                return;
                            }

                            /* =========================================
                            CREDENCIALES INCORRECTAS
                            ========================================= */

                            Swal.fire({

                                icon: "error",

                                title: "Acceso denegado",

                                text: data.message ||
                                    "Usuario o contraseña incorrectos.",

                                confirmButtonColor: "#dc3545",

                                confirmButtonText: "Aceptar"

                            });


                        } catch (error) {


                            console.error(
                                "Error login:",
                                error
                            );


                            Swal.fire({

                                icon: "error",

                                title: "Error de conexión",

                                text: "No fue posible comunicarse con el servidor.",

                                confirmButtonColor: "#dc3545",

                                confirmButtonText: "Aceptar"

                            });


                        } finally {


                            boton.disabled =
                                false;

                            boton.innerHTML =
                                '<i class="fas fa-right-to-bracket me-2"></i>' +
                                'Ingresar';

                        }

                    }
                );

            }
        );
    </script>


</body>

</html>