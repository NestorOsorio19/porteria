<?php
session_start();

/* ======================================================
CONFIGURACIÓN DE SEGURIDAD Y SESIÓN
====================================================== */

// Evitar caché del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* ======================================================
MÓDULOS DEL SISTEMA
Cada módulo genera automáticamente una tarjeta
====================================================== */

$modulos = [
    [
        "titulo" => "Colaboradores",
        "subtitulo" => "Formulario",
        "icono" => "fa-house",
        "color" => "gradient-1",
        "link" => "View/registro_colaboradores.php"
    ],

    [
        "titulo" => "Visitantes",
        "subtitulo" => "Formulario",
        "icono" => "fa-person",
        "color" => "gradient-2",
        "link" => "View/registro_visitantes.php"
    ],

    [
        "titulo" => "Contratistas",
        "subtitulo" => "Formulario",
        "icono" => "fa-briefcase",
        "color" => "gradient-3",
        "link" => "View/registro_contratistas.php"
    ],

    [
        "titulo" => "Vehiculos Externos",
        "subtitulo" => "Formulario",
        "icono" => "fa-truck",
        "color" => "gradient-4",
        "link" => "View/registro_vehiculos_externos.php"
    ],

    [
        "titulo" => "Vehiculos Internos",
        "subtitulo" => "Formulario",
        "icono" => "fa-truck-front",
        "color" => "gradient-8",
        "link" => "View/registro_vehiculos_internos.php"
    ],

    [
        "titulo" => "Tabla de Registros",
        "subtitulo" => "Formulario",
        "icono" => "fa-table",
        "color" => "gradient-7",
        "link" => "View/menu_tablas.php"
    ]
];
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel Principal</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- ESTILOS PERSONALIZADOS -->
    <style>
        :root {
            --bg: #edf4ff;
            --card: #f8fbff;
            --text: #1e293b;
            --muted: #64748b;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg,
                    #dbeafe,
                    #eef6ff,
                    #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }

        /* NAVBAR */

        .clay-navbar {
            margin: 20px;
            padding: 15px 25px;
            border-radius: 25px;
            background: rgba(255, 255, 255, .75);
            backdrop-filter: blur(15px);

            box-shadow:
                12px 12px 30px rgba(0, 0, 0, .08),
                -12px -12px 30px rgba(255, 255, 255, .95);
        }

        .navbar-brand {
            font-weight: 700;
            color: #1e293b;
        }

        .user-info {
            color: #334155;
            font-weight: 500;
        }

        /* TITULO */

        .welcome-box {
            text-align: center;
            margin-bottom: 50px;
        }

        .welcome-box h1 {
            color: var(--text);
            font-size: 2.7rem;
            font-weight: 800;
        }

        .welcome-box p {
            color: var(--muted);
            font-size: 1.1rem;
        }

        /* INFO */

        .info-card {
            border: none;
            border-radius: 30px;
            background: var(--card);

            box-shadow:
                15px 15px 40px rgba(0, 0, 0, .08),
                -15px -15px 40px rgba(255, 255, 255, .95);

            padding: 25px;
            text-align: center;
        }

        .info-card h3 {
            color: #2563eb;
            font-weight: 800;
        }

        .info-card p {
            margin-bottom: 0;
            color: #64748b;
        }

        /* MODULOS */

        .info-card {
            height: 100%;
            padding: 20px;
            border-radius: 12px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .info-card h3 {
            color: #2563eb;
            font-weight: 800;
        }

        .info-card p {
            margin-bottom: 0;
            color: #64748b;
        }

        .menu-card {
            border: none;
            border-radius: 30px;
            background: #f8fbff;

            box-shadow:
                15px 15px 40px rgba(0, 0, 0, .08),
                -15px -15px 40px rgba(255, 255, 255, .95);

            transition: all .35s ease;
        }

        .menu-card:hover {
            transform: translateY(-10px);

            box-shadow:
                25px 25px 50px rgba(0, 0, 0, .12),
                -15px -15px 40px rgba(255, 255, 255, 1);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .icon-circle {
            width: 100px;
            height: 100px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: auto;

            border-radius: 28px;

            color: white;
            font-size: 38px;

            box-shadow:
                0 15px 25px rgba(0, 0, 0, .15);
        }

        .card-title {
            font-weight: 800;
            margin-top: 20px;
            color: #1e293b;
        }

        .card-text {
            color: #64748b;
        }

        /* GRADIENTES */

        .gradient-1 {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .gradient-2 {
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
        }

        .gradient-3 {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
        }

        .gradient-4 {
            background: linear-gradient(135deg, #ff512f, #dd2476);
        }

        .gradient-5 {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
        }

        .gradient-6 {
            background: linear-gradient(135deg, #f7971e, #ffd200);
        }

        .gradient-7 {
            background: linear-gradient(135deg, #a18cd1, #fbc2eb);
        }

        .gradient-8 {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .gradient-9 {
            background: linear-gradient(135deg, #fc466b, #3f5efb);
        }

        /* BOTONES */

        .btn-clay {
            border: none;
            border-radius: 18px;
            padding: 12px 28px;

            color: white !important;
            font-weight: 700;

            box-shadow:
                0 10px 20px rgba(0, 0, 0, .15);

            transition: .3s;
        }

        .btn-clay:hover {
            transform: translateY(-3px);
        }

        .logout-btn {
            border-radius: 15px;
        }

        @media(max-width:768px) {

            .welcome-box h1 {
                font-size: 2rem;
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

    <!-- =========================================
        NAVBAR SUPERIOR
    ========================================== -->
    <nav class="clay-navbar">

        <div class="d-flex justify-content-between align-items-center">

            <div class="navbar-brand">
                <i class="fas fa-layer-group me-2"></i>
                Plataforma Ingresos - Planta Lebrija
            </div>

            <div>

                <span class="user-info me-3">
                    Bienvenido
                </span>

            </div>

        </div>

    </nav>

    <!-- =========================================
        CONTENIDO PRINCIPAL
    ========================================== -->
    <div class="container py-5">

        <!-- BIENVENIDA -->
        <div class="welcome-box">

            <h1>Menu Principal</h1>

            <p>
                Ingreso Modulos de Registro
            </p>

        </div>

        <!-- GRID DE MÓDULOS -->

        <div class="row g-4">

            <!-- GRID DE TARJETAS -->
            <div class="row g-4">

                <?php foreach ($modulos as $modulo): ?>

                    <div class="col-12 col-sm-6 col-lg-4">

                        <div class="card menu-card shadow-sm h-100">

                            <div class="card-body text-center p-4">

                                <!-- ICONO -->

                                <div class="icon-circle <?= $modulo['color'] ?>">
                                    <i class="fas <?= $modulo['icono'] ?>"></i>
                                </div>

                                <!-- TITULO -->
                                <h5 class="card-title">
                                    <?= strtoupper($modulo['titulo']) ?>
                                </h5>

                                <!-- SUBTITULO -->
                                <p class="text-muted">
                                    <?= $modulo['subtitulo'] ?>
                                </p>

                                <!-- BOTÓN -->
                                <a href="<?= $modulo['link'] ?>"
                                    class="btn btn-clay <?= $modulo['color'] ?>">
                                    Abrir módulo
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>