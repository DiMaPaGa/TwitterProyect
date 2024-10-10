<?php
require_once "../scripts/connection.php";

// Verificar si el usuario está logueado

if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

$idUsuario = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id']; // ID del usuario cuyo seguidores se mostrarán
//Tomar el nombre del usuario a traves de su id
$userName = mysqli_fetch_assoc(mysqli_query($connect, "SELECT username FROM social_network.users WHERE id = $idUsuario"))['username'];

// Obtener la lista de seguidores
$sqlSeguidores = "SELECT u.* FROM social_network.follows f JOIN social_network.users u ON f.users_id = u.id WHERE f.userToFollowId = '$idUsuario'";
$resSeguidores = mysqli_query($connect, $sqlSeguidores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguidores</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Seguidores de <?php echo $userName; ?></h1>
    <ul>
        
        <?php while ($seguidor = mysqli_fetch_assoc($resSeguidores)): ?>
            <li>
                <a href="perfil.php?id=<?php echo $seguidor['id']; ?>"><?php echo htmlspecialchars($seguidor['username']); ?></a>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php if (mysqli_num_rows($resSeguidores) == 0): ?>
        <p>No hay seguidores aún para mostrar.</p>
    <?php endif; ?>

    <a href="./main.php">Volver a la página principal</a>
    <a href="../scripts/logout.php">Cerrar sesión</a>
</body>
</html>