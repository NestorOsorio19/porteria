<?php 
    // Se incluye el archivo que contiene la función para establecer la conexión con la base de datos.
    include("../Config/database.php");

    // Se establece la conexión a la base de datos mediante la función 'connection'.
    $con = connection();

    // Se define la consulta SQL para seleccionar todos los registros de la tabla 'contratistas'.
    $sql = "SELECT * FROM contratistas";

    // Se ejecuta la consulta SQL y se guarda el resultado en la variable $query.
    $query = mysqli_query($con, $sql);    
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Ingreso de datos a la base de usuarios">
        <!-- Se incluyen los archivos CSS para el diseño de la página -->
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/header.css" rel="stylesheet">
        <title>Consulta Portería</title>        
    </head>
    <body>
        <!-- Se crea un contenedor para la tabla de contratistas -->
        <div class="users-table">
            <h2>Consulta General de Contratistas</h2>
            <!-- Se define la tabla para mostrar los datos de los contratistas -->
            <table id="tablaPersonas" name="tablaPersonas">
                <thead>
                    <tr>
                        <!-- Encabezados de la tabla que corresponden a los datos de los contratistas -->
                        <th>ID</th>
                        <th>Fecha</th> 
                        <th>Nombre</th> 
                        <th>Cédula</th>
                        <th>Arl</th> 
                        <th>EPS</th>
                        <th>Nom Emer</th> 
                        <th>Tel Emer</th> 
                        <th>RH</th>  
                        <th>Placa</th>
                        <th>Ingreso</th>                          
                        <th>Equipo</th>
                        <th>Tipo</th>
                        <th>Serial</th>
                        <th>Motivo</th>
                        <th></th> <!-- Espacio para editar -->
                        <th></th> <!-- Espacio para eliminar -->

                        <!-- Se incluyen los scripts necesarios para el funcionamiento de DataTables -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

                        <!-- Inicialización de DataTables para la tabla de contratistas -->
                        <script>
                            var tablaPersonas; // Variable global para la instancia de DataTable

                            $(document).ready(function () {
                                // Inicializa DataTables con algunas configuraciones como paginación y búsqueda
                                tablaPersonas = $('#tablaPersonas').DataTable({
                                    "paging": true, // Activar paginación
                                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], // Opciones de cantidad de registros por página
                                    "searching": true // Habilitar búsqueda
                                });
                            });
                        </script>
                    </tr>
                    <br>
                    <!-- Botón que redirige al menú de registro de contratistas -->
                    <input type="button" onclick="window.location.href='../View/registrocontratistas.php';" value="Menú">                    
                </thead>
                <tbody>
                    <!-- Bucle PHP para recorrer los resultados de la consulta y mostrar cada registro en una fila de la tabla -->
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <!-- Muestra los datos de cada contratista en la tabla -->
                            <th><?= $row['id'] ?></th>
                            <th><?= $row['fecha'] ?></th> 
                            <th><?= $row['nombre'] ?></th> 
                            <th><?= $row['cedula'] ?></th>  
                            <th><?= $row['arl'] ?></th>                                                 
                            <th><?= $row['eps'] ?></th>
                            <th><?= $row['nombre_emergencia'] ?></th>
                            <th><?= $row['telefono_emergencia'] ?></th>
                            <th><?= $row['rh'] ?></th> 
                            <th><?= $row['placa'] ?></th>
                            <th><?= $row['ingreso'] ?></th>
                            <th><?= $row['equipo'] ?></th> 
                            <th><?= $row['tipo'] ?></th> 
                            <th><?= $row['serial'] ?></th>  
                            <th><?= $row['motivo'] ?></th>  
                            <!-- Enlaces para editar y eliminar los registros -->
                            <th><a href="actualizar.php?id=<?= $row['id'] ?>" class="users-table--edit">Editar</a></th>
                            <th><a href="eliminar.php?id=<?= $row['id'] ?>" class="users-table--delete">Eliminar</a></th>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
