<?php
require_once "../scripts/connection.php";

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Redirige si no está autenticado
    exit();
}

// ID del usuario actual
$userId = $_SESSION['id'];

try {
    // Consulta para obtener usuarios que no están siendo seguidos por el usuario actual
    $sql = "
        SELECT u.id, u.username, u.description
        FROM social_network.users u
        WHERE u.id != :userId
        AND u.id NOT IN (
            SELECT userToFollowId FROM social_network.follows WHERE users_id = :userId
        )";
    
    // Preparar la consulta
    $stmt = $pdo->prepare($sql);
    // Vincular el parámetro :userId
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    // Ejecutar la consulta
    $stmt->execute();
    // Obtener todos los resultados
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios a seguir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col items-center p-6">

    <!-- Título -->
    <h1 class="text-3xl font-bold text-[#1DA1F2] mb-6">Personas a las que puedes seguir</h1>

    <!-- Mensaje de éxito -->
    <?php if (isset($_GET['mensaje'])): ?>
        <div id="mensaje-exito" class="bg-green-100 text-green-700 p-4 mb-4 w-full max-w-md text-center rounded-md">
            <p><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
        </div>

        <script>
        // Desaparecer mensaje tras 5 segundos
        setTimeout(function() {
            let mensaje = document.getElementById('mensaje-exito');
            if (mensaje) {
                mensaje.style.display = 'none';
            }
        }, 5000); // 5000 ms = 5 segundos
    </script>

    <?php endif; ?>

    <!-- Lista de usuarios a seguir -->
    <?php if (!empty($usuarios)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-4xl">
            <?php foreach ($usuarios as $user): ?>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-[#1DA1F2] mb-2">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </h2>
                    <p class="text-gray-700 mb-4">
                        <?php echo htmlspecialchars($user['description'] ?? 'No hay descripción disponible.'); ?>
                    </p>
                    <a href="../scripts/seguir.php?id=<?php echo htmlspecialchars($user['id']); ?>"
                       class="bg-[#1DA1F2] hover:bg-[#1A91DA] text-gray-50 font-semibold py-2 px-4 rounded transition duration-300 block text-center">
                        Seguir
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 text-lg">No hay usuarios disponibles para seguir.</p>
    <?php endif; ?>

    <div class="mt-6 flex justify-center gap-4">
            <a href="./main.php" class="bg-gray-600 hover:bg-gray-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a la página principal</a>
            <a href="./perfil.php?id=<?php echo htmlspecialchars($userId); ?>" class="bg-blue-600 hover:bg-blue-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a perfil</a>
            <a href="../scripts/logout.php" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Cerrar sesión</a>
    </div>

</body>
</html>

