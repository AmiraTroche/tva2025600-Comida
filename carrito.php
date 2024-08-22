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

// Consultar los productos en el carrito del usuario, asegurando que cada producto se muestre solo una vez
$stmt = $conn->prepare("SELECT p.idProducto, p.nombreProducto, SUM(c.cantidad) as cantidad FROM carrito c JOIN productos p ON c.idProducto = p.idProducto WHERE c.idUsuario = ? GROUP BY p.idProducto, p.nombreProducto");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

$totalCantidad = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carrito</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Página Principal</a>
            <a href="cliente.php">Volver</a>
            <a href="realizar_pedido.php">Realizar Pedido</a>
            <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <h1>Contenido del Carrito</h1>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombreProducto']); ?></td>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                    </tr>
                    <?php $totalCantidad += $row['cantidad']; ?>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Total Productos en el Carrito: <?php echo $totalCantidad; ?></h2>

        <!-- Botón de Agregar Más y Realizar Pedido -->
        <form action="cliente.php" method="post">
            <button type="submit">Agregar Más Productos</button>
        </form>
        <h2>Realizar Pedido</h2>
        <form action="realizar_pedido.php" method="post">
            <button type="submit">Realizar Pedido</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Comida. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>