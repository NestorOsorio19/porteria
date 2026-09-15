<?php

session_start();

require_once '../Config/config.php';
require_once '../Config/database.php';

$connection = connection();

/* ==========================================================
SOLO POST
========================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/registro_usuarios.php");
    exit();
}

/* ==========================================================
RECIBIR DATOS
========================================================== */

$cedula  = trim($_POST['cedula'] ?? '');
$nombre  = trim($_POST['nombre'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

$id_rol = filter_input(
    INPUT_POST,
    'id_rol',
    FILTER_VALIDATE_INT
);

/* ==========================================================
VALIDAR CAMPOS OBLIGATORIOS
========================================================== */

if (
    $cedula === '' ||
    $nombre === '' ||
    $username === '' ||
    $password === '' ||
    $password_confirm === ''
) {

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('Todos los campos son obligatorios.')
    );

    exit();
}

/* ==========================================================
VALIDAR CONTRASEÑAS
========================================================== */

if ($password !== $password_confirm) {

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('Las contraseñas no coinciden.')
    );

    exit();
}

/* ==========================================================
VALIDAR CÉDULA
========================================================== */

if (!ctype_digit($cedula)) {

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('La cédula debe contener solo números.')
    );

    exit();
}

/* ==========================================================
VALIDAR USERNAME
========================================================== */

if (strlen($username) > 50) {

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('El usuario supera el límite permitido.')
    );

    exit();
}

/* ==========================================================
VALIDAR ROL
========================================================== */

if (!$id_rol) {

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('Debe seleccionar un rol.')
    );

    exit();
}

/* ==========================================================
VALIDAR CONTRASEÑA
========================================================== */

if (strlen($password) < 6) {

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('La contraseña debe tener mínimo 6 caracteres.')
    );

    exit();
}

try {

    /* ======================================================
    VERIFICAR ROL
    ====================================================== */

    $stmtRol = $connection->prepare("
        SELECT id_rol
        FROM roles
        WHERE id_rol = :id_rol
        LIMIT 1
    ");

    $stmtRol->execute([
        ':id_rol' => $id_rol
    ]);

    if (!$stmtRol->fetch(PDO::FETCH_ASSOC)) {

        header(
            "Location: ../View/registro_usuarios.php?error=" .
                urlencode('El rol seleccionado no existe.')
        );

        exit();
    }

    /* ======================================================
    VALIDAR USUARIO EXISTENTE
    ====================================================== */

    $stmtExiste = $connection->prepare("
        SELECT
            id_registro,
            cedula,
            username
        FROM usuarios
        WHERE cedula = :cedula
        OR username = :username
        LIMIT 1
    ");

    $stmtExiste->execute([
        ':cedula' => $cedula,
        ':username' => $username
    ]);

    $usuarioExiste =
        $stmtExiste->fetch(PDO::FETCH_ASSOC);

    if ($usuarioExiste) {

        if (
            (string)$usuarioExiste['cedula']
            ===
            (string)$cedula
        ) {

            $mensaje =
                'La cédula ya está registrada';
        } else {

            $mensaje =
                'El nombre de usuario ya existe';
        }

        header(
            "Location: ../View/registro_usuarios.php?error=" .
                urlencode($mensaje)
        );

        exit();
    }

    /* ======================================================
    ENCRIPTAR CONTRASEÑA
    ====================================================== */

    $passwordHash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    /* ======================================================
    INSERTAR USUARIO
    ====================================================== */

    $stmtInsert = $connection->prepare("
        INSERT INTO usuarios (
            cedula,
            nombre,
            username,
            password,
            id_rol
        )
        VALUES (
            :cedula,
            :nombre,
            :username,
            :password,
            :id_rol
        )
    ");

    $stmtInsert->execute([
        ':cedula'       => $cedula,
        ':nombre'       => $nombre,
        ':username'     => $username,
        ':password'     => $passwordHash,
        ':id_rol'       => $id_rol
    ]);

    header(
        "Location: ../View/registro_usuarios.php?guardado=1"
    );

    exit();
} catch (PDOException $e) {

    error_log(
        'Error al registrar usuario: ' .
            $e->getMessage()
    );

    header(
        "Location: ../View/registro_usuarios.php?error=" .
            urlencode('Ocurrió un error al registrar el usuario.')
    );

    exit();
}
