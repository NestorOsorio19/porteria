<?php 
// Incluir el archivo de conexión a la base de datos
include("Config/database.php");

// Establecer la conexión con la base de datos
$con = connection();

// Realizar una consulta SQL para obtener todos los registros de la tabla 'porteria'
$sql = "SELECT * FROM visitantes";

// Ejecutar la consulta y verificar si se ejecutó correctamente
if ($query = mysqli_query($con, $sql)) {
    // Aquí puedes procesar el resultado de la consulta, si es necesario
} else {
    // Si hubo un error en la consulta, mostrar el error
    echo "Error en la consulta: " . mysqli_error($con);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Vincular archivo de estilos CSS -->
    <link href="View/CSS/estilosprincipales.css" rel="stylesheet">
    <!-- Incluir la librería jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Registro Visitantes</title>
</head>
<body>

<header>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
    <div class="menu-lateral" id="menu-lateral">
        <ul class="nav-links">
            <li><a href="index.php">REGISTRO VISITANTES</a></li>
            <li><a href="View/tablavisitantes.php">TABLA REGISTROS VISITANTES</a></li>
            <li><a href="View/registrocolaboradores.php">REGISTROS COLABORADORES</a></li>
            <li><a href="View/tablacolaboradores.php">TABLA REGISTROS COLABORADORES</a></li>
            <li><a href="View/registrocontratistas.php">REGISTROS CONTRATISTAS</a></li>
            <li><a href="View/tablacontratistas.php">TABLA REGISTROS CONTRATISTAS</a></li>
        </ul>
        <div class="logo-container">
            <img src="View/Img/favicon-avicampo.png" width="40" alt="Logo">
        </div>
    </div>
</header>

<div class="main-content">
    <div class="users-form">
        <h1>Registros de Visitantes</h1>

        <!-- Mostrar un mensaje de éxito si existe en la sesión -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Mostrar un mensaje de error si existe en la sesión -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Formulario para agregar un visitante -->
        <form action="Controller/ingreso_visitantes.php" method="POST">
            <!-- Campo para ingresar la fecha -->
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" placeholder="Fecha" value="<?= date('Y-m-d') ?>">

            <!-- Campo para ingresar la cédula del visitante -->
            <label for="cedula">Cédula:</label>
            <input type="text" name="cedula" id="cedula" placeholder="Cédula" required>

            <!-- Campo para ingresar el nombre del visitante -->
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre" required>

            <!-- Campo para ingresar la ARL -->
            <label for="arl">Seleccione la ARL:</label>
                <select name="arl" id="arl" required>
                    <option value="" disabled selected>Selecione la ARL...</option>
                    <option value="sura">SURA</option>
                    <option value="postiva">POSITIVA</option>
                    <option value="axacolpatria">AXA COLPATRIA</option>
                    <option value="colmena">COLMENA</option>
                    <option value="Bolivia">BOLIVAR</option>
                    <option value="liberty">LIBERTY</option>
                    <option value="extranjero">EXTRANJERO</option>
                    <option value="otra">OTRA</option>
                </select>

            <!-- Campo para ingresar la EPS -->
            <label for="eps">Seleccione una EPS:</label>
                <select name="eps" id="eps" required>
                    <option value="" disabled selected>Seleccione la EPS...</option>
                    <option value="comeva">COMEVA EPS</option>
                    <option value="coosalud">COOSALUD</option>
                    <option value="famisanar">FAMISANAR</option>
                    <option value="nuevaeps">NUEVA EPS</option>
                    <option value="saludtotal">SALUD TOTAL</option>
                    <option value="sanitas">SANITAS</option>
                    <option value="sura">SURA</option>
                    <option value="otra">OTRA</option>
                </select>

            <!-- Campo para seleccionar el tipo de sangre -->
            <label for="rh">Tipo de Sangre:</label>
                <select name="rh" id="rh" required>
                    <option value="" disabled selected>Tipo de Sangre...</option>
                    <option value="O-">O -</option>
                    <option value="O+">O +</option>
                    <option value="A-">A -</option>
                    <option value="A+">A +</option>
                    <option value="B-">B -</option>
                    <option value="B+">B +</option>
                    <option value="AB-">AB -</option>
                    <option value="AB+">AB +</option>
                </select>

            <!-- Campo para ingresar el teléfono -->
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" placeholder="Teléfono" required>

        <!-- Campo para seleccionar empresa -->
        <label for="empresa">Empresa o Entidad a la que Pertenece:</label>
            <select name="empresa" id="empresa" required>
                <option value="" disabled selected>Seleccione la empresa de la que viene...</option>
                <option value="abogados">ABOGADOS E INVERSIONES</option>
                <option value="afigraficas">AFIGRAFICAS</option>
                <option value="agencia wellco">AGENCIA WELLCO</option>
                <option value="ajover">AJOVER</option>
                <option value="aldecar">ALDECAR</option>
                <option value="alfrio">ALFRIO</option>
                <option value="campoabaono">CAMPOABONO</option>
                <option value="comfenalco">COMFERNALCO</option>
                <option value="ecolab">ECOLAB</option>
                <option value="envia">ENVIA</option>
                <option value="g&r">G & R INGENIERIA</option>
                <option value="inoqualab">INOQUALAB</option>
                <option value="invima">INVIMA</option>
                <option value="palmerajunior">PALMERA JUNIOR</option>
                <option value="servientrega">SERVIENTREGA</option>
                <option value="sioma">SIOMA</option>
                <option value="tcc">TCC</option>
                <option value="tecnas">TECNAS</option>
                <option value="otra">OTRA</option>
            </select>

        <!-- Input adicional oculto para cuando se seleccione "OTRA" -->
        <div id="otra-empresa-container" style="display: none; margin-top: 10px;">
            <label for="empresa_otro">Ingrese el nombre de la empresa:</label>
            <input type="text" name="empresa_otro" id="empresa_otro" placeholder="Nombre de la empresa">
        </div>

            <!-- Campo para ingresar el motivo del ingreso -->
            <label for="motivo">Motivo de Ingreso:</label>
            <textarea class="form-control" name="motivo" id="motivo" placeholder="Motivo de ingreso..." required></textarea>

            <!-- Campo para ingresar la hora de ingreso -->
            <label for="ingreso">Hora de ingreso:</label>
            <input type="time" name="ingreso" id="ingreso" placeholder="Hora de ingreso" required>

            <!-- Campo para ingresar el número del carnet -->
            <label for="carnet">Ingrese el Número del Carnet:</label>
            <input type="text" name="carnet" id="carnet" placeholder="N° Carnet" required>

            <!-- Botones de acción: enviar formulario o consultar registros -->
            <div class="buttons-container">
                <input type="submit" value="Agregar">
                <a href="View/tablavisitantes.php" class="btn-consulta">Consultar Registro</a>
            </div>
        </form>
    </div>
</div>
<script>

     // Validar que el campo 'cedula' solo permita números
        $("#cedula").on("input", function() {
        var cedula = $(this).val();
        var cedulaValida = /^[0-9]*$/;  // Expresión regular para solo permitir números
        if (!cedulaValida.test(cedula)) {
            // Si no es válida, eliminamos el último carácter ingresado
            $(this).val(cedula.substring(0, cedula.length - 1));
            alert("La cédula solo debe contener números.");
        }
    });

    // Funcionalidad para autocompletar los campos con los datos del visitante cuando se ingresa la cédula
    $("#cedula").on("blur", function() {
        var cedula = $(this).val();
        if (cedula.length > 0) {
            $.ajax({
                url: "Controller/buscar_visitantes.php", // Realizar la consulta AJAX
                method: "POST",
                data: { cedula: cedula },
                dataType: "json",
                success: function(response) {
                    // Si la consulta es exitosa, llenar los campos con la información del visitante
                    if (!response.error) {
                        $("input[name='nombre']").val(response.nombre);
                        $("select[name='arl']").val(response.arl);
                        $("select[name='eps']").val(response.eps);
                        $("select[name='rh']").val(response.rh);
                        $("input[name='telefono']").val(response.telefono);
                        $("input[name='empresa']").val(response.empresa);
                        $("#motivo").val(response.motivo);
                        $("input[name='carnet']").val(response.carnet);
                    } else {
                        alert("Usuario no encontrado.");
                    }
                },
                error: function() {
                    alert("Hubo un error al consultar la base de datos.");
                }
            });
        }
    });

    // Funcionalidad para mostrar/ocultar el menú lateral al hacer clic en el botón
    $("#menu-toggle").click(function() {
        $("#menu-lateral").toggleClass("open"); // Alternar la clase para abrir/cerrar el menú lateral
        $(".main-content").toggleClass("shift"); // Mover el contenido principal al hacer toggle
    });

    // Mostrar campo adicional si se selecciona "OTRA" en empresa
    $("#empresa").on("change", function() {
        if ($(this).val() === "otra") {
            $("#otra-empresa-container").slideDown();
        } else {
            $("#otra-empresa-container").slideUp();
            $("#empresa_otro").val(""); // Limpiar campo si se oculta
        }
    });
</script>
</body>
</html>
