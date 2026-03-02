<?php
session_start();

require_once 'Config/config.php';
require_once 'Config/database.php';

// ------------------------
// Conexión a la base de datos
// ------------------------
$con = connection();

// ------------------------
// Cargar Empresas
// ------------------------
$empresas = '';
$q_emp = mysqli_query($con, "SELECT id_registro, nom_empresa FROM empresas ORDER BY nom_empresa ASC");
if ($q_emp && mysqli_num_rows($q_emp) > 0) {
    while ($row = mysqli_fetch_assoc($q_emp)) {
        $empresas .= "<option value='" . htmlspecialchars($row['id_registro']) . "'>" . htmlspecialchars($row['nom_empresa']) . "</option>";
    }
} else {
    $empresas = "<option value='' disabled>No hay empresas disponibles</option>";
}

// ------------------------
// Cargar ARL
// ------------------------
$arl_options = '';
$q_arl = mysqli_query($con, "SELECT id_arl, nom_arl FROM arls ORDER BY nom_arl ASC");
if ($q_arl && mysqli_num_rows($q_arl) > 0) {
    while ($row = mysqli_fetch_assoc($q_arl)) {
        $arl_options .= "<option value='" . htmlspecialchars($row['id_arl']) . "'>" . htmlspecialchars($row['nom_arl']) . "</option>";
    }
} else {
    $arl_options = "<option value='' disabled>No hay ARL disponibles</option>";
}

// ------------------------
// Cargar EPS
// ------------------------
$eps_options = '';
$q_eps = mysqli_query($con, "SELECT id_eps, nom_eps FROM eps ORDER BY nom_eps ASC");
if ($q_eps && mysqli_num_rows($q_eps) > 0) {
    while ($row = mysqli_fetch_assoc($q_eps)) {
        $eps_options .= "<option value='" . htmlspecialchars($row['id_eps']) . "'>" . htmlspecialchars($row['nom_eps']) . "</option>";
    }
} else {
    $eps_options = "<option value='' disabled>No hay EPS disponibles</option>";
}

// Cerramos la conexión porque solo necesitamos los datos para los select
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Visitantes</title>
    <link href="View/CSS/estilosprincipales.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                <!-- ARL -->
                <label for="arl">Seleccione ARL:</label>
                <select name="arl" id="arl" required>
                    <option value="" disabled selected>Seleccione la ARL...</option>
                    <?= $arl_options ?>
                </select>

                <!-- EPS -->
                <label for="eps">Seleccione EPS:</label>
                <select name="eps" id="eps" required>
                    <option value="" disabled selected>Seleccione la EPS...</option>
                    <?= $eps_options ?>
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

                <!-- Empresa -->
                <label for="empresa">Empresa a la que Pertenece:</label>
                <select name="empresa" id="empresa" required>
                    <option value="" disabled selected>Seleccione la Empresa...</option>
                    <?= $empresas ?>
                    <option value="otra">OTRA...</option>
                </select>

                <!-- Input oculto -->
                <div id="nuevaEmpresaContainer" style="display:none;">
                    <label for="nueva_empresa">Nombre de la nueva empresa:</label>
                    <input type="text" name="nueva_empresa" id="nueva_empresa" placeholder="Ingrese el nombre de la empresa">
                </div>

                <!-- Campo para ingresar el motivo del ingreso -->
                <label for="motivo">Motivo de Ingreso:</label>
                <textarea class="form-control" name="motivo" id="motivo" placeholder="Motivo de ingreso..." required></textarea>

                <!-- Equipo Electrónico -->
                <label style="display: flex;">
                    <input name="equipo" type="checkbox" id="equipoElectronicoCheckbox" value="SI"> Ingreso de equipo electrónico
                </label>

                <div id="inputsEquipoElectronico" style="display: none">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca">

                    <label for="serial">Serial:</label>
                    <input type="text" id="serial" name="serial">
                </div>

                <!-- Campo para ingresar el número del carnet -->
                <label for="carnet">Ingrese el Número del Carnet:</label>
                <input type="text" name="carnet" id="carnet" placeholder="N° Carnet" required>

                <!-- Campo para ingresar la hora de ingreso -->
                <label for="ingreso">Hora de ingreso:</label>
                <input type="time" name="ingreso" id="ingreso" placeholder="Hora de ingreso" required>

                <!-- Botones de acción: enviar formulario o consultar registros -->
                <div class="buttons-container">
                    <input type="submit" value="Agregar">
                    <a href="View/tablavisitantes.php" class="btn-consulta">Consultar Registro</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Incluir script para el menú -->
    <script src="View/layout/menu.js"></script>
    <script>
        $(document).ready(function() {
            // ------------------------
            // Validar que el campo 'cedula' solo permita números
            // ------------------------
            $("#cedula").on("input", function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // ------------------------
            // Funcionalidad para autocompletar los campos con los datos existentes al ingresar la cédula
            // ------------------------
            $("#cedula").on("blur", function() {
                var cedula = $(this).val();
                if (cedula.length > 0) {
                    $.ajax({
                        url: "Controller/buscar_visitantes.php",
                        method: "POST",
                        data: {
                            cedula: cedula
                        },
                        dataType: "json",
                        success: function(response) {
                            if (!response.error) {
                                $("input[name='nombre']").val(response.nombre);
                                $("select[name='arl']").val(response.arl);
                                $("select[name='eps']").val(response.eps);
                                $("select[name='rh']").val(response.rh);
                                $("input[name='telefono']").val(response.telefono);
                                $("select[name='empresa']").val(response.empresa);
                                $("#motivo").val(response.motivo);
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'No encontrado',
                                    text: 'Usuario no encontrado en el sistema'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un error al consultar la base de datos'
                            });
                        }
                    });
                }
            });

            // Mostrar input si selecciona "OTRA"
            $("#empresa").change(function() {
                if ($(this).val() === "otra") {
                    $("#nuevaEmpresaContainer").show();
                    $("#nueva_empresa").prop("required", true);
                } else {
                    $("#nuevaEmpresaContainer").hide();
                    $("#nueva_empresa").prop("required", false);
                }
            });

            // ------------------------
            // Validación de fecha futura
            // ------------------------
            $("#fecha").on("change", function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // eliminar horas para comparar solo la fecha
                if (selectedDate > today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fecha inválida',
                        text: 'No se puede seleccionar una fecha futura.'
                    });
                    this.value = ""; // limpiar campo
                }
            });

            // ------------------------
            // Mostrar/Ocultar campos de equipo electrónico
            // ------------------------
            $("#equipoElectronicoCheckbox").change(function() {
                $("#inputsEquipoElectronico").toggle(this.checked);
            });

            // ------------------------
            // Mensajes SweetAlert desde PHP
            // ------------------------
            <?php if (isset($_SESSION['success'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '<?= $_SESSION['success'] ?>',
                    confirmButtonColor: '#3085d6'
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: '<?= $_SESSION['error'] ?>',
                    confirmButtonColor: '#d33'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>