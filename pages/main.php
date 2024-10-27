<?php
require_once '../scripts/connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Obtener la información del usuario tras la autenticación
$userId = $_SESSION['id'];

try {
    // Obtener información del usuario
    $queryUser = "SELECT * FROM users WHERE id = :id"; // Usar un marcador de posición
    $stmtUser = $pdo->prepare($queryUser);
    $stmtUser->bindParam(':id', $userId, PDO::PARAM_INT); // Vincular el parámetro
    $stmtUser->execute();
    $usuarioLogueado = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // Procesar la selección de qué tweets mostrar
    $tweetOption = 'following'; // Valor por defecto para mostrar los tweets de tus seguidores
    if (isset($_POST['tweet_option'])) {
        $tweetOption = $_POST['tweet_option'];
    }

    // Obtener los tweets según la opción seleccionada
    if ($tweetOption === 'my') {
        // Mostrar solo tus propios tweets
        $sqlTweets = "SELECT p.*, u.username FROM publications p
                      JOIN users u ON p.userId = u.id
                      WHERE p.userId = :userId
                      ORDER BY p.createDate DESC";
    } elseif ($tweetOption === 'following') {
        // Mostrar tweets de las personas que sigues
        $sqlTweets = "SELECT p.*, u.username FROM publications p
                      JOIN users u ON p.userId = u.id
                      WHERE p.userId IN (SELECT userToFollowId FROM follows WHERE users_id = :userId)
                      ORDER BY p.createDate DESC";
    } else {
        // Mostrar todos los tweets
        $sqlTweets = "SELECT p.*, u.username FROM publications p
                      JOIN users u ON p.userId = u.id
                      ORDER BY p.createDate DESC";
    }

    $stmtTweets = $pdo->prepare($sqlTweets); // Preparar la consulta
    // Vincular el parámetro solo si es necesario
    if ($tweetOption === 'my' || $tweetOption === 'following') {
        $stmtTweets->bindParam(':userId', $userId, PDO::PARAM_INT); // Vincular el parámetro
    }
    $stmtTweets->execute(); // Ejecutar la consulta

    // Obtener todos los tweets para mostrarlos
    $tweets = [];
    while ($tweet = $stmtTweets->fetch(PDO::FETCH_ASSOC)) {
        $tweets[] = $tweet; // Guardamos cada tweet en el array $tweets
    }

    // Procesar el formulario de nuevos tweets
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tweet_content'])) {
        $tweetContent = trim($_POST['tweet_content']);

        // Validar longitud del tweet
        if (strlen($tweetContent) > 140) {
            echo "<p style='color: red;'>Error: El tweet no puede tener más de 140 caracteres.</p>";
        } elseif (!empty($tweetContent)) {
            // Insertar tweet en la base de datos si es válido
            $insertTweet = "INSERT INTO publications (userId, text, createDate) VALUES (:userId, :text, CURRENT_TIMESTAMP)";
            $stmtInsert = $pdo->prepare($insertTweet);
            $stmtInsert->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':text', $tweetContent, PDO::PARAM_STR);
            $stmtInsert->execute(); // Ejecutar la inserción
            header("Location: main.php"); // Redirigir a la misma página para ver el nuevo tweet
            exit;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage(); // Manejo de errores
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-4">
        <!-- Información del usuario -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-[#1DA1F2]">¡Bienvenid@, <?php echo htmlspecialchars($usuarioLogueado['username']); ?>!</h1>
            <p class="text-gray-700"><?php echo htmlspecialchars($usuarioLogueado['description'] ?? ''); ?></p>
            <div class="mt-4 flex items-center space-x-2">
                <a href="../index.php" class="text-gray-50 bg-red-600 hover:bg-red-700 font-semibold py-2 px-4 rounded h-full flex items-center justify-center">Cerrar Sesión</a>
                <a href="perfil.php?id=<?php echo $userId; ?>" class="text-gray-50 bg-blue-600 hover:bg-blue-700 font-semibold py-2 px-4 rounded h-full flex items-center justify-center">Ir a mi perfil</a>
                <a href="../pages/inbox.php">
                    <button class="bg-green-600 hover:bg-green-700 text-gray-50 font-semibold py-2 px-4 rounded h-full flex items-center justify-center">Ver Mensajes Privados</button>
                </a>
            </div>
        </div>

        <!-- Formulario para escribir un nuevo tweet -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Nuevo Tweet</h2>
            <form method="POST" action="main.php">
                <textarea name="tweet_content" rows="4" maxlength="140" placeholder="¿Qué estás pensando?" required class="w-full p-2 border border-gray-300 rounded-md"></textarea>
                <p id="charCount" class="mt-2">140 caracteres restantes</p>
                <button type="submit" class="mt-2 bg-[#1DA1F2] text-gray-50 font-semibold py-2 px-4 rounded">Publicar</button>
            </form>
        </div>

        <script>
            // Actualiza el contador de caracteres restantes en tiempo real
            function updateCharacterCount() {
                const textarea = document.querySelector('textarea[name="tweet_content"]');
                const charCount = document.getElementById('charCount');
                const maxLength = 140;
                const currentLength = textarea.value.length;

                charCount.textContent = `${maxLength - currentLength} caracteres restantes`;
            }

            // Asignar la función de actualización al evento input del textarea
            document.querySelector('textarea[name="tweet_content"]').addEventListener('input', updateCharacterCount);
        </script>

        <!-- Formulario para seleccionar qué tweets mostrar -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form method="POST" action="main.php">
                <label for="tweet_option" class="font-medium text-gray-700">Mostrar:</label>
                <select name="tweet_option" id="tweet_option" onchange="this.form.submit()" class="ml-2 border border-gray-300 rounded-md">
                    <option value="my" <?php echo ($tweetOption === 'my') ? 'selected' : ''; ?>>Mis Tweets</option>
                    <option value="following" <?php echo ($tweetOption === 'following') ? 'selected' : ''; ?>>Tweets de Personas que Sigo</option>
                    <option value="all" <?php echo ($tweetOption === 'all') ? 'selected' : ''; ?>>Todos los Tweets</option>
                </select>
            </form>
        </div>

        <!-- Mostrar tweets -->
        <h2 class="text-xl font-semibold mb-4">Tweets</h2>
        <?php if (empty($tweets)): ?>
            <p>No hay tweets para mostrar.</p>
        <?php else: ?>
            <?php foreach ($tweets as $tweet): ?>
                <div class="bg-white shadow-md rounded-lg p-4 mb-4">
                    <p class="font-bold text-[#1DA1F2]"><a href="perfil.php?id=<?php echo $tweet['userId']; ?>"><?php echo htmlspecialchars($tweet['username']); ?></a></p>
                    <p><?php echo htmlspecialchars($tweet['text']); ?></p>
                    <p class="text-gray-500 text-sm"><small><?php echo $tweet['createDate']; ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</body>
</html>



