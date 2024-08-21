<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario desde la sesión
$idUsuario = $_SESSION['idUsuario'];

// Consultar las cantidades actuales de los productos en el carrito
$carritoStmt = $conn->prepare("SELECT idProducto, SUM(cantidad) as cantidad FROM carrito WHERE idUsuario = ? GROUP BY idProducto");
$carritoStmt->bind_param("i", $idUsuario);
$carritoStmt->execute();
$carritoResult = $carritoStmt->get_result();

// Crear un array para almacenar las cantidades actuales en el carrito
$carritoCantidades = [];
while ($row = $carritoResult->fetch_assoc()) {
    $carritoCantidades[$row['idProducto']] = $row['cantidad'];
}
$carritoStmt->close();

// Consultar los productos disponibles
$productosStmt = $conn->prepare("SELECT idProducto, nombreProducto, precio FROM productos");
$productosStmt->execute();
$productosStmt->bind_result($idProducto, $nombreProducto, $precio);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente - Tienda de Comida</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
        <nav>
            <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </nav>
    </header>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombreUsuario']); ?></h1>
    
    <h2>Productos Disponibles</h2>
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
                            <input type="hidden" name="idProducto[]" value="<?php echo htmlspecialchars($idProducto); ?>">
                            <input type="number" name="cantidad[]" min="0" value="<?php echo isset($carritoCantidades[$idProducto]) ? htmlspecialchars($carritoCantidades[$idProducto]) : 0; ?>" required>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit">Añadir al Carrito</button>
    </form>

    <h2>Carrito</h2>
    <a href="carrito.php">Ver Carrito</a>

    <?php
    $productosStmt->close();
    $conn->close();
    ?>
</body>
</html>
