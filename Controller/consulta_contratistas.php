<?php

require_once "../Config/database.php";

$con = connection();

try {

    /* =====================================================
       CONSULTA GENERAL DE CONTRATISTAS
    ====================================================== */

    $sql = "
        SELECT
            v.*,
            a.nom_arl,
            e.nom_eps,
            emp.nom_empresa
        FROM contratistas v

        LEFT JOIN arls a
            ON v.id_arl = a.id_arl

        LEFT JOIN eps e
            ON v.id_eps = e.id_eps

        LEFT JOIN empresas emp
            ON v.empresa_fk = emp.id_registro

        ORDER BY v.fecha DESC
    ";

    $stmt = $con->prepare($sql);

    $stmt->execute();

} catch (PDOException $e) {

    die(
        "Error al consultar los contratistas: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
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
        content="Consulta de registros de contratistas">

    <meta
        name="keywords"
        content="php, pdo, base de datos, contratistas, sistema">

    <title>Consulta Contratistas</title>


    <!-- =====================================================
         JQUERY
    ====================================================== -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js">
    </script>


    <!-- =====================================================
         SWEETALERT
    ====================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>


    <!-- =====================================================
         DATATABLES
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <script
        src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js">
    </script>


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
           CONTENEDOR
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


        .header-content {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

        }


        .card-header-custom h2 {

            margin: 0;

            color: #192a56;

            font-weight: 700;

        }


        .card-header-custom small {

            color: #718093;

        }


        /* =====================================================
           BOTONES
        ====================================================== */

        .header-buttons {

            display: flex;

            gap: 10px;

            align-items: center;

        }


        .btn-menu,
        .btn-nuevo {

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


        .btn-menu {

            background: #718093;

        }


        .btn-menu:hover {

            background: #576574;

            color: white;

            transform: translateY(-1px);

        }


        .btn-nuevo {

            background: #273c75;

        }


        .btn-nuevo:hover {

            background: #192a56;

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
           SALIDA PENDIENTE
        ====================================================== */

        .pendiente {

            background: #ffc107;

            color: #212529;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;

        }


        /* =====================================================
           HORA DE SALIDA
        ====================================================== */

        .hora-salida {

            background: #17a2b8;

            color: white;

            padding: 5px 9px;

            border-radius: 6px;

            font-weight: 600;

        }


        /* =====================================================
           BOTÓN SALIDA
        ====================================================== */

        .btnSalida {

            background: #e84118;

            border: none;

            color: white;

            padding: 7px 13px;

            border-radius: 6px;

            cursor: pointer;

            font-weight: 600;

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
        .dataTables_filter input {

            border: 1px solid #ced4da;

            border-radius: 6px;

            padding: 6px 10px;

            margin-left: 5px;

        }


        .dataTables_wrapper
        .dataTables_length select {

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

                align-items: flex-start;

            }


            .header-buttons {

                width: 100%;

                flex-direction: column;

            }


            .btn-menu,
            .btn-nuevo {

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

            <div class="header-content">


                <div>

                    <h2>

                        <i
                            class="fas fa-hard-hat"
                            style="color:#dc3545;">
                        </i>

                        Registro General de Contratistas

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


                    <!-- NUEVO -->

                    <a
                        href="registro_contratistas.php"
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
                    id="tablaContratistas"
                    class="display">


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Fecha</th>

                            <th>Nombre</th>

                            <th>Cédula</th>

                            <th>RH</th>

                            <th>ARL</th>

                            <th>EPS</th>

                            <th>Empresa</th>

                            <th>Enfermedad / Alergia</th>

                            <th>Contacto Emergencia</th>

                            <th>Tel. Emergencia</th>

                            <th>Marca</th>

                            <th>Serial</th>

                            <th>Ingreso</th>

                            <th>Salida</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>


                            <?php

                            /*
                             * Identificador principal
                             */

                            $id = (int) ($row['id'] ?? 0);


                            /*
                             * Hora de salida
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


                                <!-- ID -->

                                <td data-label="ID">

                                    <?= $id ?>

                                </td>


                                <!-- FECHA -->

                                <td data-label="Fecha">

                                    <?= htmlspecialchars(
                                        $row['fecha'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- NOMBRE -->

                                <td data-label="Nombre">

                                    <?= htmlspecialchars(
                                        $row['nombre'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- CÉDULA -->

                                <td data-label="Cédula">

                                    <?= htmlspecialchars(
                                        $row['cedula'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- RH -->

                                <td data-label="RH">

                                    <?= htmlspecialchars(
                                        $row['rh'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- ARL -->

                                <td data-label="ARL">

                                    <?= htmlspecialchars(
                                        $row['nom_arl'] ?? 'Sin ARL',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- EPS -->

                                <td data-label="EPS">

                                    <?= htmlspecialchars(
                                        $row['nom_eps'] ?? 'Sin EPS',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- EMPRESA -->

                                <td data-label="Empresa">

                                    <?= htmlspecialchars(
                                        $row['nom_empresa'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- ENFERMEDAD / ALERGIA -->

                                <td data-label="Enfermedad / Alergia">

                                    <?= htmlspecialchars(
                                        $row['enfermedad_alergia'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- CONTACTO EMERGENCIA -->

                                <td data-label="Contacto Emergencia">

                                    <?= htmlspecialchars(
                                        $row['nombre_emergencia'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- TELEFONO EMERGENCIA -->

                                <td data-label="Tel. Emergencia">

                                    <?= htmlspecialchars(
                                        $row['telefono_emergencia'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- MARCA -->

                                <td data-label="Marca">

                                    <?= htmlspecialchars(
                                        $row['marca'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- SERIAL -->

                                <td data-label="Serial">

                                    <?= htmlspecialchars(
                                        $row['serial'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <!-- INGRESO -->

                                <td data-label="Ingreso">

                                    <?php if (!empty($row['ingreso'])): ?>

                                        <span
                                            style="
                                                background:#28a745;
                                                color:white;
                                                padding:5px 9px;
                                                border-radius:6px;
                                                font-weight:600;
                                            ">

                                            <?= htmlspecialchars(
                                                $row['ingreso'],
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

                                <td
                                    data-label="Salida"
                                    id="salida_<?= $id ?>">


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


                                <!-- ACCIONES -->

                                <td
                                    data-label="Acciones"
                                    id="accion_<?= $id ?>">


                                    <?php if (!$tieneSalida): ?>


                                        <button
                                            type="button"
                                            id="btnSalida_<?= $id ?>"
                                            class="btnSalida"
                                            onclick="marcarSalida(<?= $id ?>)">

                                            <i
                                                class="fas fa-sign-out-alt">
                                            </i>

                                            Salida

                                        </button>


                                    <?php else: ?>


                                        <span class="finalizado">

                                            <i
                                                class="fas fa-check">
                                            </i>

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
         FONT AWESOME
    ====================================================== -->

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js">
    </script>


    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->

    <script>


        /* =====================================================
           DATATABLE
        ====================================================== */

        $(document).ready(function() {


            $('#tablaContratistas').DataTable({

                pageLength: 10,

                order: [
                    [0, 'desc']
                ],

                lengthMenu: [
                    [10, 50, -1],
                    [10, 50, "Todos"]
                ],

                searching: true,

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

                    paginate: {

                        first: "Primero",

                        last: "Último",

                        next: "Siguiente",

                        previous: "Anterior"

                    }

                }

            });

        });



        /* =====================================================
           REGISTRAR SALIDA
        ====================================================== */

        function marcarSalida(id) {


            Swal.fire({

                title: "¿Registrar salida?",

                text:
                    "La salida del contratista será registrada.",

                icon: "question",

                showCancelButton: true,

                confirmButtonColor: "#dc3545",

                cancelButtonColor: "#6c757d",

                confirmButtonText:
                    "Sí, registrar",

                cancelButtonText:
                    "Cancelar",

                reverseButtons: true

            }).then(function(result) {


                if (!result.isConfirmed) {

                    return;

                }


                /* =================================================
                   BOTÓN
                ================================================= */

                const $boton =
                    $("#btnSalida_" + id);


                $boton

                    .prop("disabled", true)

                    .html(
                        '<i class="fas fa-spinner fa-spin"></i> ' +
                        'Registrando...'
                    );


                /* =================================================
                   AJAX
                ================================================= */

                $.ajax({

                    url:
                        "../Controller/salidas_contratistas.php",

                    type:
                        "POST",

                    data: {

                        id: id

                    },

                    dataType:
                        "json",


                    /* =============================================
                       ÉXITO
                    ============================================== */

                    success: function(response) {


                        console.log(
                            "RESPUESTA PHP:",
                            response
                        );


                        if (
                            response.ok === true
                        ) {


                            const horaSalida =
                                response.hora_salida ||
                                "";


                            /* =====================================
                               ACTUALIZAR SALIDA
                            ====================================== */

                            $("#salida_" + id)
                                .html(

                                    '<span class="hora-salida">' +

                                    horaSalida +

                                    '</span>'

                                );


                            /* =====================================
                               CAMBIAR ACCIÓN
                            ====================================== */

                            $("#accion_" + id)
                                .html(

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
                                    "Salida registrada",

                                text:
                                    "La hora de salida se registró correctamente.",

                                icon:
                                    "success",

                                confirmButtonColor:
                                    "#28a745",

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
                                    "disabled",
                                    false
                                )

                                .html(

                                    '<i class="fas fa-sign-out-alt"></i> ' +

                                    'Salida'

                                );


                            Swal.fire({

                                title:
                                    "Aviso",

                                text:
                                    response.mensaje ||
                                    "No se pudo registrar la salida.",

                                icon:
                                    "warning"

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


                        /* =====================================
                           RESTAURAR BOTÓN
                        ====================================== */

                        $boton

                            .prop(
                                "disabled",
                                false
                            )

                            .html(

                                '<i class="fas fa-sign-out-alt"></i> ' +

                                'Salida'

                            );


                        Swal.fire({

                            title:
                                "Error de servidor",

                            html:

                                "<b>Código HTTP:</b> " +
                                xhr.status +

                                "<br><br>" +

                                "<b>Respuesta del servidor:</b>" +

                                "<pre " +
                                "style='text-align:left;" +
                                "white-space:pre-wrap;" +
                                "max-height:300px;" +
                                "overflow:auto;'>" +

                                $("<div>")
                                    .text(
                                        xhr.responseText
                                    )
                                    .html() +

                                "</pre>",
                            icon:
                                "error"

                        });

                    }

                });

            });

        }

    </script>


</body>

</html>
