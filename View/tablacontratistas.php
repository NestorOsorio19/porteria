<?php
// Incluir la conexión a la base de datos
include("../Config/database.php");

// Establecer la conexión con la base de datos
$con = connection();

// Realizar la consulta SQL para obtener todos los registros de contratistas
$sql = "SELECT * FROM contratistas";
$query = mysqli_query($con, $sql);

// Validar si la consulta fue exitosa
if (!$query) {
    // Si la consulta falla, mostrar un mensaje de error
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Consulta de registros de contratistas">
    <meta name="keywords" content="php, base de datos, contratistas, sistema">
    <!-- Vincular archivos de estilos CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/header.css" rel="stylesheet">
    <!-- Incluir la librería CSS de DataTables para el manejo de tablas -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <title>Consulta Portería</title>

    <style>
        /* Estilo personalizado para el botón de salida */
        .btnSalida {
            background-color: coral;
            border: 1px solid coral;
            color: white;
            padding: 4px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: 0.15s ease;
        }

        .botones-accion {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="users-table">
        <h2>Tabla General Registros Contratistas</h2>

        <!-- Botones para acceder a otros menús y generar el reporte en Excel -->
        <div class="botones-accion">
            <button onclick="window.location.href='registrocontratistas.php';">Menú</button>
            <!-- <button onclick="window.location.href='generarExcel.php';">Generar Excel</button> -->
        </div>

        <!-- Tabla para mostrar los registros de los contratistas -->
        <table id="tablaPersonas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>RH</th>
                    <th>ARL</th>
                    <th>EPS</th>
                    <th>Enfermedad / Alergia</th>
                    <th>Nom. Emergencia</th>
                    <th>Tel. Emergencia</th>
                    <th>Inducción SG-SST</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Iterar sobre los resultados de la consulta y mostrar cada registro -->
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['fecha'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['cedula']) ?></td>
                        <td><?= $row['rh'] ?></td>
                        <td><?= htmlspecialchars($row['arl']) ?></td>
                        <td><?= htmlspecialchars($row['eps']) ?></td>
                        <td><?= htmlspecialchars($row['enfermedad_alergia']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_emergencia']) ?></td>
                        <td><?= htmlspecialchars($row['telefono_emergencia']) ?></td>
                        <td><?= $row['induccion_sgsst'] ? 'SI' : 'NO' ?></td>
                        <td><?= $row['ingreso'] ?></td>
                        <td id="salida_<?= $row['id'] ?>"><?= $row['salida'] ?></td>
                        <td>
                            <!-- Botón para marcar la salida, solo visible si la salida no está registrada -->
                            <button 
                                id="btnSalida_<?= $row['id'] ?>" 
                                class="btnSalida" 
                                data-registro="<?= $row['id'] ?>" 
                                <?= !empty($row['salida']) ? 'style="display:none;"' : '' ?> 
                                onclick="marcarSalida(<?= $row['id'] ?>)">
                                Marcar Salida
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <!-- Incluir la librería jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Incluir la librería de DataTables para mejorar la visualización de la tabla -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicializar DataTable para la tabla 'tablaPersonas'
            $('#tablaPersonas').DataTable({
                order: [[0, 'desc']], // Ordenar por ID en orden descendente
                paging: true, // Habilitar paginación
                lengthMenu: [[10, 50, -1], [10, 50, "Todos"]], // Opciones para el número de filas por página
                searching: true // Habilitar búsqueda en la tabla
            });
        });

        function marcarSalida(id) {
            const horaSalida = new Date().toTimeString().split(' ')[0]; // Obtener la hora actual (HH:MM:SS)

            $.post('../Controller/salidas_contratistas.php', {
                id: id,
                accion: 'salida',
                hora_salida: horaSalida
            }, function(respuesta) {
                if (respuesta) {
                    $('#salida_' + id).text(horaSalida); // Actualizar la hora de salida en la tabla
                    $('#btnSalida_' + id).hide(); // Ocultar el botón de marcar salida
                } else {
                    alert('No se pudo registrar la salida.');
                }
            }).fail(function() {
                alert('Error al conectar con el servidor.');
            });
        }
    </script>
</body>
</html>
