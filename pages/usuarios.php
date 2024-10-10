<?php
require_once "../scripts/connection.php";


// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); // Redirige si no está autenticado
    exit();
}

// ID del usuario actual
$userId = $_SESSION['id'];

// Consulta para obtener usuarios que no están siendo seguidos
$sql = "
    SELECT u.id, u.username, u.description
    FROM social_network.users u
    WHERE u.id != $userId
    AND u.id NOT IN (
        SELECT userToFollowId FROM social_network.follows WHERE users_id = $userId
    )";

$result = mysqli_query($connect, $sql);

// Comprobar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . mysqli_error($connect));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios a seguir</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Personas a las que puedes seguir</h1>

<?php if (isset($_GET['mensaje'])): ?>
    <p><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
<?php endif; ?>

<?php if (mysqli_num_rows($result) > 0): ?>
    <ul>
        <?php while ($user = mysqli_fetch_assoc($result)): ?>
            <li>
                <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['description'] ?? ''); ?>)
                <a href="../scripts/seguir.php?id=<?php echo $user['id']; ?>">Seguir</a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No hay usuarios disponibles para seguir.</p>
<?php endif; ?>

<a href="./perfil.php">Volver a mi perfil</a>

</body>
</html>
