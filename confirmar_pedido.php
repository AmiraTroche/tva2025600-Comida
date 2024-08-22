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
$montoPagado = isset($_POST['montoPagado']) ? floatval($_POST['montoPagado']) : 0;
$totalPedido = isset($_POST['totalPedido']) ? floatval($_POST['totalPedido']) : 0;

// Generar un número de pedido único
$numeroPedido = uniqid('PED');

// Insertar el pedido en la base de datos
$insertPedidoStmt = $conn->prepare("INSERT INTO pedidos (idUsuario, total, montoPagado, cambio) VALUES (?, ?, ?, ?)");
$cambio = $montoPagado - $totalPedido;

// Aquí se especifican los tipos para cada parámetro
$insertPedidoStmt->bind_param("idid", $idUsuario, $totalPedido, $montoPagado, $cambio);
$insertPedidoStmt->execute();
$insertPedidoStmt->close();

// Obtener los productos del carrito del usuario
$stmt = $conn->prepare("SELECT p.nombreProducto, c.cantidad, p.precio, (c.cantidad * p.precio) AS total FROM carrito c JOIN productos p ON c.idProducto = p.idProducto WHERE c.idUsuario = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

// Mostrar recibo
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pedido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Página Principal</a>
            <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <h1>Recibo de Pedido</h1>
        <p><strong>Número de Pedido:</strong> <?php echo htmlspecialchars($numeroPedido); ?></p>
        <p><strong>Nombre de Usuario:</strong> <?php echo htmlspecialchars($_SESSION['nombreUsuario']); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalRecibo = 0;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombreProducto']); ?></td>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td><?php echo number_format($row['precio'], 2); ?> BS</td>
                        <td><?php echo number_format($row['total'], 2); ?> BS</td>
                    </tr>
                    <?php $totalRecibo += $row['total']; ?>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p><strong>Total del Pedido:</strong> <?php echo number_format($totalPedido, 2); ?> BS</p>
        <p><strong>Monto Pagado:</strong> <?php echo number_format($montoPagado, 2); ?> BS</p>
        <p><strong>Cambio a Devolver:</strong> <?php echo number_format($cambio, 2); ?> BS</p>
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
