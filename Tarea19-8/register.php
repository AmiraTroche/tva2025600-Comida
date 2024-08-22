<?php
// Iniciar sesión para manejar mensajes de error y éxito
session_start();

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para almacenar mensajes de error y éxito
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar datos del formulario
    $nombreUsuario = $_POST['nombreUsuario'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validaciones básicas
    if (empty($nombreUsuario) || empty($correo) || empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de correo electrónico no válido.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Encriptar la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Preparar la sentencia SQL para insertar un nuevo usuario (tipo cliente)
        $stmt = $conn->prepare("INSERT INTO usuarios (nombreUsuario, correo, password, rol) VALUES (?, ?, ?, 'cliente')");
        $stmt->bind_param("sss", $nombreUsuario, $correo, $password_hash);

        // Ejecutar y verificar si la inserción fue exitosa
        if ($stmt->execute()) {
            $success = "Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
        } else {
            $error = "Error en el registro: " . $stmt->error;
        }

        // Cerrar la sentencia
        $stmt->close();
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Cliente</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Página Principal</a>
            <a href="login.php">Iniciar Sesión</a>
            <a href="register.php">Registrarse</a>
        </nav>
    </header>

    <main>
        <h1>Registro de Usuario</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="register.php" method="post">
            <div>
                <label for="nombreUsuario">Nombre de Usuario:</label>
                <input type="text" id="nombreUsuario" name="nombreUsuario" required>
            </div>
            <div>
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Comida. Todos los derechos reservados.</p>
    </footer>
</body>
</html>