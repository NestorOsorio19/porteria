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

    die("Error al consultar los vehículos: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        ));
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

/* ==========================================================
TODOS LOS DOCUMENTOS PARA EL MODAL
========================================================== */
$documentosVehiculos = [];

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


    /* ======================================================
    CONTAR ESTADOS DE LOS DOCUMENTOS
    ====================================================== */

    foreach ($documentos as $estado) {

        switch ($estado['estado']) {

            case 'vigente':
                $totalVigentes++;
                break;

            case 'proximo':
                $totalProximos++;
                break;

            case 'urgente':
            case 'vence_hoy':
                $totalUrgentes++;
                break;

            case 'vencido':
                $totalVencidos++;
                break;
        }
    }


    /* ======================================================
    GUARDAR TODOS LOS DOCUMENTOS
    PARA MOSTRARLOS EN EL MODAL
    ====================================================== */

    foreach ($documentos as $nombreDocumento => $estado) {

        $fechaDocumento =
            $nombreDocumento === 'Licencia'
            ? ($row['licencia'] ?? '')
            : (
                $nombreDocumento === 'SOAT'
                ? ($row['fecha_soat'] ?? '')
                : ($row['revision_tecn'] ?? '')
            );


        $documentosVehiculos[] = [

            'id' =>
            (int) $row['id_registro'],

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
            $fechaDocumento
        ];
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Consulta Vehículos</title>


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- =====================================================
         DATATABLES
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">


    <style>
        * {
            box-sizing: border-box;
        }


        body {

            font-family: Arial, sans-serif;

            background:
                linear-gradient(135deg,
                    #f5f7fa 0%,
                    #e9eef5 100%);

            color: #2f3640;

            margin: 0;

            padding: 0;
        }

        /* =====================================================
   USUARIOS DE INGRESO Y SALIDA
===================================================== */

        .usuario-registro {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            max-width: 150px;

            padding: 5px 8px;

            border-radius: 6px;

            font-size: 11px;

            font-weight: 600;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;
        }


        .usuario-ingreso {

            background: #e7f1ff;

            color: #0d6efd;

            border: 1px solid #b6d4fe;
        }


        .usuario-salida {

            background: #e8f5e9;

            color: #198754;

            border: 1px solid #badbcc;
        }

        /* =====================================================
        USUARIOS QUE REGISTRAN INGRESO Y SALIDA
        ===================================================== */

        .usuario-registro {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            max-width: 150px;

            padding: 5px 8px;

            border-radius: 6px;

            font-size: 11px;

            font-weight: 600;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;
        }


        .usuario-ingreso {

            background: #e7f1ff;

            color: #0d6efd;

            border: 1px solid #b6d4fe;
        }


        .usuario-salida {

            background: #e8f5e9;

            color: #198754;

            border: 1px solid #badbcc;
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
                0 8px 25px rgba(0, 0, 0, .08);

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
                0 6px 20px rgba(0, 0, 0, .07);

            border-left: 5px solid;

            display: flex;

            align-items: center;

            gap: 15px;

            transition: .2s;

            cursor: pointer;

            user-select: none;
        }


        .resumen-card:hover {

            transform: translateY(-4px);

            box-shadow:
                0 12px 28px rgba(0, 0, 0, .13);
        }


        .resumen-card:active {

            transform: scale(.98);
        }


        .resumen-card:focus {

            outline: 3px solid rgba(39, 60, 117, .20);

            outline-offset: 2px;
        }


        .resumen-icon {

            width: 50px;

            height: 50px;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;

            flex-shrink: 0;
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
                0 8px 25px rgba(0, 0, 0, .08);

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
                    0 0 0 0 rgba(220, 53, 69, .15);
            }

            70% {

                box-shadow:
                    0 0 0 6px rgba(220, 53, 69, 0);
            }

            100% {

                box-shadow:
                    0 0 0 0 rgba(220, 53, 69, 0);
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
           MODAL
        ====================================================== */

        .modal-header {

            background: #273c75;

            color: white;

            border-bottom: none;
        }


        .modal-header .btn-close {

            filter: brightness(0) invert(1);
        }


        .modal-title {

            font-weight: 700;
        }


        .modal-body {

            padding: 20px;
        }


        .tabla-alertas {

            width: 100%;

            border-collapse: separate;

            border-spacing: 0;

            overflow: hidden;

            border-radius: 10px;

            border: 1px solid #dee2e6;
        }


        .tabla-alertas thead th {

            background: #273c75;

            color: white;

            padding: 12px 10px;

            text-align: center;

            white-space: nowrap;

            font-size: 13px;
        }


        .tabla-alertas tbody td {

            padding: 11px 10px;

            border-bottom: 1px solid #edf0f5;

            vertical-align: middle;

            text-align: center;

            font-size: 13px;
        }


        .tabla-alertas tbody tr:last-child td {

            border-bottom: none;
        }


        .tabla-alertas tbody tr:hover {

            background: #f8f9fa;
        }


        .alerta-placa {

            background: #2f3640;

            color: white;

            padding: 5px 10px;

            border-radius: 6px;

            font-weight: bold;

            letter-spacing: 1px;

            display: inline-block;
        }


        .alerta-documento {

            font-weight: 700;

            color: #273c75;
        }


        .alerta-dias {

            font-weight: 700;
        }


        .alerta-dias.vencido {

            color: #dc3545;
        }


        .alerta-dias.urgente {

            color: #fd7e14;
        }


        .alerta-dias.proximo {

            color: #997404;
        }


        .alerta-dias.vigente {

            color: #198754;
        }


        .badge-estado {

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 700;

            display: inline-block;
        }


        .badge-vencido {

            background: #f8d7da;

            color: #842029;
        }


        .badge-urgente {

            background: #ffe5d0;

            color: #b54708;
        }


        .badge-proximo {

            background: #fff3cd;

            color: #856404;
        }


        .badge-vigente {

            background: #d1e7dd;

            color: #146c43;
        }


        .badge-vence_hoy {

            background: #f8d7da;

            color: #b02a37;
        }


        .sin-resultados {

            padding: 40px 20px;

            text-align: center;

            color: #6c757d;
        }


        .sin-resultados i {

            font-size: 45px;

            margin-bottom: 12px;

            color: #adb5bd;
        }


        .sin-resultados h5 {

            color: #495057;

            font-weight: 700;
        }


        /* =====================================================
           FILA RESALTADA
        ====================================================== */

        #tablaVehiculos tbody tr.resaltado-vehiculo {

            background-color: #fff3cd !important;

            box-shadow:
                inset 0 0 0 2px #ffc107;
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


            .modal-dialog {

                margin: 10px;
            }


            .modal-body {

                padding: 10px;
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
                class="header-content d-flex justify-content-between align-items-center">

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
                        class="btn-menu">

                        <i class="fas fa-home"></i>

                        Menú

                    </a>


                    <a
                        href="registro_vehiculos_externos.php"
                        class="btn-nuevo">

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


            <!-- =================================================
         VIGENTES
    ================================================== -->

            <div
                class="resumen-card card-vigentes"
                data-estado="vigente"
                role="button"
                tabindex="0"
                title="Ver documentos vigentes">

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


            <!-- =================================================
         PRÓXIMOS
    ================================================== -->

            <div
                class="resumen-card card-proximos"
                data-estado="proximo"
                role="button"
                tabindex="0"
                title="Ver vehículos próximos a vencer">

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


            <!-- =================================================
         URGENTES
    ================================================== -->

            <div
                class="resumen-card card-urgentes"
                data-estado="urgente"
                role="button"
                tabindex="0"
                title="Ver vehículos que vencen pronto">

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


            <!-- =================================================
        VENCIDOS
        ================================================== -->

            <div
                class="resumen-card card-vencidos"
                data-estado="vencido"
                role="button"
                tabindex="0"
                title="Ver vehículos con documentos vencidos">

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
                title="Deslice para mover la tabla horizontalmente">

                <div
                    id="scrollSuperiorContenido"
                    class="top-scroll-content"></div>

            </div>


            <!-- =================================================
            TABLA CON BARRA INFERIOR
            ================================================== -->

            <div
                id="scrollInferior"
                class="table-responsive">

                <table
                    id="tablaVehiculos"
                    class="table table-striped table-hover align-middle">

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
                            <th>Registró ingreso</th>
                            <th>Salida</th>
                            <th>Registró salida</th>
                            <th>Documentación</th>
                            <th>Acción</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($rows as $row): ?>

                            <?php

                            /* =====================================================
                            ID DEL REGISTRO
                            ===================================================== */

                            $id = (int) ($row['id_registro'] ?? 0);


                            /* =====================================================
                            HORA DE SALIDA
                            ===================================================== */

                            $horaSalida = trim(
                                $row['hora_salida'] ?? ''
                            );


                            $tieneSalida =
                                $horaSalida !== '' &&
                                $horaSalida !== '00:00:00';


                            /* =====================================================
                            USUARIOS DE INGRESO Y SALIDA
                            ===================================================== */

                            $realizoIngreso = trim(
                                $row['realizo'] ?? ''
                            );


                            $realizoSalida = trim(
                                $row['realizo_salida'] ?? ''
                            );


                            /* =====================================================
                            ESTADOS DE LOS DOCUMENTOS
                            ===================================================== */

                            $estadoLicencia = estadoDocumento(
                                $row['licencia'] ?? ''
                            );


                            $estadoSoat = estadoDocumento(
                                $row['fecha_soat'] ?? ''
                            );


                            $estadoRevision = estadoDocumento(
                                $row['revision_tecn'] ?? ''
                            );

                            ?>


                            <!-- =================================================
                            FILA DEL VEHÍCULO
                            ================================================== -->

                            <tr data-id="<?= $id ?>">

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


                                <!-- TIPO DE VISITA -->

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


                                <!-- HORA DE INGRESO -->

                                <td>

                                    <?php if (
                                        !empty($row['hora_ingreso']) &&
                                        $row['hora_ingreso'] !== '00:00:00'
                                    ): ?>

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


                                <!-- USUARIO QUE REGISTRÓ EL INGRESO -->

                                <td>

                                    <?php if ($realizoIngreso !== ''): ?>

                                        <span
                                            class="usuario-registro usuario-ingreso"
                                            title="<?= htmlspecialchars(
                                                        $realizoIngreso,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            <i class="fas fa-user-shield"></i>

                                            <?= htmlspecialchars(
                                                $realizoIngreso,
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


                                <!-- HORA DE SALIDA -->

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


                                <!-- USUARIO QUE REGISTRÓ LA SALIDA -->

                                <td id="realizo_salida_<?= $id ?>">

                                    <?php if (
                                        $tieneSalida &&
                                        $realizoSalida !== ''
                                    ): ?>

                                        <span
                                            class="usuario-registro usuario-salida"
                                            title="<?= htmlspecialchars(
                                                        $realizoSalida,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            <i class="fas fa-user-check"></i>

                                            <?= htmlspecialchars(
                                                $realizoSalida,
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


                                <!-- DOCUMENTACIÓN -->

                                <td>

                                    <!-- =================================================
                     LICENCIA
                ================================================== -->

                                    <div class="documento-box <?= htmlspecialchars(
                                                                    $estadoLicencia['clase'],
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>">

                                        <strong>
                                            <i class="fas fa-id-card me-1"></i>
                                            Licencia
                                        </strong>

                                        <div class="estado-linea">

                                            <i class="fas <?= htmlspecialchars(
                                                                $estadoLicencia['icono'],
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>"></i>

                                            <?= htmlspecialchars(
                                                $estadoLicencia['texto'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

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


                                    <!-- =================================================
                                    SOAT
                                    ================================================== -->

                                    <div class="documento-box <?= htmlspecialchars(
                                                                    $estadoSoat['clase'],
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>">

                                        <strong>
                                            <i class="fas fa-car me-1"></i>
                                            SOAT
                                        </strong>

                                        <div class="estado-linea">

                                            <i class="fas <?= htmlspecialchars(
                                                                $estadoSoat['icono'],
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>"></i>

                                            <?= htmlspecialchars(
                                                $estadoSoat['texto'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

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

                                    <!-- =================================================
                                    REVISIÓN TÉCNICO-MECÁNICA
                                    ================================================== -->

                                    <div class="documento-box <?= htmlspecialchars($estadoRevision['clase'],ENT_QUOTES,'UTF-8') ?>">

                                        <strong>
                                            <i class="fas fa-screwdriver-wrench me-1"></i>
                                            Técnico-mecánica
                                        </strong>

                                        <div class="estado-linea">

                                            <i class="fas <?= htmlspecialchars(
                                                                $estadoRevision['icono'],
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>"></i>

                                            <?= htmlspecialchars(
                                                $estadoRevision['texto'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

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


                                <!-- ACCIÓN -->

                                <td id="accion_<?= $id ?>">

                                    <?php if (!$tieneSalida): ?>

                                        <button
                                            type="button"
                                            id="btnSalida_<?= $id ?>"
                                            class="btn-salida"
                                            onclick="marcarSalida(<?= $id ?>)">

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
     MODAL VEHÍCULOS / DOCUMENTOS
====================================================== -->

    <div
        class="modal fade"
        id="modalVehiculosEstado"
        tabindex="-1"
        aria-hidden="true">

        <div
            class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="tituloModalVehiculos">

                        <i class="fas fa-car me-2"></i>

                        Vehículos

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>

                </div>


                <div class="modal-body">

                    <div
                        id="contenidoVehiculosEstado"></div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        <i class="fas fa-times me-1"></i>

                        Cerrar

                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
     JQUERY
====================================================== -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    <!-- =====================================================
     BOOTSTRAP
====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!-- =====================================================
    DATATABLES
====================================================== -->

    <script
        src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script
        src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>


    <!-- =====================================================
    SWEETALERT
    ====================================================== -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        /* =========================================================
       VARIABLES PARA LAS BARRAS HORIZONTALES
    ========================================================= */

        let scrollSuperior;
        let scrollInferior;
        let scrollSuperiorContenido;


        /* =========================================================
           ESCAPAR CONTENIDO HTML
        ========================================================= */

        function escapeHtml(text) {

            if (
                text === null ||
                text === undefined
            ) {
                return '';
            }

            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }


        /* =========================================================
           CONFIGURAR BARRA HORIZONTAL
        ========================================================= */

        function configurarScrollHorizontal() {

            scrollSuperior =
                document.getElementById('scrollSuperior');

            scrollInferior =
                document.getElementById('scrollInferior');

            scrollSuperiorContenido =
                document.getElementById('scrollSuperiorContenido');


            if (
                !scrollSuperior ||
                !scrollInferior ||
                !scrollSuperiorContenido
            ) {
                return;
            }


            const tabla =
                document.getElementById('tablaVehiculos');


            if (!tabla) {
                return;
            }


            const anchoTabla =
                tabla.scrollWidth;


            scrollSuperiorContenido.style.width =
                anchoTabla + 'px';


            /* =====================================================
               BARRA SUPERIOR HACIA BARRA INFERIOR
            ===================================================== */

            scrollSuperior.onscroll = function() {

                scrollInferior.scrollLeft =
                    scrollSuperior.scrollLeft;

            };


            /* =====================================================
               BARRA INFERIOR HACIA BARRA SUPERIOR
            ===================================================== */

            scrollInferior.onscroll = function() {

                scrollSuperior.scrollLeft =
                    scrollInferior.scrollLeft;

            };
        }


        /* =========================================================
           DATOS DE DOCUMENTOS PARA JAVASCRIPT
        ========================================================= */

        const documentosVehiculos =
            <?= json_encode(
                $documentosVehiculos,
                JSON_UNESCAPED_UNICODE |
                    JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT |
                    JSON_HEX_AMP
            ) ?>;


        /* =========================================================
           MOSTRAR VEHÍCULOS SEGÚN EL ESTADO DEL DOCUMENTO
        ========================================================= */

        function mostrarVehiculosPorEstado(estado) {

            console.log(
                'Mostrando documentos del estado:',
                estado
            );


            let documentos = [];


            /* =====================================================
               LOS URGENTES INCLUYEN LOS QUE VENCEN HOY
            ===================================================== */

            if (estado === 'urgente') {

                documentos = documentosVehiculos.filter(
                    function(item) {

                        return (
                            item.estado === 'urgente' ||
                            item.estado === 'vence_hoy'
                        );

                    }
                );

            } else {

                documentos = documentosVehiculos.filter(
                    function(item) {

                        return item.estado === estado;

                    }
                );
            }


            /* =====================================================
               CONFIGURAR TÍTULO E ICONO DEL MODAL
            ===================================================== */

            let titulo = 'Documentos';
            let icono = 'fa-file';


            switch (estado) {

                case 'vigente':

                    titulo =
                        'Documentos vigentes';

                    icono =
                        'fa-circle-check';

                    break;


                case 'proximo':

                    titulo =
                        'Documentos próximos a vencer';

                    icono =
                        'fa-clock';

                    break;


                case 'urgente':

                    titulo =
                        'Documentos que vencen pronto';

                    icono =
                        'fa-triangle-exclamation';

                    break;


                case 'vencido':

                    titulo =
                        'Documentos vencidos';

                    icono =
                        'fa-circle-xmark';

                    break;
            }


            $('#tituloModalVehiculos').html(

                '<i class="fas ' +
                icono +
                ' me-2"></i>' +

                escapeHtml(titulo)

            );


            let html = '';


            /* =====================================================
               NO HAY RESULTADOS
            ===================================================== */

            if (documentos.length === 0) {

                html =

                    '<div class="sin-resultados">' +

                    '<i class="fas fa-car"></i>' +

                    '<h5>No hay vehículos para mostrar</h5>' +

                    '<p class="mb-0">' +
                    'No existen documentos en este estado.' +
                    '</p>' +

                    '</div>';

            } else {

                /* =================================================
                   CREAR TABLA DEL MODAL
                ================================================= */

                html =

                    '<div class="table-responsive">' +

                    '<table class="tabla-alertas">' +

                    '<thead>' +

                    '<tr>' +

                    '<th>Placa</th>' +
                    '<th>Nombre</th>' +
                    '<th>Documento</th>' +
                    '<th>Fecha vencimiento</th>' +
                    '<th>Estado</th>' +
                    '<th>Días</th>' +
                    '<th>Acción</th>' +

                    '</tr>' +

                    '</thead>' +

                    '<tbody>';


                documentos.forEach(
                    function(item) {

                        /* =========================================
                           FECHA DEL DOCUMENTO
                        ========================================= */

                        const fecha =
                            item.fecha || 'Sin fecha';


                        /* =========================================
                           TEXTO DE LOS DÍAS
                        ========================================= */

                        let diasTexto = '—';


                        if (item.dias !== null) {

                            if (item.estado === 'vencido') {

                                diasTexto =
                                    'Vencido hace ' +
                                    Math.abs(item.dias) +
                                    ' día(s)';

                            } else if (
                                item.estado === 'vence_hoy' ||
                                Number(item.dias) === 0
                            ) {

                                diasTexto =
                                    'Vence hoy';

                            } else {

                                diasTexto =
                                    item.dias +
                                    ' día(s) restantes';
                            }
                        }


                        const estadoTexto =
                            item.texto || 'Sin estado';


                        const estadoClase =
                            item.estado || 'sin_fecha';


                        /* =========================================
                           CREAR FILA
                        ========================================= */

                        html +=

                            '<tr>' +

                            '<td>' +

                            '<span class="alerta-placa">' +

                            escapeHtml(
                                item.placa
                            ) +

                            '</span>' +

                            '</td>' +


                            '<td>' +

                            escapeHtml(
                                item.nombre
                            ) +

                            '</td>' +


                            '<td>' +

                            '<span class="alerta-documento">' +

                            escapeHtml(
                                item.documento
                            ) +

                            '</span>' +

                            '</td>' +


                            '<td>' +

                            escapeHtml(
                                fecha
                            ) +

                            '</td>' +


                            '<td>' +

                            '<span class="badge-estado badge-' +
                            escapeHtml(estadoClase) +
                            '">' +

                            escapeHtml(
                                estadoTexto
                            ) +

                            '</span>' +

                            '</td>' +


                            '<td>' +

                            '<span class="alerta-dias ' +
                            escapeHtml(estadoClase) +
                            '">' +

                            escapeHtml(
                                diasTexto
                            ) +

                            '</span>' +

                            '</td>' +


                            '<td>' +

                            '<button ' +
                            'type="button" ' +
                            'class="btn btn-sm btn-primary" ' +
                            'onclick="irAlVehiculo(' +
                            Number(item.id) +
                            ')">' +

                            '<i class="fas fa-eye me-1"></i>' +

                            'Ver' +

                            '</button>' +

                            '</td>' +

                            '</tr>';
                    }
                );


                html +=

                    '</tbody>' +

                    '</table>' +

                    '</div>';
            }


            /* =====================================================
               INSERTAR CONTENIDO EN EL MODAL
            ===================================================== */

            $('#contenidoVehiculosEstado').html(
                html
            );


            /* =====================================================
               ABRIR MODAL
            ===================================================== */

            const modalElement =
                document.getElementById(
                    'modalVehiculosEstado'
                );


            if (!modalElement) {

                console.error(
                    'No existe el modal #modalVehiculosEstado'
                );

                return;
            }


            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );


            modal.show();
        }


        /* =========================================================
           IR AL VEHÍCULO DESDE EL MODAL
        ========================================================= */

        function irAlVehiculo(id) {

            const idVehiculo =
                Number(id);


            if (
                !Number.isInteger(idVehiculo) ||
                idVehiculo <= 0
            ) {

                console.error(
                    'El ID del vehículo no es válido:',
                    id
                );

                return;
            }


            /* =====================================================
               CERRAR MODAL
            ===================================================== */

            const modalElement =
                document.getElementById(
                    'modalVehiculosEstado'
                );


            if (modalElement) {

                const modal =
                    bootstrap.Modal.getInstance(
                        modalElement
                    );


                if (modal) {

                    modal.hide();

                }
            }


            /* =====================================================
               ESPERAR A QUE TERMINE DE CERRAR
            ===================================================== */

            setTimeout(
                function() {

                    if (
                        !$.fn.DataTable.isDataTable(
                            '#tablaVehiculos'
                        )
                    ) {

                        console.error(
                            'La tabla de vehículos no está inicializada.'
                        );

                        return;
                    }


                    const tabla =
                        $('#tablaVehiculos').DataTable();


                    /* =============================================
                       LIMPIAR BÚSQUEDAS ANTERIORES
                    ============================================= */

                    tabla
                        .search('')
                        .columns()
                        .search('');


                    /* =============================================
                       BUSCAR EL ID EXACTO
                    ============================================= */

                    tabla
                        .column(0)
                        .search(
                            '^' + idVehiculo + '$',
                            true,
                            false
                        )
                        .draw();


                    /* =============================================
                       RESALTAR LA FILA ENCONTRADA
                    ============================================= */

                    setTimeout(
                        function() {

                            const fila =
                                $('#tablaVehiculos tbody tr')
                                .filter(
                                    function() {

                                        return $(this)
                                            .find('td')
                                            .first()
                                            .text()
                                            .trim() ===
                                            String(idVehiculo);
                                    }
                                );


                            if (!fila.length) {

                                console.warn(
                                    'No se encontró la fila con ID:',
                                    idVehiculo
                                );

                                return;
                            }


                            fila.addClass(
                                'resaltado-vehiculo'
                            );


                            fila[0].scrollIntoView({

                                behavior: 'smooth',

                                block: 'center',

                                inline: 'nearest'

                            });


                            setTimeout(
                                function() {

                                    fila.removeClass(
                                        'resaltado-vehiculo'
                                    );

                                },
                                4000
                            );

                        },
                        250
                    );

                },
                400
            );
        }


        /* =========================================================
           RESTAURAR BOTÓN DE SALIDA
        ========================================================= */

        function restaurarBotonSalida(id) {

            $('#btnSalida_' + id)

                .prop(
                    'disabled',
                    false
                )

                .html(

                    '<i class="fas fa-sign-out-alt"></i> ' +

                    'Salida'

                );
        }


        /* =========================================================
           REGISTRAR SALIDA
        ========================================================= */

        function marcarSalida(id) {

            const idVehiculo =
                Number(id);


            if (
                !Number.isInteger(idVehiculo) ||
                idVehiculo <= 0
            ) {

                Swal.fire({

                    title: 'ID no válido',

                    text: 'No fue posible identificar el registro del vehículo.',

                    icon: 'error',

                    confirmButtonColor: '#dc3545'

                });

                return;
            }


            Swal.fire({

                title: '¿Registrar salida?',

                text: 'Se registrará la hora actual y el usuario que realiza la salida.',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Sí, registrar',

                cancelButtonText: 'Cancelar',

                confirmButtonColor: '#dc3545',

                cancelButtonColor: '#6c757d',

                reverseButtons: true

            }).then(
                function(result) {

                    if (!result.isConfirmed) {

                        return;

                    }


                    /* =============================================
                       DESACTIVAR BOTÓN
                    ============================================= */

                    const $boton =
                        $('#btnSalida_' + idVehiculo);


                    $boton

                        .prop(
                            'disabled',
                            true
                        )

                        .html(

                            '<i class="fas fa-spinner fa-spin"></i> ' +

                            'Registrando...'

                        );


                    /* =============================================
                       PETICIÓN AJAX
                    ============================================= */

                    $.ajax({

                        url: '../Controller/salida_vehiculo.php',

                        type: 'POST',

                        data: {

                            id: idVehiculo

                        },

                        dataType: 'json',

                        timeout: 15000,


                        /* =========================================
                           RESPUESTA EXITOSA DEL SERVIDOR
                        ========================================= */

                        success: function(response) {

                            console.log(
                                'Respuesta salida vehículo:',
                                response
                            );


                            if (
                                !response ||
                                response.ok !== true
                            ) {

                                restaurarBotonSalida(
                                    idVehiculo
                                );


                                Swal.fire({

                                    title: 'No se pudo registrar',

                                    text: response &&
                                        response.mensaje ?
                                        response.mensaje : 'No se pudo registrar la salida.',

                                    icon: 'warning',

                                    confirmButtonColor: '#ffc107',

                                    confirmButtonText: 'Aceptar'

                                });

                                return;
                            }


                            const horaSalida =
                                response.hora_salida || '';


                            const realizoSalida =
                                response.realizo_salida || '';


                            /* =====================================
                               VALIDAR RESPUESTA
                            ===================================== */

                            if (
                                horaSalida === '' ||
                                realizoSalida === ''
                            ) {

                                console.warn(
                                    'La respuesta no contiene todos los datos:',
                                    response
                                );
                            }


                            /* =====================================
                               ACTUALIZAR HORA DE SALIDA
                            ===================================== */

                            $('#salida_' + idVehiculo).html(

                                '<span class="hora-salida">' +

                                escapeHtml(
                                    horaSalida || 'Registrada'
                                ) +

                                '</span>'

                            );


                            /* =====================================
                               ACTUALIZAR USUARIO DE SALIDA
                            ===================================== */

                            $('#realizo_salida_' + idVehiculo).html(

                                '<span ' +

                                'class="usuario-registro usuario-salida" ' +

                                'title="' +
                                escapeHtml(
                                    realizoSalida
                                ) +
                                '">' +

                                '<i class="fas fa-user-check"></i> ' +

                                escapeHtml(
                                    realizoSalida || 'Sin usuario'
                                ) +

                                '</span>'

                            );


                            /* =====================================
                               ACTUALIZAR COLUMNA DE ACCIÓN
                            ===================================== */

                            $('#accion_' + idVehiculo).html(

                                '<span class="finalizado">' +

                                '<i class="fas fa-check"></i> ' +

                                'Finalizado' +

                                '</span>'

                            );


                            /* =====================================
                               ACTUALIZAR DATATABLE INTERNAMENTE
                            ===================================== */

                            if (
                                $.fn.DataTable.isDataTable(
                                    '#tablaVehiculos'
                                )
                            ) {

                                const tabla =
                                    $('#tablaVehiculos').DataTable();


                                const fila =
                                    $('#tablaVehiculos tbody tr[data-id="' +
                                        idVehiculo +
                                        '"]');


                                if (fila.length) {

                                    tabla
                                        .row(fila)
                                        .invalidate('dom');
                                }
                            }


                            /* =====================================
                               REAJUSTAR BARRA HORIZONTAL
                            ===================================== */

                            setTimeout(
                                configurarScrollHorizontal,
                                100
                            );


                            /* =====================================
                               MENSAJE DE ÉXITO
                            ===================================== */

                            Swal.fire({

                                title: '¡Salida registrada!',

                                html:

                                    '<div class="text-start">' +

                                    '<p class="mb-2">' +

                                    '<strong>Hora de salida:</strong> ' +

                                    escapeHtml(
                                        horaSalida
                                    ) +

                                    '</p>' +

                                    '<p class="mb-0">' +

                                    '<strong>Registró la salida:</strong> ' +

                                    escapeHtml(
                                        realizoSalida
                                    ) +

                                    '</p>' +

                                    '</div>',

                                icon: 'success',

                                confirmButtonColor: '#198754',

                                confirmButtonText: 'Aceptar',

                                timer: 3000,

                                timerProgressBar: true

                            });
                        },


                        /* =========================================
                           ERROR AJAX
                        ========================================= */

                        error: function(
                            xhr,
                            status,
                            error
                        ) {

                            console.error(
                                '========== ERROR AJAX =========='
                            );

                            console.error(
                                'HTTP:',
                                xhr.status
                            );

                            console.error(
                                'Estado:',
                                status
                            );

                            console.error(
                                'Error:',
                                error
                            );

                            console.error(
                                'Respuesta:',
                                xhr.responseText
                            );

                            console.error(
                                '================================'
                            );


                            restaurarBotonSalida(
                                idVehiculo
                            );


                            /* =====================================
                               OBTENER MENSAJE JSON DEL CONTROLLER
                            ===================================== */

                            let mensaje =
                                'No fue posible registrar la salida.';


                            if (status === 'timeout') {

                                mensaje =
                                    'La solicitud tardó demasiado tiempo. Inténtelo nuevamente.';

                            } else if (
                                xhr.responseJSON &&
                                xhr.responseJSON.mensaje
                            ) {

                                mensaje =
                                    xhr.responseJSON.mensaje;

                            } else {

                                try {

                                    const respuesta =
                                        JSON.parse(
                                            xhr.responseText
                                        );


                                    if (
                                        respuesta &&
                                        respuesta.mensaje
                                    ) {

                                        mensaje =
                                            respuesta.mensaje;

                                    }

                                } catch (e) {

                                    console.warn(
                                        'La respuesta no es un JSON válido.'
                                    );
                                }
                            }


                            Swal.fire({

                                title: 'Error al registrar la salida',

                                text: mensaje,

                                icon: 'error',

                                confirmButtonColor: '#dc3545',

                                confirmButtonText: 'Aceptar'

                            });
                        }
                    });
                }
            );
        }


        /* =========================================================
           DOCUMENT READY
        ========================================================= */

        $(document).ready(function() {

            console.log(
                'JavaScript de vehículos cargado correctamente.'
            );


            /* =====================================================
               INICIALIZAR DATATABLE
            ===================================================== */

            const tabla =
                $('#tablaVehiculos').DataTable({

                    pageLength: 10,

                    lengthMenu: [
                        [10, 15, 25, 50, 100, -1],
                        [10, 15, 25, 50, 100, 'Todos']
                    ],

                    autoWidth: false,

                    scrollX: false,

                    order: [
                        [0, 'desc']
                    ],

                    columnDefs: [

                        {
                            targets: 0,
                            type: 'num'
                        },

                        {
                            targets: -1,
                            orderable: false,
                            searchable: false
                        }

                    ],

                    language: {

                        search: 'Buscar:',

                        lengthMenu: 'Mostrar _MENU_ registros',

                        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',

                        infoEmpty: 'No hay registros disponibles',

                        infoFiltered: '(filtrado de _MAX_ registros)',

                        zeroRecords: 'No se encontraron registros',

                        emptyTable: 'No hay vehículos registrados',

                        loadingRecords: 'Cargando registros...',

                        processing: 'Procesando...',

                        paginate: {

                            first: 'Primero',

                            last: 'Último',

                            next: 'Siguiente',

                            previous: 'Anterior'
                        }
                    }
                });


            /* =====================================================
               CONFIGURAR SCROLL INICIAL
            ===================================================== */

            setTimeout(
                configurarScrollHorizontal,
                200
            );


            /* =====================================================
               REAJUSTAR AL REDIBUJAR DATATABLE
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
               REAJUSTAR AL CAMBIAR EL TAMAÑO DE LA VENTANA
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
               EVENTO DE LAS TARJETAS DE RESUMEN
            ===================================================== */

            $('.resumen-card').on(
                'click',
                function(event) {

                    event.preventDefault();

                    event.stopPropagation();


                    const estado =
                        $(this).data('estado');


                    if (!estado) {

                        console.error(
                            'La tarjeta no tiene el atributo data-estado.'
                        );

                        return;
                    }


                    mostrarVehiculosPorEstado(
                        estado
                    );
                }
            );


            /* =====================================================
               ACCESIBILIDAD CON ENTER Y ESPACIO
            ===================================================== */

            $('.resumen-card').on(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Enter' ||
                        event.key === ' '
                    ) {

                        event.preventDefault();

                        $(this).trigger(
                            'click'
                        );
                    }
                }
            );
        });
    </script>

</body>

</html>