<?php
// Se incluye un archivo externo que probablemente contiene la función para establecer la conexión con la base de datos.
include("../Config/database.php");

// Se establece la conexión a la base de datos mediante la función 'connection' que debe estar definida en el archivo incluido.
$con = connection();

// Verificamos si la variable 'cedula' fue enviada por el método POST (es decir, si el formulario o solicitud contiene este dato).
if (isset($_POST['cedula'])) {

    // Se limpia la variable 'cedula' para prevenir inyecciones SQL, usando la función 'mysqli_real_escape_string' que escapa caracteres especiales.
    $cedula = mysqli_real_escape_string($con, $_POST['cedula']);

    // Se crea una consulta SQL para buscar en la tabla 'colaboradores' un registro donde la 'cedula' coincida con el valor proporcionado.
    $sql = "SELECT * FROM colaboradores WHERE cedula = '$cedula'";

    // Se ejecuta la consulta SQL usando 'mysqli_query', lo que devuelve un recurso de resultado.
    $query = mysqli_query($con, $sql);

    // Si la consulta devuelve al menos un registro (es decir, si se encuentra un colaborador con esa cédula),
    if (mysqli_num_rows($query) > 0) {
        
        // Se obtiene la primera fila del resultado como un arreglo asociativo con 'mysqli_fetch_assoc'.
        $row = mysqli_fetch_assoc($query);

        // Se convierte el arreglo a formato JSON y se envía como respuesta, para que pueda ser procesado en el lado del cliente.
        echo json_encode($row); // Enviar los datos en formato JSON
    } else {
        // Si no se encuentra ningún registro, se envía una respuesta JSON indicando que no se encontró al colaborador.
        echo json_encode(["error" => "No encontrado"]);
    }
}
?>
