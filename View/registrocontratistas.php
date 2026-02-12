<?php
session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

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
    <title>Registro de Contratistas</title>
    <link href="../View/CSS/estilosprincipales.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include("layout/header.html"); ?>

    <div class="main-content">
        <div class="users-form">
            <h1>Registro de Contratistas</h1>

            <form action="../Controller/ingreso_contratistas.php" method="POST">
                <!-- Fecha -->
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" required>

                <!-- Cédula -->
                <label for="cedula">Cédula:</label>
                <input type="text" name="cedula" id="cedula" placeholder="Cédula" required>

                <!-- Nombre -->
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre" required>

                <!-- Tipo de Sangre -->
                <label for="rh">Tipo de Sangre:</label>
                <select name="rh" id="rh" required>
                    <option value="" disabled selected>Seleccione el tipo de sangre</option>
                    <option value="O-">O -</option>
                    <option value="O+">O +</option>
                    <option value="A-">A -</option>
                    <option value="A+">A +</option>
                    <option value="B-">B -</option>
                    <option value="B+">B +</option>
                    <option value="AB-">AB -</option>
                    <option value="AB+">AB +</option>
                </select>

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

                <!-- Empresa -->
                <label for="empresa">Empresa a la que Pertenece:</label>
                <select name="empresa" id="empresa" required>
                    <option value="" disabled selected>Seleccione la Empresa...</option>
                    <?= $empresas ?>
                </select>

                <!-- Enfermedades o Alergias -->
                <label for="enfermedad_alergia">Enfermedades o alergias:</label>
                <textarea name="enfermedad_alergia" id="enfermedad_alergia" placeholder="Descríbalas acá..." required></textarea>

                <!-- Contacto de Emergencia -->
                <label for="nombre_emergencia">Contacto en caso de emergencia (Nombre):</label>
                <input type="text" name="nombre_emergencia" id="nombre_emergencia" placeholder="Nombre" required>

                <label for="telefono_emergencia">Teléfono de emergencia:</label>
                <input type="text" name="telefono_emergencia" id="telefono_emergencia" placeholder="Teléfono" required>

                <!-- Inducción SG-SST -->
                <label for="induccion_sgsst">Recibió inducción de SG-SST:</label>
                <select name="induccion_sgsst" id="induccion_sgsst" required>
                    <option value="" disabled selected>Seleccione su respuesta</option>
                    <option value="1">SI</option>
                    <option value="0">NO</option>
                </select>

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

                <!-- Hora de Ingreso -->
                <label for="ingreso">Hora de ingreso:</label>
                <input type="time" name="ingreso" id="ingreso" required>

                <div class="buttons-container">
                    <input type="submit" value="Agregar">
                    <a href="../View/tablacontratistas.php" class="btn-consulta">Consultar Registro</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Incluir script para el menú -->
    <script src="layout/menu.js"></script>
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
                        url: "../Controller/buscar_contratistas.php",
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
                                $("input[name='nombre_emergencia']").val(response.nombre_emergencia);
                                $("input[name='telefono_emergencia']").val(response.telefono_emergencia);
                                $("select[name='rh']").val(response.rh);
                                $("input[name='ingreso']").val(response.ingreso);
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