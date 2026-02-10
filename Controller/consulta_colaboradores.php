<?php 
    // Se incluye el archivo que contiene la función para establecer la conexión a la base de datos.
    include("../Config/database.php");

    // Se establece la conexión a la base de datos mediante la función 'connection'.
    $con = connection();

    // Se define una consulta SQL para seleccionar todos los registros de la tabla 'colaboradores'.
    $sql = "SELECT * FROM colaboradores";

    // Se ejecuta la consulta SQL y se guarda el resultado en la variable $query.
    $query = mysqli_query($con, $sql);    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Ingreso de datos a la base de usuarios">
        <!-- Se incluyen los estilos CSS para la página -->
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/header.css" rel="stylesheet">
        <title>Consulta Porteria</title>        
    </head>
    <body>
        <div class="users-table">
            <h2>Consulta General de Colaboradores Temporales Registrados</h2>
            <!-- Se crea una tabla para mostrar la información de los colaboradores -->
            <table id="TablaColaboradores" name="TablaColaboradores">
                <thead>
                    <tr>
                        <!-- Encabezados de las columnas de la tabla -->
                        <th>ID</th>
                        <th>Fecha</th> 
                        <th>Nombre</th> 
                        <th>Cedula</th>
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
                        <th></th>
                        <th></th>

                        <!-- Se incluyen las librerías jQuery y DataTables para funcionalidades de la tabla -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

                        <!-- Inicializa DataTables -->
                        <script>
                            var tablaPersonas; // Variable global para la instancia de DataTable

                            $(document).ready(function () {
                                // Inicializar DataTables con el ID correcto
                                tablaPersonas = $('#TablaColaboradores').DataTable({
                                    "paging": true, // Habilitar paginación
                                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], // Opciones de cantidad de registros por página
                                    "searching": true // Habilitar búsqueda
                                });
                            });
                        </script>                    
                    </tr>
                    <br>
                    <!-- Botón para regresar al menú principal -->
                    <input type="button" onclick="window.location.href='porteria.php';" value="Menú">                    
                </thead>
                <tbody>
                    <!-- Bucle PHP para recorrer los resultados de la consulta y mostrar cada registro en una fila de la tabla -->
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <!-- Mostrar los valores de cada columna en las filas de la tabla -->
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
                            <!-- Enlaces para editar o eliminar el registro -->
                            <th><a href="actualizar.php?id=<?= $row['id'] ?>" class="users-table--edit">Editar</a></th>
                            <th><a href="eliminar.php?id=<?= $row['id'] ?>" class="users-table--delete">Eliminar</a></th>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
