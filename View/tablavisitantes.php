<?php 
// Incluir la conexión a la base de datos
include("../Config/database.php");

// Establecer la conexión a la base de datos
$con = connection();

// Consulta SQL para obtener todos los registros de la tabla 'porteria'
$sql = "SELECT * FROM porteria";

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
    <meta name="description" content="Consulta de registros de visitantes">
    <meta name="keywords" content="php, base de datos, visitantes, sistema">
    <link rel="stylesheet" href="../View/CSS/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <title>Consulta Visitantes</title>

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
        <h2>Tabla General Registros Visitantes</h2>

        <div class="botones-accion">
            <button onclick="window.location.href='../index.php';">Menú</button>
            <!-- <button onclick="window.location.href='generarExcel.php';">Generar Excel</button> -->
        </div>

        <table id="tablaVisitantes" class="display">
            <thead>
                <tr>
                    <!--  <th>Id</th> -->
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Arl</th>
                    <th>Eps</th>
                    <th>Rh</th>
                    <th>Empresa</th>
                    <th>Motivo</th>
                    <th>Carnet</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        
                        <td data-label="Fecha"><?= htmlspecialchars($row['fecha']) ?></td>
                        <td data-label="Nombre"><?= htmlspecialchars($row['nombre']) ?></td>
                        <td data-label="Cédula"><?= htmlspecialchars($row['cedula']) ?></td>
                        <td data-label="arl"><?= htmlspecialchars($row['arl']) ?></td>
                        <td data-label="eps"><?= htmlspecialchars($row['eps']) ?></td>
                        <td data-label="rh"><?= htmlspecialchars($row['rh']) ?></td>
                        <td data-label="empresa"><?= htmlspecialchars($row['empresa']) ?></td>
                        <td data-label="Motivo"><?= htmlspecialchars($row['motivo']) ?></td>
                        <td data-label="Carnet"><?= htmlspecialchars($row['carnet']) ?></td>
                        <td data-label="Ingreso"><?= htmlspecialchars($row['ingreso']) ?></td>
                        <td data-label="Salida" id="salida_<?= $row['id'] ?>"><?= htmlspecialchars($row['salida']) ?></td>
                        <td data-label="Acciones">
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
            // Inicializar DataTable para la tabla 'tablaVisitantes'
            $('#tablaVisitantes').DataTable({
                order: [[0, 'desc']], // Ordenar por ID en orden descendente
                paging: true, // Habilitar paginación
                lengthMenu: [[10, 50, -1], [10, 50, "Todos"]], // Opciones para el número de filas por página
                searching: true // Habilitar búsqueda en la tabla
            });
        });

        function marcarSalida(id) {
            const horaSalida = new Date().toTimeString().split(' ')[0]; // Obtener la hora actual (HH:MM:SS)

            $.post('../Controller/salidas_visitantes.php', {
                id: id,
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
