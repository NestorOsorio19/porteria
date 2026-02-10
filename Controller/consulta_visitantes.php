<?php 
    // Se incluye el archivo que contiene la función para establecer la conexión con la base de datos.
    include("../Config/database.php");

    // Se establece la conexión a la base de datos utilizando la función 'connection' definida en el archivo incluido.
    $con = connection();

    // Se define la consulta SQL para seleccionar todos los registros de la tabla 'porteria'.
    $sql = "SELECT * FROM porteria";

    // Se ejecuta la consulta SQL utilizando la función 'mysqli_query', y los resultados se almacenan en la variable '$query'.
    $query = mysqli_query($con, $sql);    
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Se incluye el archivo de estilo CSS para la tabla -->
        <link href="../View/CSS/estilostablas.css" rel="stylesheet">
        <title>Consulta Visitantes</title>        
    </head>
    <body>
        <!-- Se crea un contenedor para mostrar la tabla de visitantes -->
        <div class="users-table">
            <h2>Consulta General de Visitantes</h2>
            <!-- Se define la tabla para mostrar los datos de los visitantes -->
            <table id="tablaPersonas" name="tablaPersonas">
                <thead>
                    <!-- Encabezados de la tabla con los nombres de las columnas que se mostrarán -->
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>ARL</th>
                    <th>EPS</th>
                    <th>RH</th>
                    <th>Teléfono</th>
                    <th>Empresa</th>
                    <th>Motivo</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>CARNET</th>

                    <!-- Se incluyen los scripts necesarios para usar DataTables -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

                    <!-- Se inicializa DataTables, una librería que facilita la manipulación y visualización de tablas -->
                    <script>
                        var tablaPersonas; // Se declara una variable global para la instancia de DataTable

                        $(document).ready(function () {
                            // Inicializa la tabla con el ID correspondiente y configura la paginación y búsqueda
                            tablaPersonas = $('#tablaPersonas').DataTable({
                                "paging": true, // Habilita la paginación
                                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], // Configura el número de registros por página
                                "searching": true // Habilita la búsqueda en la tabla
                            });
                        });
                    </script>
                    
                    </tr>
                    <br>
                    <!-- Botón que redirige al menú principal -->
                    <input type="button" onclick="window.location.href='index.php';" value="Menú">                    
                </thead>
                <tbody>
                    <!-- Se utiliza un bucle PHP para recorrer los resultados de la consulta y mostrar cada registro en una fila de la tabla -->
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <!-- Se muestran los datos de cada visitante en las celdas de la tabla -->
                            <th><?= $row['id'] ?></th>
                            <th><?= $row['fecha'] ?></th> 
                            <th><?= $row['nombre'] ?></th> 
                            <th><?= $row['cedula'] ?></th>  
                            <th><?= $row['arl'] ?></th>                                                 
                            <th><?= $row['eps'] ?></th>
                            <th><?= $row['rh'] ?></th> 
                            <th><?= $row['telefono'] ?></th>
                            <th><?= $row['empresa'] ?></th>  
                            <th><?= $row['motivo'] ?></th>
                            <th><?= $row['ingreso'] ?></th> 
                            <th><?= $row['salida'] ?></th>
                            <th><?= $row['carnet'] ?></th>   
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
