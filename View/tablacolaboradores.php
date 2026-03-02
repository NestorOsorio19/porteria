<?php
// Incluir la conexión a la base de datos
include("../Config/database.php");

// Establecer la conexión a la base de datos
$con = connection();

// Consulta SQL para obtener todos los registros de la tabla 'porteria'
$sql = "SELECT c.*, 
       a.nom_arl, 
       e.nom_eps, 
       ar.nom_area
FROM colaboradores c
LEFT JOIN arls a ON c.id_arl = a.id_arl
LEFT JOIN eps e ON c.id_eps = e.id_eps
LEFT JOIN areas ar ON c.id_area = ar.id_area
ORDER BY c.fecha DESC";

// Ejecutar la consulta y verificar si se ha realizado correctamente
$query = mysqli_query($con, $sql);
if (!$query) {
    echo "Error en la consulta: " . mysqli_error($con);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Consulta de registros de colaboradores">
    <meta name="keywords" content="php, base de datos, colaboradores, sistema">
    <link rel="stylesheet" href="../View/CSS/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Consulta Colaboradores</title>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f6fa;
            color: #2f3640;
            margin: 0;
            padding: 0;
        }

        .users-table {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #192a56;
        }

        .botones-accion {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 15px;
        }

        .botones-accion button {
            padding: 8px 16px;
            border: none;
            border-radius: 25px;
            background-color: #4cd137;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .botones-accion button:hover {
            background-color: #44bd32;
        }

        .btnSalida {
            background-color: #e84118;
            border: none;
            color: white;
            padding: 5px 12px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.2s;
        }

        .btnSalida:hover {
            background-color: #c23616;
        }

        table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        table.dataTable thead th {
            background-color: #273c75;
            color: #fff;
            font-weight: bold;
            padding: 12px 10px;
            text-align: center;
        }

        table.dataTable tbody td {
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        table.dataTable tbody tr:nth-child(even) {
            background-color: #f1f2f6;
        }

        table.dataTable tbody tr:hover {
            background-color: #dcdde1;
            transition: 0.2s;
        }

        @media screen and (max-width: 768px) {

            table.dataTable,
            table.dataTable thead,
            table.dataTable tbody,
            table.dataTable th,
            table.dataTable td,
            table.dataTable tr {
                display: block;
            }

            table.dataTable thead tr {
                display: none;
            }

            table.dataTable tbody tr {
                margin-bottom: 15px;
                border-radius: 10px;
                background: #fff;
                padding: 10px;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            }

            table.dataTable tbody td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            table.dataTable tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: bold;
                text-transform: uppercase;
            }
        }
    </style>
</head>

<body>
    <div class="users-table">
        <h2>Tabla General Registros Colaboradores</h2>

        <div class="botones-accion">
            <button onclick="window.location.href='../View/registrocolaboradores.php';">Menú</button>
            <!-- <button onclick="window.location.href='generarExcel.php';">Generar Excel</button> -->
        </div>

        <table id="tablaColaboradores" class="display">
            <thead>
                <tr>
                    <!--  <th>Id</th> -->
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Arl</th>
                    <th>Eps</th>
                    <th>Rh</th>
                    <th>Contac Emer</th>
                    <th>Tel Emer</th>
                    <th>Area</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td data-label="ID"><?= htmlspecialchars($row['id_registro'] ?? '') ?></td>
                        <td data-label="Fecha"><?= htmlspecialchars($row['fecha'] ?? '') ?></td>
                        <td data-label="Nombre"><?= htmlspecialchars($row['nombre'] ?? '') ?></td>
                        <td data-label="Cédula"><?= htmlspecialchars($row['cedula'] ?? '') ?></td>
                        <td data-label="ARL"><?= htmlspecialchars($row['nom_arl'] ?? '') ?></td>
                        <td data-label="EPS"><?= htmlspecialchars($row['nom_eps'] ?? '') ?></td>
                        <td data-label="RH"><?= htmlspecialchars($row['rh'] ?? '') ?></td>
                        <td data-label="Contac Emer"><?= htmlspecialchars($row['nom_eme'] ?? '') ?></td>
                        <td data-label="Tel Emer"><?= htmlspecialchars($row['tel_eme'] ?? '') ?></td>
                        <td data-label="Area"><?= htmlspecialchars($row['nom_area'] ?? '') ?></td>
                        <td data-label="Ingreso"><?= htmlspecialchars($row['ingreso'] ?? '') ?></td>
                        <td data-label="Salida" id_registro="salida_<?= $row['id_registro'] ?>"><?= htmlspecialchars($row['salida'] ?? '') ?></td>
                        <td data-label="Acciones">
                            <button
                                id_registro="btnSalida_<?= $row['id_registro'] ?>"
                                class="btnSalida"
                                data-registro="<?= $row['id_registro'] ?>"
                                <?= !empty($row['salida']) ? 'style="display:none;"' : '' ?>
                                onclick="marcarSalida(<?= $row['id_registro'] ?>)">
                                Marcar Salida
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable para la tabla 'tablaColaboradores'
            $('#tablaColaboradores').DataTable({
                order: [
                    [0, 'desc']
                ], // Ordenar por ID en orden descendente
                paging: true, // Habilitar paginación
                lengthMenu: [
                    [10, 50, -1],
                    [10, 50, "Todos"]
                ], // Opciones para el número de filas por página
                searching: true // Habilitar búsqueda en la tabla
            });
        });

        function marcarSalida(id_registro) {
            // Obtener la hora actual en HH:MM:SS
            const horaSalida = new Date().toTimeString().split(' ')[0];

            // Confirmación con SweetAlert
            Swal.fire({
                title: '¿Desea marcar la salida?',
                text: "Se registrará la hora de salida del colaborador.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, marcar salida',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hacer la petición AJAX solo si confirmamos
                    $.post('../Controller/salidas_colaboradores.php', {
                        id_registro: id_registro,
                        hora_salida: horaSalida
                    }, function(respuesta) {
                        if (respuesta.trim() === 'ok') {
                            // Actualizar la tabla
                            $('#salida_' + id_registro).text(horaSalida);
                            $('#btnSalida_' + id_registro).hide();

                            // Mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: 'Salida registrada',
                                text: 'La hora de salida se ha registrado correctamente.'
                            });
                        } else {
                            // Mensaje de error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo registrar la salida. Revisa la consola para más detalles.'
                            });
                            console.log(respuesta); // Depuración
                        }
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de servidor',
                            text: 'No se pudo conectar con el servidor.'
                        });
                    });
                }
            });
        }
    </script>
</body>

</html>