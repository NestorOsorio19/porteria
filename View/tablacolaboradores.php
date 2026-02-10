<?php
// Incluir la conexión
include("../Config/database.php");

// Conexión
$con = connection();

// Consulta segura con consulta preparada
$sql = "SELECT * FROM colaboradores";
$query = mysqli_query($con, $sql);

// Validar consulta
if (!$query) {
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Consulta de registros de colaboradores">
    <meta name="keywords" content="php, base de datos, colaboradores, sistema">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/header.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <title>Consulta Portería</title>

    <style>
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
        <h2>Tabla General Registros Colaboradores</h2>

        <div class="botones-accion">
            <button onclick="window.location.href='registrocolaboradores.php';">Menú</button>
            <!-- <button onclick="window.location.href='generarExcel.php';">Generar Excel</button> -->
        </div>

        <table id="TablaColaboradores" class="display">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Arl</th>
                    <th>EPS</th>
                    <th>Nom. Emergencia</th>
                    <th>Tel. Emergencia</th>
                    <th>Rh</th>
                    <th>Placa</th>
                    <th>Ingreso</th>
                    <th>Equipo</th>
                    <th>Tipo</th>
                    <th>Serial</th>
                    <th>Motivo</th>
                    <th>Salida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['cedula'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['arl'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['eps'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['nombre_emergencia'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['telefono_emergencia'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['rh'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['placa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['ingreso'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['equipo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['serial'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['motivo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td id="salida_<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($row['salida'], ENT_QUOTES, 'UTF-8') ?></td>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicializar DataTable para la tabla 'tablaPersonas'
            $('#TablaColaboradores').DataTable({
                order: [[0, 'desc']], // Ordenar por ID en orden descendente
                paging: true, // Habilitar paginación
                lengthMenu: [[10, 50, -1], [10, 50, "Todos"]], // Opciones para el número de filas por página
                searching: true // Habilitar búsqueda en la tabla
            });
        });

        function marcarSalida(id) {
            const horaSalida = new Date().toTimeString().split(' ')[0]; // Obtener la hora actual (HH:MM:SS)

            $.post('../Controller/salidas_colaboradores.php', {
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
