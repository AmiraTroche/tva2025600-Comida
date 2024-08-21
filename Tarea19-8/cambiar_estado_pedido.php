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

// Obtener parámetros de la URL
$idPedido = $_GET['id'] ?? '';
$estado = $_GET['estado'] ?? '';

// Validar el estado
$estados_validos = ['pagado', 'cancelado'];
if (!in_array($estado, $estados_validos) || empty($idPedido)) {
    die("Estado no válido.");
}

// Actualizar el estado del pedido
$stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE idPedido = ?");
$stmt->bind_param('si', $estado, $idPedido);

if ($stmt->execute()) {
    header("Location: admin.php"); // Redirigir al panel de administración
} else {
    echo "Error al actualizar el estado del pedido: " . $conn->error;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
