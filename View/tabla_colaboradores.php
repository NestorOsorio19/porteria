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

    die(
        "Error al consultar los colaboradores: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}


/* ==========================================================
   FUNCIÓN DE SEGURIDAD PARA MOSTRAR DATOS
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

        body {

            font-family: Arial, sans-serif;

            background: #f5f6fa;

            color: #2f3640;

            margin: 0;

            padding: 0;

        }


        /* =====================================================
           CONTENEDOR PRINCIPAL
        ====================================================== */

        .users-table {

            max-width: 1500px;

            margin: 30px auto;

            padding: 20px;

        }


        /* =====================================================
           ENCABEZADO
        ====================================================== */

        .card-header-custom {

            background: #ffffff;

            border-radius: 15px;

            padding: 20px 25px;

            box-shadow:
                0 5px 18px rgba(0, 0, 0, 0.08);

            margin-bottom: 20px;

        }


        .card-header-custom h2 {

            margin: 0;

            color: #192a56;

            font-weight: 700;

        }


        .card-header-custom small {

            color: #718093;

        }


        .header-buttons {

            display: flex;

            gap: 10px;

            align-items: center;

        }


        /* =====================================================
           BOTONES
        ====================================================== */

        .btn-nuevo,
        .btn-menu {

            color: white;

            border: none;

            padding: 10px 18px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: 600;

            transition: 0.2s;

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
           TABLA
        ====================================================== */

        .table-container {

            background: #fff;

            padding: 20px;

            border-radius: 15px;

            box-shadow:
                0 5px 18px rgba(0, 0, 0, 0.08);

        }


        table.dataTable {

            width: 100% !important;

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
           CÉDULA
        ====================================================== */

        .cedula {

            background: #718093;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;

        }


        /* =====================================================
           HORA INGRESO
        ====================================================== */

        .hora-ingreso {

            background: #28a745;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;

        }


        /* =====================================================
           HORA SALIDA
        ====================================================== */

        .hora-salida {

            background: #17a2b8;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;

        }


        /* =====================================================
           PENDIENTE
        ====================================================== */

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

        .btnSalida {

            background: #e84118;

            color: white;

            border: none;

            padding: 7px 13px;

            border-radius: 6px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;

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

            background: #28a745;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;

        }


        /* =====================================================
           DATATABLES
        ====================================================== */

        .dataTables_wrapper
        .dataTables_filter
        input {

            border: 1px solid #ced4da;

            border-radius: 6px;

            padding: 6px 10px;

            margin-left: 5px;

        }


        .dataTables_wrapper
        .dataTables_length
        select {

            border: 1px solid #ced4da;

            border-radius: 6px;

            padding: 5px;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

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


            .table-container {

                padding: 10px;

                overflow-x: auto;

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

                    <i class="fas fa-users text-danger"></i>

                    Registro General de Colaboradores

                </h2>


                <small>

                    Consulta y control de ingreso y salida

                </small>

            </div>


            <div class="header-buttons">


                <!-- MENÚ -->

                <a
                    href="../index.php"
                    class="btn-menu">

                    <i class="fas fa-home"></i>

                    Menú

                </a>


                <!-- NUEVO REGISTRO -->

                <a
                    href="registrocolaboradores.php"
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

                    <th>Cédula</th>

                    <th>Nombre</th>

                    <th>Telefono</th>

                    <th>ARL</th>

                    <th>EPS</th>

                    <th>RH</th>

                    <th>Contacto Emergencia</th>

                    <th>Tel. Emergencia</th>

                    <th>Área</th>

                    <th>Marca</th>

                    <th>Serial</th>

                    <th>Ingreso</th>

                    <th>Salida</th>

                    <th>Acción</th>

                </tr>

                </thead>


                <tbody>


                <?php while (
                    $row = $stmt->fetch(PDO::FETCH_ASSOC)
                ): ?>


                    <?php

                    /*
                     * ID PRINCIPAL DEL REGISTRO
                     */

                    $id = (int)($row['id_registro'] ?? 0);


                    /*
                     * HORA DE SALIDA
                     */

                    $horaSalida = trim(
                        $row['salida'] ?? ''
                    );


                    /*
                     * Verificar si ya tiene salida
                     */

                    $tieneSalida =
                        $horaSalida !== '' &&
                        $horaSalida !== '00:00:00';

                    ?>


                    <tr>


                        <!-- =================================================
                             ID
                        ================================================== -->

                        <td>

                            <?= $id ?>

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
                            CÉDULA
                        ================================================== -->

                        <td>

                            <span class="cedula">

                                <?= e(
                                    $row['cedula'] ?? ''
                                ) ?>

                            </span>

                        </td>


                        <!-- =================================================
                            NOMBRE
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['nombre'] ?? ''
                            ) ?>

                        </td>

                        <!-- =================================================
                            TELEFONO
                        ================================================== -->

                        <td>

                            <span class="telefono">

                                <?= e(
                                    $row['telefono'] ?? ''
                                ) ?>

                            </span>

                        </td>

                        <!-- =================================================
                            ARL
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['nom_arl'] ?? 'Sin ARL'
                            ) ?>

                        </td>


                        <!-- =================================================
                             EPS
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['nom_eps'] ?? 'Sin EPS'
                            ) ?>

                        </td>


                        <!-- =================================================
                             RH
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['rh'] ?? ''
                            ) ?>

                        </td>


                        <!-- =================================================
                             CONTACTO
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['nom_eme'] ?? ''
                            ) ?>

                        </td>


                        <!-- =================================================
                             TELÉFONO
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['tel_eme'] ?? ''
                            ) ?>

                        </td>


                        <!-- =================================================
                             ÁREA
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['nom_area'] ?? ''
                            ) ?>

                        </td>


                        <!-- =================================================
                             MARCA
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['marca'] ?? ''
                            ) ?>

                        </td>


                        <!-- =================================================
                             SERIAL
                        ================================================== -->

                        <td>

                            <?= e(
                                $row['serial'] ?? ''
                            ) ?>

                        </td>


                        <!-- =================================================
                             INGRESO
                        ================================================== -->

                        <td>

                            <?php if (
                                !empty($row['ingreso'])
                            ): ?>

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

        pageLength: 10,

        order: [
            [0, 'desc']
        ],

        lengthMenu: [
            [10, 50, 100, -1],
            [10, 50, 100, "Todos"]
        ],

        language: {

            search: "Buscar:",

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

            emptyTable:
                "No hay colaboradores registrados",

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


    /* =====================================================
       CONFIRMACIÓN
    ====================================================== */

    Swal.fire({

        title: '¿Registrar salida?',

        text:
            'Se registrará la hora de salida del colaborador.',

        icon: 'question',

        showCancelButton: true,

        confirmButtonText:
            'Sí, registrar',

        cancelButtonText:
            'Cancelar',

        confirmButtonColor:
            '#dc3545',

        cancelButtonColor:
            '#6c757d',

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
                '<i class="fas fa-spinner fa-spin"></i> ' +
                'Registrando...'
            );


        /* =================================================
           AJAX
        ================================================== */

        $.ajax({

            url:
                '../Controller/salidas_colaboradores.php',

            type:
                'POST',

            data: {

                id_registro: id

            },

            dataType:
                'json',


            /* =============================================
               RESPUESTA CORRECTA
            ============================================== */

            success: function(response) {


                console.log(
                    'RESPUESTA PHP:',
                    response
                );


                /* =========================================
                   ÉXITO
                ========================================== */

                if (response.ok === true) {


                    const horaSalida =
                        response.hora_salida || '';


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

                        title:
                            'Salida registrada',

                        text:
                            'La hora de salida se registró correctamente.',

                        icon:
                            'success',

                        confirmButtonColor:
                            '#198754',

                        confirmButtonText:
                            'Aceptar',

                        timer:
                            2000,

                        timerProgressBar:
                            true

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

                    title:
                        'Error de servidor',

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

                    icon:
                        'error',

                    confirmButtonColor:
                        '#dc3545'

                });

            }

        });

    });

}

</script>


</body>

</html>
