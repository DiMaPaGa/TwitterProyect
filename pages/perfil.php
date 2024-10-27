<?php
require_once '../scripts/connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Obtener el ID del usuario que se está viendo el perfil
$profileUserId = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];
$userId = $_SESSION['id'];

// Obtener la información del usuario del perfil
$queryProfileUser = "SELECT * FROM social_network.users WHERE id = :profileUserId";
$stmtProfileUser = $pdo->prepare($queryProfileUser);
$stmtProfileUser->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtProfileUser->execute();
$profileUser = $stmtProfileUser->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    header("Location: index.php"); // Redirigir si no existe el usuario
    exit;
}

// Contar los seguidores
$countFollowersQuery = "SELECT COUNT(*) as followerCount FROM social_network.follows WHERE userToFollowId = :profileUserId";
$stmtFollowers = $pdo->prepare($countFollowersQuery);
$stmtFollowers->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtFollowers->execute();
$rowFollowers = $stmtFollowers->fetch(PDO::FETCH_ASSOC);
$followerCount = $rowFollowers['followerCount'];

// Contar los seguidos
$countFollowingQuery = "SELECT COUNT(*) as followingCount FROM social_network.follows WHERE users_id = :profileUserId";
$stmtFollowing = $pdo->prepare($countFollowingQuery);
$stmtFollowing->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtFollowing->execute();
$rowFollowing = $stmtFollowing->fetch(PDO::FETCH_ASSOC);
$followingCount = $rowFollowing['followingCount'];

// Procesar el seguimiento de usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['follow_user'])) {
    $followUserId = $_POST['follow_user'];
    $insertFollow = "INSERT INTO social_network.follows (users_id, userToFollowId) VALUES (:userId, :followUserId)";
    $stmtInsertFollow = $pdo->prepare($insertFollow);
    $stmtInsertFollow->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtInsertFollow->bindParam(':followUserId', $followUserId, PDO::PARAM_INT);
    $stmtInsertFollow->execute();
    header("Location: perfil.php?id=$followUserId"); // Redirigir a la página de perfil del usuario seguido
    exit;
}

// Procesar dejar de seguir usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfollow_user'])) {
    $unfollowUserId = $_POST['unfollow_user'];
    $deleteFollow = "DELETE FROM social_network.follows WHERE users_id = :userId AND userToFollowId = :unfollowUserId";
    $stmtDeleteFollow = $pdo->prepare($deleteFollow);
    $stmtDeleteFollow->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtDeleteFollow->bindParam(':unfollowUserId', $unfollowUserId, PDO::PARAM_INT);
    $stmtDeleteFollow->execute();
    header("Location: perfil.php?id=$unfollowUserId"); // Redirigir a la página de perfil del usuario no seguido
    exit;
}

// Comprobar si el usuario actual sigue al usuario del perfil
$isFollowing = false;
$checkFollow = "SELECT * FROM social_network.follows WHERE users_id = :userId AND userToFollowId = :profileUserId";
$stmtCheckFollow = $pdo->prepare($checkFollow);
$stmtCheckFollow->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmtCheckFollow->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtCheckFollow->execute();
if ($stmtCheckFollow->rowCount() > 0) {
    $isFollowing = true;
}

// Procesar la actualización de la descripción del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_description'])) {
    $newDescription = trim($_POST['description']);
    
    // Actualizar la descripción solo si es el usuario logueado
    if ($userId == $profileUserId && !empty($newDescription)) {
        $updateDescription = "UPDATE social_network.users SET description = :description WHERE id = :userId";
        $stmtUpdateDescription = $pdo->prepare($updateDescription);
        $stmtUpdateDescription->bindParam(':description', $newDescription);
        $stmtUpdateDescription->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtUpdateDescription->execute();
        header("Location: perfil.php?id=$userId"); // Redirigir a la misma página para ver el cambio
        exit;
    }
}

// Verificar si se accede desde "Puedo dejar de seguir a..."
$fromSigo = isset($_GET['from']) && $_GET['from'] === 'sigo';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($profileUser['username']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-6">
        <!-- Información del usuario -->
        <div class="bg-white shadow-lg rounded-lg p-8 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-[#1DA1F2]"><?php echo htmlspecialchars($profileUser['username']); ?></h1>
                <p class="text-lg text-gray-700 mt-2 font-semibold"><?php echo htmlspecialchars($profileUser['description'] ?? ''); ?></p>
            </div>
            
            <div class="ml-4 text-right">
                <p class="text-gray-700 font-semibold">Seguidores: <?php echo htmlspecialchars($followerCount); ?></p>
                <p class="text-gray-700 font-semibold">Siguiendo: <?php echo htmlspecialchars($followingCount); ?></p>
            </div>

            <!-- Botón de seguimiento o dejar de seguir solo si no es el propio perfil -->
            <?php if ($profileUserId != $userId): ?>
                <form method="POST" action="perfil.php?id=<?php echo $profileUserId; ?>" class="mt-4">
                    <?php if ($isFollowing): ?>
                        <input type="hidden" name="unfollow_user" value="<?php echo $profileUserId; ?>">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Dejar de Seguir</button>
                    <?php else: ?>
                        <input type="hidden" name="follow_user" value="<?php echo $profileUserId; ?>">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Seguir</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>

        <!-- Enlaces adicionales para gestionar relaciones como botón -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="seguimientos.php?id=<?php echo $profileUserId; ?>" class="bg-[#1DA1F2] hover:bg-[#1A91DA] text-gray-50 font-semibold py-3 rounded-lg text-center transition duration-300">Sigo a...</a>
            <a href="seguidores.php?id=<?php echo $profileUserId; ?>&type=followers" class="bg-[#2ECC71] hover:bg-[#28B463] text-gray-50 font-semibold py-3 rounded-lg text-center transition duration-300">Me siguen...</a>
            
            <!-- Modificado para que se muestre solo si el usuario está viendo su propio perfil -->
            <?php if ($profileUserId == $userId): ?>
                <a href="seguimientos.php?id=<?php echo $profileUserId; ?>&from=can_unfollow" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-3 rounded-lg text-center transition duration-300">Puedo dejar de seguir a...</a>
            <?php endif; ?>

            <?php if ($profileUserId == $userId): ?>
                <a href="usuarios.php" class="bg-yellow-300 hover:bg-yellow-400 text-gray-50 font-semibold py-3 rounded-lg text-center transition duration-300">Sugerencias de perfiles a los que seguir</a>
            <?php endif; ?>
        </div>

        <!-- Mostrar tweets del usuario del perfil -->
        <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
            <h2 class="text-2xl font-semibold mb-4">Tweets de <?php echo htmlspecialchars($profileUser['username']); ?></h2>
            <?php
            $sqlTweets = "SELECT p.*, u.username FROM social_network.publications p
                          JOIN social_network.users u ON p.userId = u.id
                          WHERE p.userId = :profileUserId
                          ORDER BY p.createDate DESC";
            
            $stmtTweets = $pdo->prepare($sqlTweets);
            $stmtTweets->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
            $stmtTweets->execute();

            // Obtener todos los tweets del perfil para mostrarlos
            $tweets = $stmtTweets->fetchAll(PDO::FETCH_ASSOC);
            if (empty($tweets)): ?>
                <p class="text-gray-500">No hay tweets para mostrar.</p>
            <?php else: ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="border-b border-gray-300 py-4">
                        <p><strong class="text-blue-600"><?php echo htmlspecialchars($tweet['username']); ?></strong></p>
                        <p class="text-gray-800"><?php echo htmlspecialchars($tweet['text']); ?></p>
                        <p class="text-gray-500 text-sm"><small><?php echo htmlspecialchars($tweet['createDate']); ?></small></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Mostrar mensaje de estado si existe -->
        <?php if (isset($_SESSION['status-message'])): ?>
            <div id="status-message" class=" bg-green-100 text-green-800 p-4 rounded mb-6 mt-6"><?php echo htmlspecialchars($_SESSION['status-message']); ?></div>
            <?php unset($_SESSION['status-message']); // Limpiar el mensaje después de mostrarlo ?>
        <?php endif; ?>

        <script>
            // Esperar 5 segundos y ocultar el mensaje de estado si existe
            setTimeout(function() {
                let statusMessage = document.getElementById('status-message');
                if (statusMessage) {
                    statusMessage.style.display = 'none'; // Ocultar el mensaje
                }
            }, 5000); // 5 segundos
        </script>

        <!-- Formulario para enviar un mensaje privado solo si no es su propio perfil -->
        <?php if ($profileUserId != $userId): ?>
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6 mt-6">
                <h2 class="text-2xl font-semibold mb-4">Enviar Mensaje Privado</h2>
                <form action="../scripts/send_message.php" method="POST">
                    <input type="hidden" name="receiver_id" value="<?php echo $profileUserId; ?>">
                    <textarea name="message" rows="4" placeholder="Escribe un mensaje privado para esta persona..." required class="w-full p-2 border border-gray-300 rounded-md"></textarea><br>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-gray-50 font-semibold py-2 px-4 rounded mt-2">Enviar Mensaje</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Enlace para volver a la página principal -->
        <div class="text-center">
            <a href="main.php">
                <button class=" mt-4 bg-gray-600 hover:bg-gray-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a la Página Principal</button>
            </a>
        </div>
    </div>
</body>
</html>


