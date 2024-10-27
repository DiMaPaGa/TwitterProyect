<?php
require_once "../scripts/connection.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

$idUsuario = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id']; // ID del usuario cuyos seguidores se mostrarán

// Verificar si se accede desde "Puedo dejar de seguir a..."
$fromSigo = isset($_GET['from']) && $_GET['from'] === 'sigo';

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

    // Obtener la lista de seguidores junto con sus descripciones
    $sqlSeguidores = "SELECT u.id, u.username, u.description
                      FROM social_network.follows f
                      JOIN social_network.users u ON f.users_id = u.id
                      WHERE f.userToFollowId = :idUsuario";
    $stmtSeguidores = $pdo->prepare($sqlSeguidores);
    $stmtSeguidores->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmtSeguidores->execute();
    $seguidores = $stmtSeguidores->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Manejo de dejar de seguir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfollow_id'])) {
    $unfollowId = (int)$_POST['unfollow_id'];

    try {
        $sqlUnfollow = "DELETE FROM social_network.follows WHERE users_id = :idUsuario AND userToFollowId = :unfollowId";
        $stmtUnfollow = $pdo->prepare($sqlUnfollow);
        $stmtUnfollow->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmtUnfollow->bindParam(':unfollowId', $unfollowId, PDO::PARAM_INT);
        $stmtUnfollow->execute();

        // Redireccionar después de dejar de seguir
        header("Location: seguidores.php?id=" . $idUsuario . "&from=sigo");
        exit;

    } catch (PDOException $e) {
        echo "Error al dejar de seguir: " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguidores de <?php echo htmlspecialchars($userName); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-[#1DA1F2] mb-6">Seguidores de <?php echo htmlspecialchars($userName); ?></h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($seguidores)): ?>
                <?php foreach ($seguidores as $seguidor): ?>
                    <div class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-[#1DA1F2] mb-2">
                                <a href="perfil.php?id=<?php echo htmlspecialchars($seguidor['id']); ?>">
                                    <?php echo htmlspecialchars($seguidor['username']); ?>
                                </a>
                            </h2>
                            <p class="text-gray-700"><?php echo htmlspecialchars($seguidor['description'] ?? 'No hay descripción disponible.'); ?></p>
                        </div>
                        <?php if ($fromSigo): ?> <!-- Mostrar el botón solo si se accede desde "Puedo dejar de seguir a..." -->
                            <form action="" method="POST">
                                <input type="hidden" name="unfollow_id" value="<?php echo htmlspecialchars($seguidor['id']); ?>">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-1 px-3 rounded transition duration-300">Dejar de seguir</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">No hay seguidores aún para mostrar.</p>
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





