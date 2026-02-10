<?php
// Incluir el archivo de conexión para conectar con la base de datos
include("../Config/database.php");

// Iniciar sesión para manejar los mensajes de éxito o error
session_start();

// Establecer la conexión con la base de datos
$con = connection();

// Realizar la consulta SQL para obtener todos los registros de la tabla 'colaboradores'
$sql = "SELECT * FROM colaboradores";
$query = mysqli_query($con, $sql);

// Verificar si la consulta fue exitosa, si no, mostrar el error
if (!$query) {
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Vincular archivo de estilos CSS -->
    <link href="../View/CSS/estilosprincipales.css" rel="stylesheet">
    <!-- Incluir la librería de jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Registro Colaboradores</title>
</head>
<body>
    
<?php include("layout/header.html"); // Incluir el encabezado desde un archivo externo ?>

    <div class="main-content">
        <div class="users-form">
            <h1>Registros de Colaboradores</h1>

            <!-- Mostrar un mensaje de éxito si existe en la sesión -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <!-- Mostrar un mensaje de error si existe en la sesión -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Formulario para agregar un colaborador -->
            <form action="../Controller/ingreso_colaboradores.php" method="POST" id="form_colaborador">
                <!-- Campo para ingresar la fecha -->
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" required>

                <!-- Campo para ingresar la cédula -->
                <label for="cedula">Cédula:</label>
                <input type="text" name="cedula" id="cedula" placeholder="Cédula" required>

                <!-- Campo para ingresar el nombre -->
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


                <!-- Campo para ingresar el nombre del contacto de emergencia -->
                <label for="nombre_emergencia">Contacto en caso de emergencia (Nombre):</label>
                <input type="text" name="nombre_emergencia" id="nombre_emergencia" placeholder="Nombre" required>

                <!-- Campo para ingresar el teléfono de emergencia -->
                <label for="telefono_emergencia">Teléfono de emergencia:</label>
                <input type="text" name="telefono_emergencia" id="telefono_emergencia" placeholder="Teléfono" required>

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

                <!-- Campo opcional para ingresar la placa del vehículo -->
                <label for="placa">Placa Vehículo:</label>
                <input type="text" name="placa" id="placa" placeholder="Placa Vehículo">

                <!-- Campo para ingresar la hora de ingreso -->
                <label for="ingreso">Hora de ingreso:</label>
                <input type="time" name="ingreso" id="ingreso" required>

                <!-- Campo para seleccionar el área a la que se dirige el colaborador -->
                <label for="motivo">Área a la que se dirige:</label>
                <select name="motivo" id="motivo" required>
                    <option value="" disabled selected>Selecciona a donde se dirige...</option>
                    <option value="ambiental">Ambiental</option>
                    <option value="calidad">Calidad</option>
                    <option value="compras">Compras</option>
                    <option value="gerencia">Gerencia</option>
                    <option value="gestionhumana">Gestión Humana</option>
                    <option value="mantenimiento">Mantenimiento</option>
                    <option value="produccion">Producción</option>
                    <option value="sst">Oficina SST</option>
                    <option value="saladejuntas">Sala de Juntas</option>
                    <option value="sistemas">Sistemas</option>
                    <option value="transporte">Transporte</option>
                </select>

                <!-- Campo para seleccionar si se ingresa un equipo electrónico -->
                <label style="display: flex;">
                    <input name="equipo" type="checkbox" id="equipoElectronicoCheckbox" value="SI"> Ingreso de equipo electrónico
                </label>

                <!-- Campos ocultos que se mostrarán si se marca el checkbox de equipo electrónico -->
                <div id="inputsEquipoElectronico" style="display: none">
                    <label for="tipo">Tipo:</label>
                    <input type="text" id="tipo" name="tipo">

                    <label for="serial">Serial:</label>
                    <input type="text" id="serial" name="serial">
                </div>

                <!-- Botones de acción: enviar formulario o consultar registros -->
                <div class="buttons-container">
                    <input type="submit" value="Agregar">
                    <a href="../View/tablacolaboradores.php" class="btn-consulta">Consultar Registro</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Incluir script para el menú -->
    <script src="layout/menu.js"></script>
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

        $(document).ready(function() {
            // Mostrar/ocultar los campos de equipo electrónico al marcar o desmarcar el checkbox
            $("#equipoElectronicoCheckbox").on("change", function() {
                if (this.checked) {
                    $("#inputsEquipoElectronico").show(); // Mostrar los campos de equipo electrónico
                } else {
                    $("#inputsEquipoElectronico").hide(); // Ocultar los campos si no está marcado
                }
            });

            // Autocompletar los campos con los datos del colaborador cuando se ingresa la cédula
            $("#cedula").on("blur", function() {
                var cedula = $(this).val().trim();
                if (cedula.length > 0) {
                    $.ajax({
                        url: "../Controller/buscar_colaborador.php", // Consultar en la base de datos
                        method: "POST",
                        data: { cedula: cedula },
                        dataType: "json",
                        success: function(response) {
                            // Si la consulta es exitosa, completar los campos con la información
                            if (!response.error) {
                                $("input[name='nombre']").val(response.nombre);
                                $("select[name='arl']").val(response.arl);
                                $("select[name='eps']").val(response.eps);
                                $("input[name='nombre_emergencia']").val(response.nombre_emergencia);
                                $("input[name='telefono_emergencia']").val(response.telefono_emergencia);
                                $("select[name='rh']").val(response.rh);
                                $("input[name='placa']").val(response.placa);
                                $("input[name='ingreso']").val(response.ingreso);
                                $("select[name='motivo']").val(response.rh);
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
        });
    </script>
</body>
</html>
