<?php
require_once "./connection.php";


// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Redirige si no está autenticado
    exit();
}

// Obtener el ID del usuario a seguir
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userIdToFollow = intval($_GET['id']);
    $followerUserId = $_SESSION['id'];

    // Consulta para insertar en la tabla de seguimiento
    $sql = "INSERT INTO social_network.follows (users_id, userToFollowId) VALUES ($followerUserId, $userIdToFollow)";

    if (mysqli_query($connect, $sql)) {
        // Redirigir de vuelta a la página de usuarios
        header("Location: ../pages/usuarios.php?mensaje=Usuario seguido con éxito");
    } else {
        echo "Error al seguir al usuario: " . mysqli_error($connect);
    }
} else {
    // Redirigir de vuelta si no se proporciona un ID válido
    header("Location: ../pages/usuarios.php?mensaje=ID de usuario no válido");
    exit(); 
}
?>
