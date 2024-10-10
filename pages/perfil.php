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
$queryProfileUser = "SELECT * FROM social_network.users WHERE id = '$profileUserId'";
$resProfileUser = mysqli_query($connect, $queryProfileUser);
$profileUser = mysqli_fetch_assoc($resProfileUser);

// Contar los seguidores
$countFollowersQuery = "SELECT COUNT(*) as followerCount FROM social_network.follows WHERE userToFollowId = '$profileUserId'";
$resultFollowers = mysqli_query($connect, $countFollowersQuery);
$rowFollowers = mysqli_fetch_assoc($resultFollowers);
$followerCount = $rowFollowers['followerCount'];

// Contar los seguidos
$countFollowingQuery = "SELECT COUNT(*) as followingCount FROM social_network.follows WHERE users_id = '$profileUserId'";
$resultFollowing = mysqli_query($connect, $countFollowingQuery);
$rowFollowing = mysqli_fetch_assoc($resultFollowing);
$followingCount = $rowFollowing['followingCount'];

// Procesar el seguimiento de usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['follow_user'])) {
    $followUserId = $_POST['follow_user'];
    $insertFollow = "INSERT INTO social_network.follows (users_id, userToFollowId) VALUES ('$userId', '$followUserId')";
    mysqli_query($connect, $insertFollow);
    header("Location: perfil.php?id=$followUserId"); // Redirigir a la página de perfil del usuario seguido
    exit;
}

// Procesar dejar de seguir usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfollow_user'])) {
    $unfollowUserId = $_POST['unfollow_user'];
    $deleteFollow = "DELETE FROM social_network.follows WHERE users_id = '$userId' AND userToFollowId = '$unfollowUserId'";
    mysqli_query($connect, $deleteFollow);
    header("Location: perfil.php?id=$unfollowUserId"); // Redirigir a la página de perfil del usuario no seguido
    exit;
}

// Comprobar si el usuario actual sigue al usuario del perfil
$isFollowing = false;
$checkFollow = "SELECT * FROM social_network.follows WHERE users_id = '$userId' AND userToFollowId = '$profileUserId'";
$resCheckFollow = mysqli_query($connect, $checkFollow);
if (mysqli_num_rows($resCheckFollow) > 0) {
    $isFollowing = true;
}

// Procesar la actualización de la descripción del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_description'])) {
    $newDescription = mysqli_real_escape_string($connect, trim($_POST['description']));
    
    // Actualizar la descripción solo si es el usuario logueado
    if ($userId == $profileUserId && !empty($newDescription)) {
        $updateDescription = "UPDATE social_network.users SET description = '$newDescription' WHERE id = '$userId'";
        mysqli_query($connect, $updateDescription);
        header("Location: perfil.php?id=$userId"); // Redirigir a la misma página para ver el cambio
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($profileUser['username']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- Información del usuario -->
    <h1> Perfil de <?php echo htmlspecialchars($profileUser['username']); ?></h1>
    <p><?php echo htmlspecialchars($profileUser['description']?? ''); ?></p>

   
    
    <!-- Botón de seguimiento o dejar de seguir solo si no es el propio perfil -->
    <?php if ($profileUserId != $userId): ?>
        <form method="POST" action="perfil.php?id=<?php echo $profileUserId; ?>" style="display:inline;">
            <?php if ($isFollowing): ?>
                <input type="hidden" name="unfollow_user" value="<?php echo $profileUserId; ?>">
                <button type="submit">Dejar de Seguir</button>
            <?php else: ?>
                <input type="hidden" name="follow_user" value="<?php echo $profileUserId; ?>">
                <button type="submit">Seguir</button>
            <?php endif; ?>
        </form>
    <?php endif; ?>

    <!-- Enlaces adicionales para gestionar relaciones -->
    <h2>Gestión de Seguimiento</h2>

    <p>Número de seguidores: <?php echo $followerCount; ?></p>
    <p>Número de personas que sigue: <?php echo $followingCount; ?></p> 

    <?php if ($profileUserId == $userId): ?>
         
        <ul>
            <li><a href="seguimientos.php?id=<?php echo $profileUserId; ?>">Personas a las que sigo</a></li>
            <li><a href="seguidores.php?id=<?php echo $profileUserId; ?>&type=followers">Personas que me siguen</a></li>
            <li><a href="seguimientos.php?id=<?php echo $profileUserId; ?>&type=available">Personas a las que puedo dejar de seguir</a></li>
            <li><a href="usuarios.php">Personas a quienes puedo seguir</a></li>
        </ul>
    

        <!-- Formulario para editar la descripción (solo si es el usuario logueado) -->
    
        <h2>Editar Descripción</h2>
        <form method="POST" action="perfil.php?id=<?php echo $profileUserId; ?>">
            <textarea name="description" rows="4" cols="50" maxlength="255" placeholder="Nueva descripción" required><?php echo htmlspecialchars($profileUser['description']?? ''); ?></textarea><br>
            <button type="submit" name="update_description">Actualizar Descripción</button>
        </form>
    <?php else: ?>
        
        <ul>
            <li><a href="seguimientos.php?id=<?php echo $profileUserId; ?>">Personas seguidas</a></li>
            <li><a href="seguidores.php?id=<?php echo $profileUserId; ?>&type=followers">Personas seguidoras</a></li>
        </ul>

    <?php endif; ?>

    <!-- Mostrar tweets del usuario del perfil -->
    <h2>Tweets de <?php echo htmlspecialchars($profileUser['username']); ?></h2>
    <?php
    $sqlTweets = "SELECT p.*, u.username FROM social_network.publications p
                  JOIN social_network.users u ON p.userId = u.id
                  WHERE p.userId = '$profileUserId'
                  ORDER BY p.createDate DESC";
    
    $resTweets = mysqli_query($connect, $sqlTweets);
    
    // Obtener todos los tweets del perfil para mostrarlos
    $tweets = [];
    if ($resTweets) {
        while ($tweet = mysqli_fetch_assoc($resTweets)) {
            $tweets[] = $tweet; // Guardamos cada tweet en el array $tweets
        }
    }
    
    if (empty($tweets)): ?>
        <p>No hay tweets para mostrar.</p>
    <?php else: ?>
        <?php foreach ($tweets as $tweet): ?>
            <div>
                <p><strong><?php echo htmlspecialchars($tweet['username']); ?></strong></p>
                <p><?php echo htmlspecialchars($tweet['text']); ?></p>
                <p><small><?php echo $tweet['createDate']; ?></small></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Enlace para volver a la página principal -->
    <a href="main.php"><button>Volver a la Página Principal</button></a>

</body>
</html>
