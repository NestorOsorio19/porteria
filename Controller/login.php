<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* ==========================================================
   CONFIGURACIÓN
========================================================== */

require_once "../Config/config.php";
require_once "../Config/database.php";

/* ==========================================================
   CONEXIÓN PDO
========================================================== */

try {

    $connection = connection();

} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => "No fue posible conectar con la base de datos."
    ]);

    exit;
}

/* ==========================================================
   RECIBIR JSON
========================================================== */

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

/* ==========================================================
   VALIDAR JSON
========================================================== */

if (!is_array($data)) {

    echo json_encode([
        "success" => false,
        "message" => "No se recibieron datos válidos."
    ]);

    exit;
}

/* ==========================================================
   OBTENER USUARIO Y CONTRASEÑA
========================================================== */

$username = trim($data["username"] ?? "");
$password = $data["password"] ?? "";

/* ==========================================================
   VALIDAR CAMPOS
========================================================== */

if ($username === "" || $password === "") {

    echo json_encode([
        "success" => false,
        "message" => "Debe ingresar usuario y contraseña."
    ]);

    exit;
}

/* ==========================================================
   BUSCAR USUARIO + ROL
========================================================== */

try {

    $stmt = $connection->prepare("
        SELECT
            u.id_registro,
            u.nombre,
            u.cedula,
            u.username,
            u.password,
            u.id_rol,
            r.nombre_rol,
            r.descripcion
        FROM usuarios u
        INNER JOIN roles r
            ON u.id_rol = r.id_rol
        WHERE u.username = :username
        LIMIT 1
    ");

    $stmt->execute([
        ":username" => $username
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Error al consultar el usuario."
    ]);

    exit;
}

/* ==========================================================
   USUARIO NO EXISTE
========================================================== */

if (!$usuario) {

    echo json_encode([
        "success" => false,
        "message" => "Usuario no encontrado."
    ]);

    exit;
}

/* ==========================================================
   VERIFICAR CONTRASEÑA
========================================================== */

if (!password_verify($password, $usuario["password"])) {

    echo json_encode([
        "success" => false,
        "message" => "Contraseña incorrecta."
    ]);

    exit;
}

/* ==========================================================
   REGENERAR SESIÓN
========================================================== */

session_regenerate_id(true);

/* ==========================================================
   CREAR VARIABLES DE SESIÓN
========================================================== */

$_SESSION["autenticado"] = true;
$_SESSION["id_usuario"] = $usuario["id_registro"];
$_SESSION["nombre"] = $usuario["nombre"];
$_SESSION["cedula"] = $usuario["cedula"];
$_SESSION["usuario"] = $usuario["username"];
$_SESSION["id_rol"] = $usuario["id_rol"];
$_SESSION["role"] = $usuario["nombre_rol"];

/* ==========================================================
DEFINIR REDIRECCIÓN SEGÚN EL ROL
========================================================== */

$role = strtolower(trim($usuario["nombre_rol"]));

switch ($role) {

    case "super":
        $redirect = "View/menu_administrador.php";
        break;

    case "porteria1":
        $redirect = "View/menu_principal.php";
        break;

    case "porteria2":
        $redirect = "View/menu_principal.php";
        break;

    default:

        $_SESSION = [];
        session_destroy();

        echo json_encode([
            "success" => false,
            "message" => "El rol del usuario no tiene acceso al sistema."
        ]);

        exit;
}
$_SESSION["descripcion_rol"] = $usuario["descripcion"];

/* ==========================================================
   RESPUESTA AL VIEW
========================================================== */

echo json_encode([

    "success" => true,

    "message" => "Inicio de sesión exitoso.",

    "usuario" => $usuario["username"],

    "nombre" => $usuario["nombre"],

    "id_rol" => $usuario["id_rol"],

    "role" => $usuario["nombre_rol"],

    "descripcion_rol" => $usuario["descripcion"],

    "redirect" => $redirect

]);

exit;