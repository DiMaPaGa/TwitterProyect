<?php
require_once "./connection.php";

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Redirige si no está autenticado
    exit();
}

try {
    // Obtener el ID del usuario a seguir
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $userIdToFollow = intval($_GET['id']);
        $followerUserId = $_SESSION['id'];

        // Consulta para insertar en la tabla de seguimiento
        $sql = "INSERT INTO social_network.follows (users_id, userToFollowId) VALUES (:followerUserId, :userIdToFollow)";
        
        // Preparar la consulta
        $stmt = $pdo->prepare($sql);
        // Vincular los parámetros
        $stmt->bindParam(':followerUserId', $followerUserId, PDO::PARAM_INT);
        $stmt->bindParam(':userIdToFollow', $userIdToFollow, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir de vuelta a la página de usuarios con mensaje de éxito
            header("Location: ../pages/usuarios.php?mensaje=Usuario seguido con éxito");
            exit();
        } else {
            // En caso de error en la inserción
            echo "Error al seguir al usuario.";
        }
    } else {
        // Redirigir de vuelta si no se proporciona un ID válido
        header("Location: ../pages/usuarios.php?mensaje=ID de usuario no válido");
        exit();
    }
} catch (PDOException $e) {
    // Manejar cualquier error de la base de datos
    echo "Error en la base de datos: " . $e->getMessage();
}
?>
