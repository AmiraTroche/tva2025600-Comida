<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Comida</title>
    <link rel="stylesheet" href="style.css"> <!-- Archivo CSS para estilos -->
</head>
<body>
    <!-- Encabezado con botones de inicio de sesión y registro -->
    <header>
        <nav>
            <a href="index.php">Página Principal</a>
            <a href="login.php">Iniciar Sesión</a>
            <a href="register.php">Registrarse</a>
        </nav>
    </header>

    <!-- Sección de productos -->
    <section>
        <h1>Productos Disponibles</h1>
        <div class="product-container">
            <?php
            // Conexión a la base de datos
            $conn = new mysqli('localhost', 'root', '', 'SistemaPedidos');

            // Verificar la conexión
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Consulta para obtener los productos
            $sql = "SELECT idProducto, nombreProducto, descripcion, precio FROM productos";
            $result = $conn->query($sql);

            // Verificar si hay productos
            if ($result->num_rows > 0) {
                // Mostrar cada producto
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    echo '<h2>' . $row["nombreProducto"] . '</h2>';
                    echo '<p>' . $row["descripcion"] . '</p>';
                    echo '<p><strong>Precio: BS' . $row["precio"] . '</strong></p>';
                    echo '<form action="agregar_al_carrito.php" method="post">
    <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
    <button type="submit">Agregar al Carrito</button>
</form>
';
                    echo '</div>';
                }
            } else {
                echo "<p>No hay productos disponibles en este momento.</p>";
            }

            // Cerrar la conexión
            $conn->close();
            ?>
        </div>
    </section>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 Tienda de Comida. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
