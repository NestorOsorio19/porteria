<?php

/* ==========================================================
   CONEXIÓN PDO
========================================================== */

require_once '../Config/database.php';

$con = connection();


/* ==========================================================
   CONSULTAR COLABORADORES
========================================================== */

try {

    $sql = "
        SELECT
            c.*,
            a.nom_arl,
            e.nom_eps,
            ar.nom_area
        FROM colaboradores c

        LEFT JOIN arls a
            ON c.id_arl = a.id_arl

        LEFT JOIN eps e
            ON c.id_eps = e.id_eps

        LEFT JOIN areas ar
            ON c.id_area = ar.id_area

        ORDER BY c.id_registro DESC
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute();
} catch (PDOException $e) {

    die("Error al consultar los colaboradores: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        ));
}


/* ==========================================================
   FUNCIÓN DE SEGURIDAD
========================================================== */

function e($valor): string
{
    return htmlspecialchars(
        (string)($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

?>


<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Consulta de registros de colaboradores">

    <title>Consulta Colaboradores</title>


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


    <!-- =====================================================
         ESTILOS
    ====================================================== -->

    <style>
        /* =====================================================
           GENERAL
        ====================================================== */

        body {

            font-family: Arial, sans-serif;

            background: #f5f6fa;

            color: #2f3640;

            margin: 0;

            padding: 0;

        }


        /* =====================================================
           CONTENEDOR
        ====================================================== */

        .users-table {

            max-width: 1750px;

            margin: 15px auto;

            padding: 10px 15px;

        }


        /* =====================================================
           ENCABEZADO
        ====================================================== */

        .card-header-custom {

            background: #ffffff;

            border-radius: 10px;

            padding: 14px 18px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.07);

            margin-bottom: 12px;

        }


        .card-header-custom h2 {

            margin: 0;

            color: #192a56;

            font-size: 20px;

            font-weight: 700;

        }


        .card-header-custom small {

            color: #718093;

            font-size: 12px;

        }


        .header-buttons {

            display: flex;

            gap: 7px;

            align-items: center;

        }


        /* =====================================================
           BOTONES
        ====================================================== */

        .btn-nuevo,
        .btn-menu {

            color: white;

            border: none;

            padding: 7px 12px;

            border-radius: 6px;

            text-decoration: none;

            font-size: 12px;

            font-weight: 600;

            transition: 0.2s;

            white-space: nowrap;

            display: inline-flex;

            align-items: center;

            gap: 5px;

        }


        .btn-nuevo {

            background: #273c75;

        }


        .btn-nuevo:hover {

            background: #192a56;

            color: white;

        }


        .btn-menu {

            background: #718093;

        }


        .btn-menu:hover {

            background: #576574;

            color: white;

        }


        /* =====================================================
           TABLA
        ====================================================== */

        .table-container {

            background: #fff;

            padding: 10px;

            border-radius: 10px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.07);

        }


        table.dataTable {

            width: 100% !important;

            font-size: 12px;

            margin-top: 0 !important;

        }


        table.dataTable thead th {

            background-color: #273c75 !important;

            color: #fff !important;

            font-weight: 600;

            text-align: center;

            vertical-align: middle;

            white-space: nowrap;

            padding: 7px 6px !important;

            border: none;

        }


        table.dataTable tbody td {

            text-align: center;

            vertical-align: middle;

            white-space: nowrap;

            padding: 5px 6px !important;

        }


        table.dataTable tbody tr:hover {

            background-color: #f1f2f6 !important;

        }


        /* =====================================================
           ID
        ====================================================== */

        .id-registro {

            color: #6c757d;

            font-size: 11px;

            font-weight: 600;

        }


        /* =====================================================
           COLABORADOR
        ====================================================== */

        .colaborador {

            display: flex;

            flex-direction: column;

            align-items: flex-start;

            line-height: 1.15;

            min-width: 150px;

        }


        .colaborador strong {

            color: #192a56;

            font-size: 12px;

            font-weight: 700;

        }


        .colaborador small {

            color: #718093;

            font-size: 10px;

        }


        /* =====================================================
           EQUIPO
        ====================================================== */

        .equipo {

            display: flex;

            flex-direction: column;

            align-items: flex-start;

            line-height: 1.15;

        }


        .equipo strong {

            font-size: 11px;

            color: #2f3640;

        }


        .equipo small {

            font-size: 10px;

            color: #718093;

        }


        /* =====================================================
           ÁREA
        ====================================================== */

        .area {

            font-size: 11px;

            font-weight: 600;

            color: #495057;

        }


        /* =====================================================
           HORAS
        ====================================================== */

        .hora-ingreso,
        .hora-salida,
        .pendiente {

            display: inline-block;

            padding: 3px 6px;

            border-radius: 4px;

            font-size: 10px;

            font-weight: 700;

        }


        .hora-ingreso {

            background: #d4edda;

            color: #155724;

        }


        .hora-salida {

            background: #d1ecf1;

            color: #0c5460;

        }


        .pendiente {

            background: #fff3cd;

            color: #856404;

        }


        /* =====================================================
           USUARIO
        ====================================================== */

        .usuario-registro,
        .usuario-salida {

            display: inline-flex;

            align-items: center;

            gap: 4px;

            max-width: 120px;

            padding: 3px 6px;

            border-radius: 4px;

            font-size: 10px;

            font-weight: 600;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }


        .usuario-registro {

            background: #e9ecef;

            color: #495057;

        }


        .usuario-salida {

            background: #e8f5e9;

            color: #198754;

        }


        /* =====================================================
           BOTÓN SALIDA
        ====================================================== */

        .btnSalida {

            background: #e84118;

            color: white;

            border: none;

            padding: 4px 8px;

            border-radius: 5px;

            font-size: 10px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;

            white-space: nowrap;

        }


        .btnSalida:hover {

            background: #c23616;

            transform: translateY(-1px);

        }


        .btnSalida:disabled {

            opacity: 0.7;

            cursor: not-allowed;

            transform: none;

        }


        /* =====================================================
           FINALIZADO
        ====================================================== */

        .finalizado {

            display: inline-flex;

            align-items: center;

            gap: 3px;

            background: #d4edda;

            color: #155724;

            padding: 3px 6px;

            border-radius: 4px;

            font-size: 10px;

            font-weight: 700;

        }


        /* =====================================================
           BOTÓN DETALLES
        ====================================================== */

        .btnDetalles {

            background: #6c757d;

            color: white;

            border: none;

            padding: 4px 7px;

            border-radius: 5px;

            font-size: 10px;

            cursor: pointer;

            margin-left: 3px;

        }


        .btnDetalles:hover {

            background: #495057;

        }


        /* =====================================================
           DATATABLES
        ====================================================== */

        .dataTables_wrapper {

            font-size: 12px;

        }


        .dataTables_wrapper .dataTables_filter {

            margin-bottom: 8px;

        }


        .dataTables_wrapper .dataTables_filter input {

            border: 1px solid #ced4da;

            border-radius: 5px;

            padding: 4px 8px;

            margin-left: 5px;

            font-size: 12px;

        }


        .dataTables_wrapper .dataTables_length select {

            border: 1px solid #ced4da;

            border-radius: 5px;

            padding: 3px;

            font-size: 12px;

        }


        .dataTables_wrapper .dataTables_info {

            font-size: 11px;

        }


        .dataTables_wrapper .dataTables_paginate {

            font-size: 11px;

        }


        /* =====================================================
           MODAL DETALLES
        ====================================================== */

        .detalle-label {

            color: #718093;

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            margin-bottom: 2px;

        }


        .detalle-valor {

            font-size: 12px;

            font-weight: 600;

            color: #2f3640;

            margin-bottom: 10px;

        }


        .detalle-seccion {

            background: #f8f9fa;

            border-radius: 7px;

            padding: 10px;

            margin-bottom: 10px;

        }


        .detalle-seccion h6 {

            color: #273c75;

            font-size: 12px;

            font-weight: 700;

            margin-bottom: 8px;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media screen and (max-width: 768px) {

            .users-table {

                margin: 5px auto;

                padding: 5px;

            }


            .card-header-custom {

                padding: 10px;

            }


            .card-header-custom h2 {

                font-size: 16px;

            }


            .header-content {

                flex-direction: column;

                align-items: flex-start !important;

                gap: 10px;

            }


            .header-buttons {

                width: 100%;

            }


            .btn-nuevo,
            .btn-menu {

                flex: 1;

                justify-content: center;

            }


            .table-container {

                padding: 5px;

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

            <div class="header-content d-flex justify-content-between align-items-center">


                <div>

                    <h2>

                        <i class="fas fa-users text-danger"></i>

                        Registro General de Colaboradores

                    </h2>


                    <small>

                        Consulta y control de ingreso y salida

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
                        href="registro_colaboradores.php"
                        class="btn-nuevo">

                        <i class="fas fa-plus"></i>

                        Nuevo Registro

                    </a>


                </div>

            </div>

        </div>



        <!-- =====================================================
         TABLA
    ====================================================== -->

        <div class="table-container">

            <div class="table-responsive">


                <table
                    id="tablaColaboradores"
                    class="table table-striped table-hover align-middle">


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Fecha</th>

                            <th>Colaborador</th>

                            <th>Área</th>

                            <th>Equipo</th>

                            <th>Ingreso</th>

                            <th>Registró ingreso</th>

                            <th>Salida</th>

                            <th>Registró salida</th>

                            <th>Acción</th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php while (
                            $row = $stmt->fetch(PDO::FETCH_ASSOC)
                        ): ?>


                            <?php

                            $id = (int)($row['id_registro'] ?? 0);


                            $horaSalida = trim(
                                $row['salida'] ?? ''
                            );


                            $tieneSalida =
                                $horaSalida !== '' &&
                                $horaSalida !== '00:00:00';


                            $realizo = trim(
                                $row['realizo'] ?? ''
                            );


                            $realizoSalida = trim(
                                $row['realizo_salida'] ?? ''
                            );

                            ?>


                            <tr>


                                <!-- =================================================
                             ID
                        ================================================== -->

                                <td>

                                    <span class="id-registro">

                                        #<?= $id ?>

                                    </span>

                                </td>


                                <!-- =================================================
                             FECHA
                        ================================================== -->

                                <td>

                                    <?= e(
                                        $row['fecha'] ?? ''
                                    ) ?>

                                </td>


                                <!-- =================================================
                             COLABORADOR
                        ================================================== -->

                                <td>

                                    <div class="colaborador">

                                        <strong>

                                            <?= e(
                                                $row['nombre'] ?? ''
                                            ) ?>

                                        </strong>

                                        <small>

                                            CC:
                                            <?= e(
                                                $row['cedula'] ?? ''
                                            ) ?>

                                            <?php if (!empty($row['telefono'])): ?>

                                                ·
                                                <?= e(
                                                    $row['telefono']
                                                ) ?>

                                            <?php endif; ?>

                                        </small>

                                    </div>

                                </td>


                                <!-- =================================================
                             ÁREA
                        ================================================== -->

                                <td>

                                    <span class="area">

                                        <?= e(
                                            $row['nom_area'] ?? 'Sin área'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- =================================================
                             EQUIPO
                        ================================================== -->

                                <td>

                                    <div class="equipo">

                                        <strong>

                                            <?= e(
                                                $row['marca'] ?? 'Sin equipo'
                                            ) ?>

                                        </strong>

                                        <small>

                                            <?= e(
                                                $row['serial'] ?? 'Sin serial'
                                            ) ?>

                                        </small>

                                    </div>

                                </td>


                                <!-- =================================================
                             INGRESO
                        ================================================== -->

                                <td>

                                    <?php if (!empty($row['ingreso'])): ?>

                                        <span class="hora-ingreso">

                                            <?= e(
                                                $row['ingreso']
                                            ) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            —

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================================
                             USUARIO QUE REGISTRÓ INGRESO
                        ================================================== -->

                                <td>

                                    <?php if ($realizo !== ''): ?>

                                        <span
                                            class="usuario-registro"
                                            title="<?= e($realizo) ?>">

                                            <i class="fas fa-user"></i>

                                            <?= e($realizo) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            —

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================================
                             SALIDA
                        ================================================== -->

                                <td id="salida_<?= $id ?>">

                                    <?php if (!$tieneSalida): ?>

                                        <span class="pendiente">

                                            Pendiente

                                        </span>

                                    <?php else: ?>

                                        <span class="hora-salida">

                                            <?= e(
                                                $horaSalida
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================================
                             USUARIO QUE REGISTRÓ SALIDA
                        ================================================== -->

                                <td id="realizo_salida_<?= $id ?>">

                                    <?php if (
                                        $tieneSalida &&
                                        $realizoSalida !== ''
                                    ): ?>

                                        <span
                                            class="usuario-salida"
                                            title="<?= e($realizoSalida) ?>">

                                            <i class="fas fa-user-check"></i>

                                            <?= e($realizoSalida) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            —

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================================
                             ACCIÓN
                        ================================================== -->

                                <td id="accion_<?= $id ?>">

                                    <?php if (!$tieneSalida): ?>

                                        <button
                                            type="button"
                                            id="btnSalida_<?= $id ?>"
                                            class="btnSalida"
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


                        <?php endwhile; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- =====================================================
     MODAL DETALLES
====================================================== -->

    <div
        class="modal fade"
        id="modalDetalles"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="fas fa-user-circle text-primary"></i>

                        Detalles del colaborador

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div id="contenidoDetalles"></div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">

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
        src="https://code.jquery.com/jquery-3.7.1.min.js">
    </script>


    <!-- =====================================================
     BOOTSTRAP
====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =====================================================
     DATATABLES
====================================================== -->

    <script
        src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js">
    </script>

    <script
        src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js">
    </script>


    <!-- =====================================================
     SWEETALERT
====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>



    <script>
        /* =========================================================
   DATATABLE
========================================================= */
        $(document).ready(function() {

            $('#tablaColaboradores').DataTable({

                pageLength: 15,

                lengthMenu: [
                    [10, 15, 25, 50, 100, -1],
                    [10, 15, 25, 50, 100, "Todos"]
                ],

                /*
                 * ID = columna 0
                 * Se fuerza como número
                 */
                columnDefs: [{
                    targets: 0,
                    type: 'num'
                }],

                /*
                 * Ordenar por ID de mayor a menor
                 */
                order: [
                    [0, 'desc']
                ],

                autoWidth: false,

                language: {

                    search: "Buscar:",

                    lengthMenu: "Mostrar _MENU_ registros",

                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",

                    infoEmpty: "No hay registros disponibles",

                    infoFiltered: "(filtrado de _MAX_ registros)",

                    zeroRecords: "No se encontraron registros",

                    emptyTable: "No hay colaboradores registrados",

                    paginate: {

                        first: "Primero",

                        last: "Último",

                        next: "Siguiente",

                        previous: "Anterior"

                    }

                }

            });

        });

        /* =========================================================
           REGISTRAR SALIDA
        ========================================================= */

        function marcarSalida(id) {


            Swal.fire({

                title: '¿Registrar salida?',

                text: 'Se registrará la hora actual y el usuario que realiza la salida.',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: 'Sí, registrar',

                cancelButtonText: 'Cancelar',

                confirmButtonColor: '#dc3545',

                cancelButtonColor: '#6c757d',

                reverseButtons: true

            }).then(function(result) {


                if (!result.isConfirmed) {

                    return;

                }


                /* =================================================
                   BOTÓN
                ================================================== */

                const $boton =
                    $('#btnSalida_' + id);


                $boton

                    .prop('disabled', true)

                    .html(
                        '<i class="fas fa-spinner fa-spin"></i>'
                    );


                /* =================================================
                   AJAX
                ================================================== */

                $.ajax({

                    url: '../Controller/salidas_colaboradores.php',

                    type: 'POST',

                    data: {

                        id_registro: id

                    },

                    dataType: 'json',


                    /* =============================================
                       RESPUESTA CORRECTA
                    ============================================== */

                    success: function(response) {


                        console.log(
                            'RESPUESTA PHP:',
                            response
                        );


                        if (response.ok === true) {


                            const horaSalida =
                                response.hora_salida || '';


                            const realizoSalida =
                                response.realizo_salida || '';


                            /* =====================================
                               ACTUALIZAR SALIDA
                            ====================================== */

                            $('#salida_' + id).html(

                                '<span class="hora-salida">' +

                                $('<div>')
                                .text(horaSalida)
                                .html() +

                                '</span>'

                            );


                            /* =====================================
                               ACTUALIZAR USUARIO SALIDA
                            ====================================== */

                            $('#realizo_salida_' + id).html(

                                '<span ' +

                                'class="usuario-salida" ' +

                                'title="' +
                                $('<div>')
                                .text(realizoSalida)
                                .html() +
                                '">' +

                                '<i class="fas fa-user-check"></i> ' +

                                $('<div>')
                                .text(realizoSalida)
                                .html() +

                                '</span>'

                            );


                            /* =====================================
                               ACTUALIZAR ACCIÓN
                            ====================================== */

                            $('#accion_' + id).html(

                                '<span class="finalizado">' +

                                '<i class="fas fa-check"></i> ' +

                                'Finalizado' +

                                '</span>'

                            );


                            /* =====================================
                               MENSAJE
                            ====================================== */

                            Swal.fire({

                                title: 'Salida registrada',

                                html: 'Hora: <b>' +
                                    $('<div>')
                                    .text(horaSalida)
                                    .html() +
                                    '</b><br>' +

                                    'Registró: <b>' +
                                    $('<div>')
                                    .text(realizoSalida)
                                    .html() +
                                    '</b>',

                                icon: 'success',

                                confirmButtonColor: '#198754',

                                confirmButtonText: 'Aceptar',

                                timer: 2500,

                                timerProgressBar: true

                            });


                        } else {


                            /* =====================================
                               RESTAURAR BOTÓN
                            ====================================== */

                            $boton

                                .prop(
                                    'disabled',
                                    false
                                )

                                .html(

                                    '<i class="fas fa-sign-out-alt"></i> ' +

                                    'Salida'

                                );


                            /* =====================================
                               MENSAJE
                            ====================================== */

                            Swal.fire({

                                title: 'Aviso',

                                text: response.mensaje ||
                                    'No se pudo registrar la salida.',

                                icon: 'warning',

                                confirmButtonColor: '#ffc107'

                            });

                        }

                    },


                    /* =============================================
                       ERROR AJAX
                    ============================================== */

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


                        /* =========================================
                           RESTAURAR BOTÓN
                        ========================================== */

                        $boton

                            .prop(
                                'disabled',
                                false
                            )

                            .html(

                                '<i class="fas fa-sign-out-alt"></i> ' +

                                'Salida'

                            );


                        /* =========================================
                           MENSAJE
                        ========================================== */

                        Swal.fire({

                            title: 'Error de servidor',

                            html:

                                '<b>Código HTTP:</b> ' +
                                xhr.status +

                                '<br><br>' +

                                '<b>Respuesta del servidor:</b>' +

                                '<pre style="' +

                                'text-align:left;' +
                                'white-space:pre-wrap;' +
                                'max-height:300px;' +
                                'overflow:auto;' +
                                'background:#f8f9fa;' +
                                'padding:10px;' +
                                'border-radius:6px;' +

                                '">' +

                                $('<div>')
                                .text(
                                    xhr.responseText
                                )
                                .html() +

                                '</pre>',

                            icon: 'error',

                            confirmButtonColor: '#dc3545'

                        });

                    }

                });

            });

        }
    </script>


</body>

</html>