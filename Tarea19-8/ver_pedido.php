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

$idUsuario = $_SESSION['idUsuario'];

// Obtener los productos en el carrito del usuario
$carritoStmt = $conn->prepare("SELECT p.nombreProducto, c.cantidad, c.precioTotal FROM carrito c JOIN productos p ON c.idProducto = p.idProducto WHERE c.idUsuario = ?");
$carritoStmt->bind_param("i", $idUsuario);
$carritoStmt->execute();
$carritoStmt->bind_result($nombreProducto, $cantidad, $precioTotal);

$totalPedido = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen del Pedido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Resumen del Pedido</h1>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($carritoStmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($nombreProducto); ?></td>
                    <td><?php echo htmlspecialchars($cantidad); ?></td>
                    <td><?php echo htmlspecialchars($precioTotal); ?></td>
                </tr>
                <?php $totalPedido += $precioTotal; ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Total: <?php echo $totalPedido; ?></h2>

    <form action="confirmar_pedido.php" method="post">
        <button type="submit">Confirmar Pedido</button>
    </form>

    <?php
    $carritoStmt->close();
    $conn->close();
    ?>
</body>
</html>
