<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener productos
$productosStmt = $conn->prepare("SELECT idProducto, nombreProducto, precio FROM productos");
$productosStmt->execute();
$productosStmt->bind_result($idProducto, $nombreProducto, $precio);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Productos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Seleccionar Productos</h1>

    <form action="agregar_al_carrito.php" method="post">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($productosStmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($nombreProducto); ?></td>
                        <td><?php echo htmlspecialchars($precio); ?></td>
                        <td>
                            <input type="number" name="cantidad[<?php echo $idProducto; ?>]" min="1" required>
                            <input type="hidden" name="idProducto[<?php echo $idProducto; ?>]" value="<?php echo $idProducto; ?>">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit">Añadir al Carrito</button>
    </form>

    <?php
    $productosStmt->close();
    $conn->close();
    ?>
</body>
</html>
