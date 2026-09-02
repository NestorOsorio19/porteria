<?php

require_once '../Config/database.php';

$con = connection();

try {

    /* ==========================================================
       CONSULTAR VEHÍCULOS
    ========================================================== */

    $sql = "
        SELECT
            v.*,
            a.nom_arl,
            e.nom_eps
        FROM vehiculos v

        LEFT JOIN arls a
            ON v.arl = a.id_arl

        LEFT JOIN eps e
            ON v.eps = e.id_eps

        ORDER BY v.id_registro DESC
    ";

    $stmt = $con->prepare($sql);
    $stmt->execute();

} catch (PDOException $e) {

    die(
        "Error al consultar los vehículos: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}

/* ==========================================================
   FUNCIÓN ESTADO DOCUMENTO
========================================================== */

function estadoDocumento($fecha)
{
    if (empty($fecha)) {

        return [
            'estado' => 'sin_fecha',
            'texto' => 'Sin fecha',
            'dias' => null,
            'clase' => 'estado-sin-fecha',
            'icono' => 'fa-circle-question'
        ];
    }

    try {

        $hoy = new DateTime('today');
        $vencimiento = new DateTime($fecha);

    } catch (Exception $e) {

        return [
            'estado' => 'sin_fecha',
            'texto' => 'Fecha inválida',
            'dias' => null,
            'clase' => 'estado-sin-fecha',
            'icono' => 'fa-circle-question'
        ];
    }

    $diferencia = $hoy->diff($vencimiento);

    $dias = (int) $diferencia->days;


    /* ======================================================
       VENCIDO
    ====================================================== */

    if ($vencimiento < $hoy) {

        return [
            'estado' => 'vencido',
            'texto' => 'Vencido',
            'dias' => -$dias,
            'clase' => 'estado-vencido',
            'icono' => 'fa-circle-xmark'
        ];
    }


    /* ======================================================
       VENCE HOY
    ====================================================== */

    if ($dias === 0) {

        return [
            'estado' => 'vence_hoy',
            'texto' => 'Vence hoy',
            'dias' => 0,
            'clase' => 'estado-hoy',
            'icono' => 'fa-triangle-exclamation'
        ];
    }


    /* ======================================================
       7 DÍAS O MENOS
    ====================================================== */

    if ($dias <= 7) {

        return [
            'estado' => 'urgente',
            'texto' => 'Vence pronto',
            'dias' => $dias,
            'clase' => 'estado-urgente',
            'icono' => 'fa-triangle-exclamation'
        ];
    }


    /* ======================================================
       30 DÍAS O MENOS
    ====================================================== */

    if ($dias <= 30) {

        return [
            'estado' => 'proximo',
            'texto' => 'Próximo a vencer',
            'dias' => $dias,
            'clase' => 'estado-proximo',
            'icono' => 'fa-clock'
        ];
    }


    /* ======================================================
       VIGENTE
    ====================================================== */

    return [
        'estado' => 'vigente',
        'texto' => 'Vigente',
        'dias' => $dias,
        'clase' => 'estado-vigente',
        'icono' => 'fa-circle-check'
    ];
}


/* ==========================================================
   CONTADORES
========================================================== */

$totalVehiculos = 0;

$totalVigentes = 0;

$totalProximos = 0;

$totalUrgentes = 0;

$totalVencidos = 0;

$totalAtencion = 0;


/* ==========================================================
   ALERTAS PARA JAVASCRIPT
========================================================== */

$alertasDocumentos = [];


/* ==========================================================
   GUARDAR RESULTADOS
========================================================== */

$rows = [];


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $rows[] = $row;

    $totalVehiculos++;


    /* ======================================================
       ESTADOS DOCUMENTOS
    ====================================================== */

    $estadoLicencia =
        estadoDocumento(
            $row['licencia'] ?? ''
        );

    $estadoSoat =
        estadoDocumento(
            $row['fecha_soat'] ?? ''
        );

    $estadoRevision =
        estadoDocumento(
            $row['revision_tecn'] ?? ''
        );


    $documentos = [

        'Licencia' => $estadoLicencia,

        'SOAT' => $estadoSoat,

        'Técnico-mecánica' => $estadoRevision

    ];


    foreach ($documentos as $nombreDocumento => $estado) {

        if (
            $estado['estado'] === 'vencido' ||
            $estado['estado'] === 'vence_hoy' ||
            $estado['estado'] === 'urgente' ||
            $estado['estado'] === 'proximo'
        ) {

            $totalAtencion++;


            $alertasDocumentos[] = [

                'nombre' =>
                    $row['nombre'] ?? 'Sin nombre',

                'placa' =>
                    $row['placa'] ?? 'Sin placa',

                'documento' =>
                    $nombreDocumento,

                'estado' =>
                    $estado['estado'],

                'texto' =>
                    $estado['texto'],

                'dias' =>
                    $estado['dias'],

                'fecha' =>
                    $nombreDocumento === 'Licencia'
                        ? ($row['licencia'] ?? '')
                        : (
                            $nombreDocumento === 'SOAT'
                                ? ($row['fecha_soat'] ?? '')
                                : ($row['revision_tecn'] ?? '')
                        )
            ];
        }


        /* ==================================================
           CONTADORES
        ================================================== */

        if ($estado['estado'] === 'vigente') {

            $totalVigentes++;

        } elseif ($estado['estado'] === 'proximo') {

            $totalProximos++;

        } elseif (
            $estado['estado'] === 'urgente' ||
            $estado['estado'] === 'vence_hoy'
        ) {

            $totalUrgentes++;

        } elseif ($estado['estado'] === 'vencido') {

            $totalVencidos++;
        }
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Consulta Vehículos</title>


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- =====================================================
         DATATABLES
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            font-family: Arial, sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #f5f7fa 0%,
                    #e9eef5 100%
                );

            color: #2f3640;

            margin: 0;

            padding: 0;
        }


        .users-table {

            max-width: 1600px;

            margin: 30px auto;

            padding: 20px;
        }


        /* =====================================================
           ENCABEZADO
        ====================================================== */

        .card-header-custom {

            background: #ffffff;

            border-radius: 18px;

            padding: 22px 25px;

            box-shadow:
                0 8px 25px rgba(0,0,0,.08);

            margin-bottom: 20px;

            border: 1px solid #edf0f5;
        }


        .card-header-custom h2 {

            margin: 0;

            color: #192a56;

            font-weight: 700;
        }


        .card-header-custom small {

            color: #718093;

            display: block;

            margin-top: 5px;
        }


        .header-buttons {

            display: flex;

            gap: 10px;

            align-items: center;
        }


        .btn-nuevo,
        .btn-menu {

            color: white;

            border: none;

            padding: 10px 18px;

            border-radius: 9px;

            text-decoration: none;

            font-weight: 600;

            transition: .2s;

            white-space: nowrap;

            display: inline-flex;

            align-items: center;

            gap: 7px;
        }


        .btn-nuevo {

            background: #273c75;
        }


        .btn-nuevo:hover {

            background: #192a56;

            color: white;

            transform: translateY(-1px);
        }


        .btn-menu {

            background: #718093;
        }


        .btn-menu:hover {

            background: #576574;

            color: white;

            transform: translateY(-1px);
        }


        /* =====================================================
           TARJETAS RESUMEN
        ====================================================== */

        .resumen-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 20px;
        }


        .resumen-card {

            background: white;

            border-radius: 16px;

            padding: 18px;

            box-shadow:
                0 6px 20px rgba(0,0,0,.07);

            border-left: 5px solid;

            display: flex;

            align-items: center;

            gap: 15px;

            transition: .2s;
        }


        .resumen-card:hover {

            transform: translateY(-2px);

            box-shadow:
                0 10px 25px rgba(0,0,0,.10);
        }


        .resumen-icon {

            width: 50px;

            height: 50px;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;
        }


        .resumen-info small {

            display: block;

            color: #718093;

            font-weight: 600;

            margin-bottom: 2px;
        }


        .resumen-info strong {

            font-size: 27px;

            line-height: 1;

            color: #2f3640;
        }


        .card-vigentes {

            border-color: #198754;
        }


        .card-vigentes .resumen-icon {

            background: #d1e7dd;

            color: #198754;
        }


        .card-proximos {

            border-color: #ffc107;
        }


        .card-proximos .resumen-icon {

            background: #fff3cd;

            color: #997404;
        }


        .card-urgentes {

            border-color: #fd7e14;
        }


        .card-urgentes .resumen-icon {

            background: #ffe5d0;

            color: #fd7e14;
        }


        .card-vencidos {

            border-color: #dc3545;
        }


        .card-vencidos .resumen-icon {

            background: #f8d7da;

            color: #dc3545;
        }


        /* =====================================================
           CONTENEDOR TABLA
        ====================================================== */

        .table-container {

            background: #fff;

            padding: 20px;

            border-radius: 18px;

            box-shadow:
                0 8px 25px rgba(0,0,0,.08);

            border: 1px solid #edf0f5;
        }


        /* =====================================================
           BARRA HORIZONTAL SUPERIOR
        ====================================================== */

        .top-scroll-wrapper {

            width: 100%;

            overflow-x: auto;

            overflow-y: hidden;

            height: 22px;

            margin-bottom: 5px;

            border-radius: 8px;

            background: #f1f3f5;

            border: 1px solid #dee2e6;
        }


        .top-scroll-content {

            height: 1px;

            min-width: 1800px;
        }


        /*
         * Hacemos la barra superior más visible
         */

        .top-scroll-wrapper::-webkit-scrollbar {

            height: 14px;
        }


        .top-scroll-wrapper::-webkit-scrollbar-track {

            background: #e9ecef;

            border-radius: 8px;
        }


        .top-scroll-wrapper::-webkit-scrollbar-thumb {

            background: #273c75;

            border-radius: 8px;

            border: 3px solid #e9ecef;
        }


        .top-scroll-wrapper::-webkit-scrollbar-thumb:hover {

            background: #192a56;
        }


        /* =====================================================
           BARRA INFERIOR
        ====================================================== */

        .table-responsive {

            overflow-x: auto;

            overflow-y: visible;

            width: 100%;

            padding-bottom: 4px;
        }


        .table-responsive::-webkit-scrollbar {

            height: 14px;
        }


        .table-responsive::-webkit-scrollbar-track {

            background: #e9ecef;

            border-radius: 8px;
        }


        .table-responsive::-webkit-scrollbar-thumb {

            background: #273c75;

            border-radius: 8px;

            border: 3px solid #e9ecef;
        }


        .table-responsive::-webkit-scrollbar-thumb:hover {

            background: #192a56;
        }


        /* =====================================================
           TABLA
        ====================================================== */

        table.dataTable {

            width: 100% !important;

            min-width: 1800px;
        }


        table.dataTable thead th {

            background-color: #273c75 !important;

            color: #fff !important;

            font-weight: 600;

            text-align: center;

            vertical-align: middle;

            white-space: nowrap;
        }


        table.dataTable tbody td {

            text-align: center;

            vertical-align: middle;

            white-space: nowrap;
        }


        table.dataTable tbody tr:hover {

            background-color: #f1f2f6 !important;
        }


        /* =====================================================
           PLACA
        ====================================================== */

        .placa {

            background: #2f3640;

            color: white;

            padding: 5px 11px;

            border-radius: 6px;

            font-weight: bold;

            letter-spacing: 1px;

            display: inline-block;
        }


        /* =====================================================
           HORAS
        ====================================================== */

        .hora-ingreso {

            background: #198754;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;
        }


        .hora-salida {

            background: #0dcaf0;

            color: #083344;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;
        }


        .pendiente {

            background: #ffc107;

            color: #212529;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;
        }


        /* =====================================================
           BOTÓN SALIDA
        ====================================================== */

        .btn-salida {

            background: #e84118;

            color: white;

            border: none;

            padding: 7px 13px;

            border-radius: 7px;

            font-weight: 600;

            cursor: pointer;

            transition: .2s;
        }


        .btn-salida:hover {

            background: #c23616;

            transform: translateY(-1px);
        }


        .btn-salida:disabled {

            opacity: .7;

            cursor: not-allowed;

            transform: none;
        }


        /* =====================================================
           FINALIZADO
        ====================================================== */

        .finalizado {

            background: #198754;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;
        }


        /* =====================================================
           DOCUMENTOS
        ====================================================== */

        .documento-box {

            min-width: 170px;

            text-align: left;

            padding: 8px 10px;

            border-radius: 10px;

            margin: 3px 0;

            border: 1px solid transparent;
        }


        .documento-box strong {

            display: block;

            font-size: 12px;

            margin-bottom: 2px;
        }


        .documento-box small {

            display: block;

            font-size: 11px;
        }


        .documento-box .estado-linea {

            display: flex;

            align-items: center;

            gap: 5px;

            font-weight: 700;

            font-size: 12px;
        }


        /* =====================================================
           ESTADOS DOCUMENTOS
        ====================================================== */

        .estado-vigente {

            background: #e8f7ee;

            border-color: #b7e4c7;

            color: #146c43;
        }


        .estado-proximo {

            background: #fff8df;

            border-color: #ffe69c;

            color: #856404;
        }


        .estado-urgente {

            background: #fff0e5;

            border-color: #ffcfad;

            color: #b54708;
        }


        .estado-hoy {

            background: #ffe5e8;

            border-color: #f5b5bd;

            color: #b02a37;

            animation: alertaSuave 1.8s infinite;
        }


        .estado-vencido {

            background: #fcebed;

            border-color: #f1aeb5;

            color: #842029;
        }


        .estado-sin-fecha {

            background: #f1f3f5;

            border-color: #dee2e6;

            color: #6c757d;
        }


        @keyframes alertaSuave {

            0% {

                box-shadow:
                    0 0 0 0 rgba(220,53,69,.15);
            }

            70% {

                box-shadow:
                    0 0 0 6px rgba(220,53,69,0);
            }

            100% {

                box-shadow:
                    0 0 0 0 rgba(220,53,69,0);
            }
        }


        /* =====================================================
           DATATABLES
        ====================================================== */

        .dataTables_wrapper .dataTables_filter input {

            border: 1px solid #ced4da;

            border-radius: 8px;

            padding: 7px 10px;

            margin-left: 5px;
        }


        .dataTables_wrapper .dataTables_length select {

            border: 1px solid #ced4da;

            border-radius: 8px;

            padding: 5px;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media screen and (max-width: 1000px) {

            .resumen-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }
        }


        @media screen and (max-width: 768px) {

            .users-table {

                margin: 10px auto;

                padding: 10px;
            }


            .card-header-custom {

                padding: 15px;
            }


            .header-content {

                flex-direction: column;

                align-items: flex-start !important;

                gap: 15px;
            }


            .header-buttons {

                width: 100%;

                flex-direction: column;
            }


            .btn-nuevo,
            .btn-menu {

                width: 100%;

                justify-content: center;
            }


            .resumen-grid {

                grid-template-columns: 1fr;
            }


            .table-container {

                padding: 10px;
            }


            .top-scroll-wrapper {

                margin-bottom: 5px;
            }
        }

    </style>

</head>


<body>


<div class="users-table">


    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="card-header-custom">

        <div
            class="header-content d-flex justify-content-between align-items-center"
        >

            <div>

                <h2>

                    <i class="fas fa-truck text-danger"></i>

                    Registro General de Vehículos

                </h2>

                <small>

                    Consulta y control de ingreso, salida y documentación

                </small>

            </div>


            <div class="header-buttons">

                <a
                    href="../index.php"
                    class="btn-menu"
                >

                    <i class="fas fa-home"></i>

                    Menú

                </a>


                <a
                    href="registro_vehiculos.php"
                    class="btn-nuevo"
                >

                    <i class="fas fa-plus"></i>

                    Nuevo Registro

                </a>

            </div>

        </div>

    </div>


    <!-- =====================================================
         RESUMEN
    ====================================================== -->

    <div class="resumen-grid">


        <div class="resumen-card card-vigentes">

            <div class="resumen-icon">

                <i class="fas fa-circle-check"></i>

            </div>

            <div class="resumen-info">

                <small>Documentos vigentes</small>

                <strong>
                    <?= $totalVigentes ?>
                </strong>

            </div>

        </div>


        <div class="resumen-card card-proximos">

            <div class="resumen-icon">

                <i class="fas fa-clock"></i>

            </div>

            <div class="resumen-info">

                <small>Próximos a vencer</small>

                <strong>
                    <?= $totalProximos ?>
                </strong>

            </div>

        </div>


        <div class="resumen-card card-urgentes">

            <div class="resumen-icon">

                <i class="fas fa-triangle-exclamation"></i>

            </div>

            <div class="resumen-info">

                <small>Vencen pronto</small>

                <strong>
                    <?= $totalUrgentes ?>
                </strong>

            </div>

        </div>


        <div class="resumen-card card-vencidos">

            <div class="resumen-icon">

                <i class="fas fa-circle-xmark"></i>

            </div>

            <div class="resumen-info">

                <small>Documentos vencidos</small>

                <strong>
                    <?= $totalVencidos ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- =====================================================
         TABLA
    ====================================================== -->

    <div class="table-container">


        <!-- =================================================
             BARRA HORIZONTAL SUPERIOR
        ================================================== -->

        <div
            id="scrollSuperior"
            class="top-scroll-wrapper"
            title="Deslice para mover la tabla horizontalmente"
        >

            <div
                id="scrollSuperiorContenido"
                class="top-scroll-content"
            ></div>

        </div>


        <!-- =================================================
             TABLA CON BARRA INFERIOR
        ================================================== -->

        <div
            id="scrollInferior"
            class="table-responsive"
        >

            <table
                id="tablaVehiculos"
                class="table table-striped table-hover align-middle"
            >

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Fecha</th>

                        <th>Nombre</th>

                        <th>Cédula</th>

                        <th>Placa</th>

                        <th>ARL</th>

                        <th>EPS</th>

                        <th>Tipo Visita</th>

                        <th>Procedencia</th>

                        <th>Destino</th>

                        <th>Ingreso</th>

                        <th>Salida</th>

                        <th>Documentación</th>

                        <th>Registro</th>

                        <th>Acción</th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach ($rows as $row): ?>


                    <?php

                    $id =
                        (int) $row['id_registro'];


                    $horaSalida =
                        trim(
                            $row['hora_salida'] ?? ''
                        );


                    $tieneSalida =
                        $horaSalida !== '' &&
                        $horaSalida !== '00:00:00';


                    $estadoLicencia =
                        estadoDocumento(
                            $row['licencia'] ?? ''
                        );


                    $estadoSoat =
                        estadoDocumento(
                            $row['fecha_soat'] ?? ''
                        );


                    $estadoRevision =
                        estadoDocumento(
                            $row['revision_tecn'] ?? ''
                        );

                    ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?= $id ?>

                        </td>


                        <!-- FECHA -->

                        <td>

                            <?= htmlspecialchars(
                                $row['fecha'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- NOMBRE -->

                        <td>

                            <?= htmlspecialchars(
                                $row['nombre'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- CÉDULA -->

                        <td>

                            <?= htmlspecialchars(
                                $row['cedula'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- PLACA -->

                        <td>

                            <span class="placa">

                                <?= htmlspecialchars(
                                    $row['placa'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        </td>


                        <!-- ARL -->

                        <td>

                            <?= htmlspecialchars(
                                $row['nom_arl'] ?? 'Sin ARL',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- EPS -->

                        <td>

                            <?= htmlspecialchars(
                                $row['nom_eps'] ?? 'Sin EPS',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- TIPO VISITA -->

                        <td>

                            <?= htmlspecialchars(
                                $row['tipo_visita'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- PROCEDENCIA -->

                        <td>

                            <?= htmlspecialchars(
                                $row['procedencia'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- DESTINO -->

                        <td>

                            <?= htmlspecialchars(
                                $row['destino'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- INGRESO -->

                        <td>

                            <?php if (!empty($row['hora_ingreso'])): ?>

                                <span class="hora-ingreso">

                                    <?= htmlspecialchars(
                                        $row['hora_ingreso'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            <?php else: ?>

                                <span class="text-muted">
                                    —
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- SALIDA -->

                        <td id="salida_<?= $id ?>">

                            <?php if (!$tieneSalida): ?>

                                <span class="pendiente">

                                    Pendiente

                                </span>

                            <?php else: ?>

                                <span class="hora-salida">

                                    <?= htmlspecialchars(
                                        $horaSalida,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- DOCUMENTACIÓN -->

                        <td>


                            <!-- LICENCIA -->

                            <div
                                class="documento-box <?= $estadoLicencia['clase'] ?>"
                            >

                                <strong>

                                    <i class="fas fa-id-card me-1"></i>

                                    Licencia

                                </strong>


                                <div class="estado-linea">

                                    <i
                                        class="fas <?= $estadoLicencia['icono'] ?>"
                                    ></i>

                                    <?= $estadoLicencia['texto'] ?>

                                </div>


                                <?php if ($estadoLicencia['dias'] !== null): ?>

                                    <small>

                                        <?php if (
                                            $estadoLicencia['estado'] === 'vencido'
                                        ): ?>

                                            Vencida hace
                                            <?= abs($estadoLicencia['dias']) ?>
                                            día(s)

                                        <?php elseif (
                                            $estadoLicencia['dias'] === 0
                                        ): ?>

                                            Vence hoy

                                        <?php else: ?>

                                            <?= $estadoLicencia['dias'] ?>
                                            día(s) restantes

                                        <?php endif; ?>

                                    </small>

                                <?php endif; ?>

                            </div>


                            <!-- SOAT -->

                            <div
                                class="documento-box <?= $estadoSoat['clase'] ?>"
                            >

                                <strong>

                                    <i class="fas fa-car me-1"></i>

                                    SOAT

                                </strong>


                                <div class="estado-linea">

                                    <i
                                        class="fas <?= $estadoSoat['icono'] ?>"
                                    ></i>

                                    <?= $estadoSoat['texto'] ?>

                                </div>


                                <?php if ($estadoSoat['dias'] !== null): ?>

                                    <small>

                                        <?php if (
                                            $estadoSoat['estado'] === 'vencido'
                                        ): ?>

                                            Vencido hace
                                            <?= abs($estadoSoat['dias']) ?>
                                            día(s)

                                        <?php elseif (
                                            $estadoSoat['dias'] === 0
                                        ): ?>

                                            Vence hoy

                                        <?php else: ?>

                                            <?= $estadoSoat['dias'] ?>
                                            día(s) restantes

                                        <?php endif; ?>

                                    </small>

                                <?php endif; ?>

                            </div>


                            <!-- TÉCNICO MECÁNICA -->

                            <div
                                class="documento-box <?= $estadoRevision['clase'] ?>"
                            >

                                <strong>

                                    <i class="fas fa-screwdriver-wrench me-1"></i>

                                    Técnico-mecánica

                                </strong>


                                <div class="estado-linea">

                                    <i
                                        class="fas <?= $estadoRevision['icono'] ?>"
                                    ></i>

                                    <?= $estadoRevision['texto'] ?>

                                </div>


                                <?php if ($estadoRevision['dias'] !== null): ?>

                                    <small>

                                        <?php if (
                                            $estadoRevision['estado'] === 'vencido'
                                        ): ?>

                                            Vencida hace
                                            <?= abs($estadoRevision['dias']) ?>
                                            día(s)

                                        <?php elseif (
                                            $estadoRevision['dias'] === 0
                                        ): ?>

                                            Vence hoy

                                        <?php else: ?>

                                            <?= $estadoRevision['dias'] ?>
                                            día(s) restantes

                                        <?php endif; ?>

                                    </small>

                                <?php endif; ?>

                            </div>

                        </td>


                        <!-- REGISTRO -->

                        <td>

                            <?= htmlspecialchars(
                                $row['registro'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <!-- ACCIÓN -->

                        <td id="accion_<?= $id ?>">

                            <?php if (!$tieneSalida): ?>

                                <button
                                    type="button"
                                    id="btnSalida_<?= $id ?>"
                                    class="btn-salida"
                                    onclick="marcarSalida(<?= $id ?>)"
                                >

                                    <i class="fas fa-sign-out-alt"></i>

                                    Salida

                                </button>

                            <?php else: ?>

                                <span class="finalizado">

                                    <i class="fas fa-check"></i>

                                    Finalizado

                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>


                <?php endforeach; ?>


                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- =====================================================
     JQUERY
====================================================== -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
></script>


<!-- =====================================================
     BOOTSTRAP
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<!-- =====================================================
     DATATABLES
====================================================== -->

<script
    src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"
></script>

<script
    src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"
></script>


<!-- =====================================================
     SWEETALERT
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
></script>


<script>


/* =========================================================
   VARIABLES BARRAS HORIZONTALES
========================================================= */

let scrollSuperior;
let scrollInferior;
let scrollSuperiorContenido;


/* =========================================================
   CONFIGURAR BARRA SUPERIOR
========================================================= */

function configurarScrollHorizontal() {

    scrollSuperior =
        document.getElementById(
            'scrollSuperior'
        );


    scrollInferior =
        document.getElementById(
            'scrollInferior'
        );


    scrollSuperiorContenido =
        document.getElementById(
            'scrollSuperiorContenido'
        );


    if (
        !scrollSuperior ||
        !scrollInferior ||
        !scrollSuperiorContenido
    ) {

        return;
    }


    /* =====================================================
       OBTENER ANCHO REAL DE LA TABLA
    ===================================================== */

    const tabla =
        document.getElementById(
            'tablaVehiculos'
        );


    if (!tabla) {

        return;
    }


    const anchoTabla =
        tabla.scrollWidth;


    /*
     * El contenido invisible de la barra superior
     * tendrá exactamente el ancho de la tabla.
     */

    scrollSuperiorContenido.style.width =
        anchoTabla + 'px';


    /* =====================================================
       SINCRONIZAR SUPERIOR → INFERIOR
    ===================================================== */

    scrollSuperior.addEventListener(
        'scroll',
        function() {

            scrollInferior.scrollLeft =
                scrollSuperior.scrollLeft;

        }
    );


    /* =====================================================
       SINCRONIZAR INFERIOR → SUPERIOR
    ===================================================== */

    scrollInferior.addEventListener(
        'scroll',
        function() {

            scrollSuperior.scrollLeft =
                scrollInferior.scrollLeft;

        }
    );
}


/* =========================================================
   DATATABLE
========================================================= */

$(document).ready(function() {


    const tabla =
        $('#tablaVehiculos').DataTable({

            pageLength: 10,

            autoWidth: false,

            scrollX: false,

            order: [
                [0, 'desc']
            ],

            language: {

                search:
                    "Buscar:",

                lengthMenu:
                    "Mostrar _MENU_ registros",

                info:
                    "Mostrando _START_ a _END_ de _TOTAL_ registros",

                infoEmpty:
                    "No hay registros disponibles",

                infoFiltered:
                    "(filtrado de _MAX_ registros)",

                zeroRecords:
                    "No se encontraron registros",

                paginate: {

                    first:
                        "Primero",

                    last:
                        "Último",

                    next:
                        "Siguiente",

                    previous:
                        "Anterior"
                }
            }

        });


    /* =====================================================
       CONFIGURAR SCROLL DESPUÉS DE DATATABLE
    ===================================================== */

    setTimeout(
        configurarScrollHorizontal,
        200
    );


    /* =====================================================
       REAJUSTAR CUANDO DATATABLE CAMBIE
    ===================================================== */

    tabla.on(
        'draw',
        function() {

            setTimeout(
                configurarScrollHorizontal,
                50
            );

        }
    );


    /* =====================================================
       REAJUSTAR AL CAMBIAR TAMAÑO DE VENTANA
    ===================================================== */

    $(window).on(
        'resize',
        function() {

            setTimeout(
                configurarScrollHorizontal,
                100
            );

        }
    );


    /* =====================================================
       ALERTA DE DOCUMENTOS
    ===================================================== */

    const alertas =
        <?= json_encode(
            $alertasDocumentos,
            JSON_UNESCAPED_UNICODE |
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_QUOT |
            JSON_HEX_AMP
        ) ?>;


    if (alertas.length > 0) {


        let vencidos = 0;

        let proximos = 0;


        alertas.forEach(
            function(item) {

                if (
                    item.estado === 'vencido' ||
                    item.estado === 'vence_hoy'
                ) {

                    vencidos++;

                } else {

                    proximos++;
                }

            }
        );


        let html = '';


        if (vencidos > 0) {

            html +=

                '<div class="alerta-resumen">' +

                    '<i class="fas fa-circle-xmark text-danger me-2"></i>' +

                    '<strong>' +

                    vencidos +

                    '</strong> documento(s) vencido(s) o que vencen hoy.' +

                '</div>';
        }


        if (proximos > 0) {

            html +=

                '<div class="alerta-resumen mt-2">' +

                    '<i class="fas fa-clock text-warning me-2"></i>' +

                    '<strong>' +

                    proximos +

                    '</strong> documento(s) próximo(s) a vencer.' +

                '</div>';
        }


        Swal.fire({

            title:

                '<i class="fas fa-file-circle-exclamation text-warning me-2"></i>' +

                'Documentación por revisar',

            html:
                html,

            icon:

                vencidos > 0
                    ? 'warning'
                    : 'info',

            confirmButtonText:
                'Entendido',

            confirmButtonColor:
                '#273c75',

            width:
                520

        });

    }

});


/* =========================================================
   REGISTRAR SALIDA
========================================================= */

function marcarSalida(id) {


    Swal.fire({

        title:
            '¿Registrar salida?',

        text:
            'La salida del vehículo será registrada.',

        icon:
            'question',

        showCancelButton:
            true,

        confirmButtonText:
            'Sí, registrar',

        cancelButtonText:
            'Cancelar',

        confirmButtonColor:
            '#dc3545',

        cancelButtonColor:
            '#6c757d',

        reverseButtons:
            true

    }).then(
        function(result) {


            if (!result.isConfirmed) {

                return;
            }


            const $boton =
                $('#btnSalida_' + id);


            $boton

                .prop(
                    'disabled',
                    true
                )

                .html(

                    '<i class="fas fa-spinner fa-spin"></i> ' +

                    'Registrando...'
                );


            $.ajax({

                url:
                    '../Controller/salida_vehiculo.php',

                type:
                    'POST',

                data: {

                    id: id

                },

                dataType:
                    'json',


                success:
                    function(response) {


                        console.log(
                            'RESPUESTA PHP:',
                            response
                        );


                        if (
                            response.ok === true
                        ) {

                            const horaSalida =
                                response.hora_salida;

                            $('#salida_' + id).html(

                                '<span class="hora-salida">' +

                                horaSalida +

                                '</span>'
                            );

                            $('#accion_' + id).html(

                                '<span class="finalizado">' +

                                '<i class="fas fa-check"></i> ' +

                                'Finalizado' +

                                '</span>'
                            );

                            Swal.fire({

                                title:
                                    '¡Salida registrada!',

                                text:
                                    'La salida fue registrada correctamente.',

                                icon:
                                    'success',

                                confirmButtonColor:
                                    '#198754',

                                timer:
                                    2000,

                                timerProgressBar:
                                    true

                            });

                        } else {

                            $('#btnSalida_' + id)

                                .prop(
                                    'disabled',
                                    false
                                )

                                .html(

                                    '<i class="fas fa-sign-out-alt"></i> ' +

                                    'Salida'
                                );

                            Swal.fire({

                                title:
                                    'Aviso',

                                text:
                                    response.mensaje ||
                                    'No se pudo registrar la salida.',

                                icon:
                                    'warning',

                                confirmButtonColor:
                                    '#ffc107'

                            });

                        }

                    },

                error:
                    function(xhr, status, error) {

                        console.error(
                            '========== ERROR AJAX =========='
                        );

                        console.error(
                            'HTTP:',
                            xhr.status
                        );

                        console.error(
                            'STATUS:',
                            status
                        );

                        console.error(
                            'ERROR:',
                            error
                        );

                        console.error(
                            'RESPUESTA:',
                            xhr.responseText
                        );

                        console.error(
                            '================================'
                        );

                        $('#btnSalida_' + id)

                            .prop(
                                'disabled',
                                false
                            )

                            .html(

                                '<i class="fas fa-sign-out-alt"></i> ' +

                                'Salida'
                            );

                        Swal.fire({

                            title:
                                'Error',

                            text:
                                'No fue posible registrar la salida.',

                            icon:
                                'error',

                            confirmButtonColor:
                                '#dc3545'

                        });

                    }

            });

        }
    );

}

</script>

</body>

</html>