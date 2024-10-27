<?php
require_once "../scripts/connection.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

$idUsuario = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id']; // ID del usuario cuyos seguidos se mostrarán

try {
    // Obtener el nombre del usuario a través de su ID
    $stmt = $pdo->prepare("SELECT username FROM social_network.users WHERE id = :id");
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Usuario no encontrado.");
    }

    $userName = $user['username'];

    // Obtener la lista de personas seguidas por el usuario junto con sus descripciones
    $sqlSeguidas = "SELECT u.id, u.username, u.description
                    FROM social_network.follows f
                    JOIN social_network.users u ON f.userToFollowId = u.id
                    WHERE f.users_id = :idUsuario";
    $stmtSeguidas = $pdo->prepare($sqlSeguidas);
    $stmtSeguidas->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmtSeguidas->execute();
    $seguidos = $stmtSeguidas->fetchAll(PDO::FETCH_ASSOC);

    // Procesar dejar de seguir usuarios
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfollow_user'])) {
        $unfollowUserId = (int)$_POST['unfollow_user']; // ID del usuario a dejar de seguir
        $deleteFollow = "DELETE FROM social_network.follows WHERE users_id = :userId AND userToFollowId = :unfollowUserId";
        $stmtDeleteFollow = $pdo->prepare($deleteFollow);
        $stmtDeleteFollow->bindParam(':userId', $idUsuario, PDO::PARAM_INT);
        $stmtDeleteFollow->bindParam(':unfollowUserId', $unfollowUserId, PDO::PARAM_INT);
        $stmtDeleteFollow->execute();
        
        // Redirigir a la página de seguimientos, manteniendo el parámetro 'from'
        header("Location: seguimientos.php?id=$idUsuario&from=can_unfollow"); 
        exit;
    }

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios que sigues</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">

        <h1 class="text-3xl font-bold text-center text-[#1DA1F2] mb-6">Usuarios que sigue <?php echo htmlspecialchars($userName); ?></h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($seguidos)): ?>
                <?php foreach ($seguidos as $seguido): ?>
                    <div class="bg-white shadow-md rounded-lg p-4 border-none">
                        <h2 class="text-xl font-semibold text-[#1DA1F2] mb-2">
                            <a href="perfil.php?id=<?php echo htmlspecialchars($seguido['id']); ?>">
                                <?php echo htmlspecialchars($seguido['username']); ?>
                            </a>
                        </h2>
                        <p class="text-gray-700"><?php echo htmlspecialchars($seguido['description'] ?? 'No hay descripción disponible.'); ?></p>

                        <!-- Mostrar botón de dejar de seguir solo si se accede desde "Puedo dejar de seguir a..." -->
                        <?php if (isset($_GET['from']) && $_GET['from'] === 'can_unfollow'): ?>
                            <form method="POST" action="seguimientos.php?id=<?php echo htmlspecialchars($idUsuario); ?>" class="mt-2 border-none flex justify-center">
                                <input type="hidden" name="unfollow_user" value="<?php echo htmlspecialchars($seguido['id']); ?>">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-1 px-2 rounded transition duration-300">
                                    Dejar de Seguir
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">No hay usuarios que seguir aún.</p>
            <?php endif; ?>
        </div>


        <div class="mt-6 flex justify-center gap-4">
            <a href="./main.php" class="bg-gray-600 hover:bg-gray-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a la página principal</a>
            <a href="./perfil.php?id=<?php echo htmlspecialchars($idUsuario); ?>" class="bg-blue-600 hover:bg-blue-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a perfil</a>
            <a href="../scripts/logout.php" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>




