<?php

require_once("../Config/database.php");

$con = connection();

$sql = "SELECT * FROM porteria ORDER BY id DESC";
$stmt = $con->prepare($sql);
$stmt->execute();

$visitantes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta General de Visitantes</title>

    ../View/CSS/estilostablas.css

    <!-- DataTables -->
    <link rel="stylesheet".net/1.13.8/css/jquery.dataTables.min.css

    <script/code.jquery.com/jquery-3.7.1.min.jsscript>

    <script/cdn.datatables.net/1.13.8/js/jquery.dataTables.min.jsscript>
</head>

<body>

    <div class="users-table">

        <h2>Consulta General de Visitantes</h2>

        <input
            type="button"
            value="Menú"
            onclick="window.location.href='index.php';">

        <br><br>

        <table id="tablaPersonas" class="display">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>ARL</th>
                    <th>EPS</th>
                    <th>RH</th>
                    <th>Empresa</th>
                    <th>Motivo</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Carnet</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($visitantes as $row): ?>

                    <tr>
                        <td><?= htmlspecialchars($row['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['fecha'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['cedula'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['telefono'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['arl'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['eps'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['rh'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['empresa'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['motivo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['ingreso'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['salida'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['carnet'] ?? '') ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <script>
        $(document).ready(function() {

            $('#tablaPersonas').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todos"]
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                }
            });

        });
    </script>

</body>

</html>