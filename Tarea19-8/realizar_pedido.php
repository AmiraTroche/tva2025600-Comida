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

// Consultar los productos en el carrito del usuario
$stmt = $conn->prepare("SELECT p.nombreProducto, c.cantidad, p.precio, (c.cantidad * p.precio) AS total FROM carrito c JOIN productos p ON c.idProducto = p.idProducto WHERE c.idUsuario = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

// Calcular el total del pedido
$pedidoTotal = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pedido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Página Principal</a>
            <a href="carrito.php">Carrito</a>
            <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <h1>Confirmar Pedido</h1>

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
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombreProducto']); ?></td>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td><?php echo number_format($row['precio'], 2); ?>BS</td>
                        <td><?php echo number_format($row['total'], 2); ?> BS</td>
                    </tr>
                    <?php $pedidoTotal += $row['total']; ?>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Total del Pedido: <?php echo number_format($pedidoTotal, 2); ?> BS</h2>

        <!-- Formulario para ingresar el monto con el que se pagará -->
        <form id="paymentForm" action="confirmar_pedido.php" method="post">
            <label for="montoPagado">Monto con el que pagará:</label>
            <input type="number" id="montoPagado" name="montoPagado" min="0" step="0.01" required>
            <input type="hidden" name="totalPedido" value="<?php echo number_format($pedidoTotal, 2); ?>">

            <button type="submit">Confirmar Pedido</button>
        </form>

        <!-- Sección para mostrar el cambio -->
        <div id="cambio" style="margin-top: 20px; display: none;">
            <h2>Cambio a devolver:</h2>
            <p id="cambioMonto"></p>
        </div>

        <script>
            document.getElementById('paymentForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Evitar el envío automático del formulario

                var montoPagado = parseFloat(document.getElementById('montoPagado').value);
                var totalPedido = parseFloat(document.querySelector('input[name="totalPedido"]').value);

                if (montoPagado >= totalPedido) {
                    var cambio = montoPagado - totalPedido;
                    document.getElementById('cambio').style.display = 'block';
                    document.getElementById('cambioMonto').textContent = cambio.toFixed(2) + ' BS';
                    this.submit(); // Enviar el formulario si el monto es suficiente
                } else {
                    alert('El monto pagado debe ser igual o mayor que el total del pedido.');
                }
            });
        </script>

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
