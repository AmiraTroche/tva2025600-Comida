<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $idProducto = $_GET['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];

        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ? WHERE idProducto = ?");
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $idProducto);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $stmt = $conn->prepare("SELECT nombreProducto, descripcion, precio FROM productos WHERE idProducto = ?");
        $stmt->bind_param("i", $idProducto);
        $stmt->execute();
        $stmt->bind_result($nombre, $descripcion, $precio);
        $stmt->fetch();
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Editar Producto</h1>

    <form action="editar_producto.php?id=<?php echo $idProducto; ?>" method="post">
        <div>
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
        </div>
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo $descripcion; ?></textarea>
        </div>
        <div>
            <label for="precio">Precio:</label>
            <input type="number" step="0.01" id="precio" name="precio" value="<?php echo $precio; ?>" required>
        </div>
        <button type="submit">Guardar Cambios</button>
    </form>

    <a href="admin.php">Volver al Panel de Administración</a>
</body>
</html>