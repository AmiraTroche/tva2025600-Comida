<?php
session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar los pedidos pendientes
$pedidos_query = "SELECT p.idPedido, p.fechaPedido, u.nombreUsuario, p.total, p.estado, p.montoPagado, p.cambio
                  FROM pedidos p 
                  JOIN usuarios u ON p.idUsuario = u.idUsuario 
                  WHERE p.estado = 'pendiente'";
$pedidos_result = $conn->query($pedidos_query);

// Consultar los productos
$productos_query = "SELECT * FROM productos";
$productos_result = $conn->query($productos_query);

// Verificar si la consulta de productos fue exitosa
if (!$productos_result) {
    die("Error al consultar productos: " . $conn->error);
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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
        <h1>Panel de Administración</h1>

        <section>
            <h2>Pedidos Pendientes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Monto Pagado</th>
                        <th>Cambio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $pedidos_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $pedido['idPedido']; ?></td>
                            <td><?php echo $pedido['fechaPedido']; ?></td>
                            <td><?php echo $pedido['nombreUsuario']; ?></td>
                            <td><?php echo number_format($pedido['total'], 2); ?> BS</td>
                            <td><?php echo number_format($pedido['montoPagado'], 2); ?> BS</td>
                            <td><?php echo number_format($pedido['cambio'], 2); ?> BS</td>
                            <td><?php echo ucfirst($pedido['estado']); ?></td>
                            <td>
                                <a href="cambiar_estado_pedido.php?id=<?php echo $pedido['idPedido']; ?>&estado=pagado" class="btn">Marcar como Pagado</a>
                                <a href="cambiar_estado_pedido.php?id=<?php echo $pedido['idPedido']; ?>&estado=cancelado" class="btn">Marcar como Cancelado</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Gestión de Productos</h2>
            <a href="crear_producto.php" class="btn">Crear Nuevo Producto</a>

            <table>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($productos_result): ?>
                        <?php while ($producto = $productos_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $producto['idProducto']; ?></td>
                                <td><?php echo $producto['nombreProducto']; ?></td>
                                <td><?php echo $producto['descripcion']; ?></td>
                                <td><?php echo number_format($producto['precio'], 2); ?> BS</td>
                                <td>
                                    <a href="editar_producto.php?id=<?php echo $producto['idProducto']; ?>" class="btn">Editar</a>
                                    <a href="eliminar_producto.php?id=<?php echo $producto['idProducto']; ?>" class="btn" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay productos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tienda de Comida. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
