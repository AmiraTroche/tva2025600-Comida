<?php
// Iniciar sesión si aún no se ha hecho
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Si se está utilizando una cookie de sesión, eliminarla
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión o página principal
header("Location: login.php");
exit();
?>
