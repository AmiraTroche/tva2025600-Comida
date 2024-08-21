<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT idUsuario, nombreUsuario, password, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombreUsuario, $hashed_password, $rol);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['idUsuario'] = $id;
            $_SESSION['nombreUsuario'] = $nombreUsuario;
            $_SESSION['rol'] = $rol;

            // Redirigir al usuario según su rol
            if ($rol == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: cliente.php");
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El correo no existe.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
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
        <h1>Iniciar Sesión</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div>
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Comida. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
