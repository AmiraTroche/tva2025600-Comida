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

// Obtener los datos del formulario
$idProductos = $_POST['idProducto'];
$cantidades = $_POST['cantidad'];

foreach ($idProductos as $index => $idProducto) {
    $cantidad = intval($cantidades[$index]);

    if ($cantidad > 0) {
        // Verificar si el producto ya está en el carrito
        $checkStmt = $conn->prepare("SELECT cantidad FROM carrito WHERE idUsuario = ? AND idProducto = ?");
        $checkStmt->bind_param("ii", $idUsuario, $idProducto);
        $checkStmt->execute();
        $checkStmt->bind_result($existingQuantity);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($existingQuantity !== null) {
            // Actualizar la cantidad del producto existente
            $updateStmt = $conn->prepare("UPDATE carrito SET cantidad = ?, precioTotal = (SELECT precio FROM productos WHERE idProducto = ?) * ? WHERE idUsuario = ? AND idProducto = ?");
            $updateStmt->bind_param("idiii", $cantidad, $idProducto, $cantidad, $idUsuario, $idProducto);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insertar nuevo producto en el carrito
            $precioStmt = $conn->prepare("SELECT precio FROM productos WHERE idProducto = ?");
            $precioStmt->bind_param("i", $idProducto);
            $precioStmt->execute();
            $precioStmt->bind_result($precio);
            $precioStmt->fetch();
            $precioStmt->close();

            $precioTotal = $precio * $cantidad;

            $insertStmt = $conn->prepare("INSERT INTO carrito (idUsuario, idProducto, cantidad, precioTotal) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("iiid", $idUsuario, $idProducto, $cantidad, $precioTotal);
            $insertStmt->execute();
            $insertStmt->close();
        }
    } else {
        // Eliminar el producto del carrito si la cantidad es 0 o menor
        $deleteStmt = $conn->prepare("DELETE FROM carrito WHERE idUsuario = ? AND idProducto = ?");
        $deleteStmt->bind_param("ii", $idUsuario, $idProducto);
        $deleteStmt->execute();
        $deleteStmt->close();
    }
}

$conn->close();

// Redirigir al carrito después de agregar productos
header("Location: carrito.php");
exit();
?>
