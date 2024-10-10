<?php
require_once '../scripts/connection.php';

// Verificar si el usuario está autenticado

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Obtener la información del usuario tras la autenticación
$userId = $_SESSION['id'];
$queryUser = "SELECT * FROM social_network.users WHERE id = '$userId'";
$resUsuario = mysqli_query($connect, $queryUser);
$usuarioLogueado = mysqli_fetch_assoc($resUsuario);

// Procesar la selección de qué tweets mostrar
$tweetOption = 'following'; // Valor por defecto para mostrar los tweets de tus seguidores
if (isset($_POST['tweet_option'])) {
    $tweetOption = $_POST['tweet_option'];
}

// Obtener los tweets según la opción seleccionada
if ($tweetOption === 'my') {
    // Mostrar solo tus propios tweets
    $sqlTweets = "SELECT p.*, u.username FROM social_network.publications p
                  JOIN social_network.users u ON p.userId = u.id
                  WHERE p.userId = '$userId'
                  ORDER BY p.createDate DESC";
} elseif ($tweetOption === 'following') {
    // Mostrar tweets de las personas que sigues
    $sqlTweets = "SELECT p.*, u.username FROM social_network.publications p
                  JOIN social_network.users u ON p.userId = u.id
                  WHERE p.userId IN (SELECT userToFollowId FROM social_network.follows WHERE users_id = '$userId')
                  ORDER BY p.createDate DESC";
} else {
    // Mostrar todos los tweets
    $sqlTweets = "SELECT p.*, u.username FROM social_network.publications p
                  JOIN social_network.users u ON p.userId = u.id
                  ORDER BY p.createDate DESC";
}

$resTweets = mysqli_query($connect, $sqlTweets);

// Obtener todos los tweets para mostrarlos
$tweets = [];
if ($resTweets) {
    while ($tweet = mysqli_fetch_assoc($resTweets)) {
        $tweets[] = $tweet; // Guardamos cada tweet en el array $tweets
    }
}

// Procesar el formulario de nuevos tweets
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tweet_content'])) {
    $tweetContent = mysqli_real_escape_string($connect, trim($_POST['tweet_content']));

    // Validar longitud del tweet
    if (strlen($tweetContent) > 140) {
        echo "<p style='color: red;'>Error: El tweet no puede tener más de 140 caracteres.</p>";
    } elseif (!empty($tweetContent)) {
        // Insertar tweet en la base de datos si es válido
        $insertTweet = "INSERT INTO social_network.publications (userId, text, createDate) VALUES ('$userId', '$tweetContent', CURRENT_TIMESTAMP)";
        mysqli_query($connect, $insertTweet);
        header("Location: main.php"); // Redirigir a la misma página para ver el nuevo tweet
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- Información del usuario -->
    <h1>¡ Bienvenid@, <?php echo htmlspecialchars($usuarioLogueado['username']); ?>!</h1>
    <p><?php echo htmlspecialchars($usuarioLogueado['description']?? ''); ?></p>
    <a href="../index.php"><button>Cerrar Sesión</button></a>

    <!-- Enlace a tu perfil -->
    <a href="perfil.php?id=<?php echo $userId; ?>"><button>Ir a mi perfil</button></a>

    <!-- Formulario para seleccionar qué tweets mostrar -->
    <form method="POST" action="main.php">
        <label for="tweet_option">Mostrar:</label>
        <select name="tweet_option" id="tweet_option" onchange="this.form.submit()">
            <option value="my" <?php echo ($tweetOption === 'my') ? 'selected' : ''; ?>>Mis Tweets</option>
            <option value="following" <?php echo ($tweetOption === 'following') ? 'selected' : ''; ?>>Tweets de Personas que Sigo</option>
            <option value="all" <?php echo ($tweetOption === 'all') ? 'selected' : ''; ?>>Todos los Tweets</option>
        </select>
    </form>

    <!-- Formulario para escribir un nuevo tweet -->
    <h2>Nuevo Tweet</h2>
    <form method="POST" action="main.php">
        <textarea name="tweet_content" rows="4" cols="50" maxlength="140" placeholder="¿Qué estás pensando?" required oninput="updateCharacterCount()"></textarea><br>
        <p id="charCount">140 caracteres restantes</p>
        <button type="submit">Publicar</button>
    </form>

    <script>
    // Actualiza el contador de caracteres restantes en tiempo real
    function updateCharacterCount() {
        const textarea = document.querySelector('textarea[name="tweet_content"]');
        const charCount = document.getElementById('charCount');
        const maxLength = 140;
        const currentLength = textarea.value.length;

        charCount.textContent = `${maxLength - currentLength} caracteres restantes`;
    }
    </script>

    <!-- Mostrar tweets -->
    <h2>Tweets</h2>
    <?php if (empty($tweets)): ?>
        <p>No hay tweets para mostrar.</p>
    <?php else: ?>
        <?php foreach ($tweets as $tweet): ?>
            <div>
                <p><strong><a href="perfil.php?id=<?php echo $tweet['userId']; ?>"><?php echo htmlspecialchars($tweet['username']); ?></a></strong></p>
                <p><?php echo htmlspecialchars($tweet['text']); ?></p>
                <p><small><?php echo $tweet['createDate']; ?></small></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>




