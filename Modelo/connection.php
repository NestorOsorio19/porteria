<?php
// Función para establecer la conexión con la base de datos
function connection() {
    // Configuración de los parámetros de conexión
    $host = "localhost"; // Dirección del servidor 
    $user = "root";    // Nombre de usuario para la base de datos
    $pass = "";    // Contraseña para la base de datos
    $bd = "porteria"; // Nombre de la base de datos a la que se quiere conectar

    // Crear una nueva instancia de la clase mysqli para establecer la conexión
    $connection = new mysqli($host, $user, $pass, $bd);

    // Verificar si ocurrió algún error en la conexión
    if ($connection->connect_error) {
        // Si hay error, mostrar un mensaje y detener la ejecución del script
        die("Conexión fallida: " . $connection->connect_error);
    }

    // Si la conexión es exitosa, devolver la conexión para usarla en otras consultas
    return $connection;
}
?>
