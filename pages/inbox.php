<?php
require_once "../scripts/connection.php";
// Asegurar que el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$userId = $_SESSION['id'];

// Contar mensajes recibidos y no leídos
$sqlCountReceived = "SELECT COUNT(*) as total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread FROM social_network.private_messages WHERE receiver_id = :userId";
$stmtCountReceived = $pdo->prepare($sqlCountReceived);
$stmtCountReceived->execute(['userId' => $userId]);
$countReceived = $stmtCountReceived->fetch(PDO::FETCH_ASSOC);
$totalReceived = $countReceived['total'];  // Total de mensajes recibidos
$unreadReceived = $countReceived['unread']; // Total de mensajes no leídos

// Contar mensajes enviados
$sqlCountSent = "SELECT COUNT(*) as total FROM social_network.private_messages WHERE sender_id = :userId";
$stmtCountSent = $pdo->prepare($sqlCountSent);
$stmtCountSent->execute(['userId' => $userId]);
$countSent = $stmtCountSent->fetch(PDO::FETCH_ASSOC);
$totalSent = $countSent['total'];  // Total de mensajes enviados

// Obtener mensajes recibidos
$sqlReceived = "SELECT pm.id, pm.message, pm.createDate, pm.is_read, u.username as sender_name
                FROM social_network.private_messages pm
                JOIN social_network.users u ON pm.sender_id = u.id
                WHERE pm.receiver_id = :userId
                ORDER BY pm.createDate DESC";
$stmtReceived = $pdo->prepare($sqlReceived);
$stmtReceived->execute(['userId' => $userId]);
$receivedMessages = $stmtReceived->fetchAll(PDO::FETCH_ASSOC);

// Obtener mensajes enviados
$sqlSent = "SELECT pm.id, pm.message, pm.createDate, u.username as receiver_name
            FROM social_network.private_messages pm
            JOIN social_network.users u ON pm.receiver_id = u.id
            WHERE pm.sender_id = :userId
            ORDER BY pm.createDate DESC";
$stmtSent = $pdo->prepare($sqlSent);
$stmtSent->execute(['userId' => $userId]);
$sentMessages = $stmtSent->fetchAll(PDO::FETCH_ASSOC);

// Marcar mensajes como leídos
if (isset($_GET['mark_read_id'])) {
    $markReadId = $_GET['mark_read_id'];
    $sqlMarkAsRead = "UPDATE social_network.private_messages SET is_read = 1 WHERE id = :markReadId AND receiver_id = :userId";
    $stmtMarkAsRead = $pdo->prepare($sqlMarkAsRead);
    $stmtMarkAsRead->execute(['markReadId' => $markReadId, 'userId' => $userId]);
    header("Location: inbox.php");
    exit();
}

// Borrar un mensaje
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $sqlDelete = "DELETE FROM social_network.private_messages WHERE id = :deleteId AND (sender_id = :userId OR receiver_id = :userId)";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->execute(['deleteId' => $deleteId, 'userId' => $userId]);
    header("Location: inbox.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bandeja de Entrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col items-center p-6">
    <h1 class="text-3xl font-bold text-[#1DA1F2] mb-6">Bandeja de Entrada</h1>

    <!-- Contadores de mensajes -->
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-8 w-full max-w-3xl text-center">
        <p class="text-lg font-semibold text-gray-700">Mensajes Recibidos: <span class="font-bold text-[#1DA1F2]"><?php echo $totalReceived; ?></span> (No leídos: <span class="font-bold text-red-500"><?php echo $unreadReceived; ?></span>)</p>
        <p class="text-lg font-semibold text-gray-700">Mensajes Enviados: <span class="font-bold text-[#1DA1F2]"><?php echo $totalSent; ?></span></p>
    </div>

    <!-- Mensajes Recibidos -->
    <h2 class="text-2xl font-semibold text-[#1DA1F2] mb-4">Mensajes Recibidos</h2>
    <?php if ($receivedMessages): ?>
        <div class="space-y-4 w-full max-w-3xl">
            <?php foreach ($receivedMessages as $msg): ?>
                <div class="border border-gray-300 rounded-md p-4 transition duration-200 shadow-sm hover:shadow-md <?php echo !$msg['is_read'] ? 'bg-blue-50' : 'bg-gray-50'; ?>">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-600">
                            De: <?php echo htmlspecialchars($msg['sender_name']); ?>
                        </span>
                        <small class="text-xs text-gray-500"><?php echo htmlspecialchars($msg['createDate']); ?></small>
                    </div>
                    <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p> <!-- nl2br es una función de PHP que convierte los saltos de línea en el texto en etiquetas HTML de salto de línea. Permite que el texto sea legible en el navegador, ya que HTML ignora los saltos de línea en el texto plano. -->
                    <div class="flex justify-end space-x-4">
                        <?php if (!$msg['is_read']): ?>
                            <a href="?mark_read_id=<?php echo $msg['id']; ?>" class="bg-[#1DA1F2] hover:bg-blue-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300 text-sm">Marcar como leído</a>
                        <?php endif; ?>
                        <a href="?delete_id=<?php echo $msg['id']; ?>" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300 text-sm">Borrar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 text-lg">No tienes mensajes recibidos.</p>
    <?php endif; ?>

    <!-- Mensajes Enviados -->
    <h2 class="text-2xl font-semibold text-[#1DA1F2] mt-8 mb-4">Mensajes Enviados</h2>
    <?php if ($sentMessages): ?>
        <div class="space-y-4 w-full max-w-3xl">
            <?php foreach ($sentMessages as $msg): ?>
                <div class="border border-gray-300 rounded-md bg-gray-50 p-4 transition duration-200 shadow-sm hover:shadow-md">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-600">
                            Para: <?php echo htmlspecialchars($msg['receiver_name']); ?>
                        </span>
                        <small class="text-xs text-gray-500"><?php echo htmlspecialchars($msg['createDate']); ?></small>
                    </div>
                    <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p> <!-- nl2br explicado en linea 95 --> 
                    <div class="flex justify-end">
                        <a href="?delete_id=<?php echo $msg['id']; ?>" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300 text-sm">Borrar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 text-lg">No has enviado mensajes.</p>
    <?php endif; ?>

    <!-- Volver a página principal, volver a perfil y cerrar sesión -->
    <div class="mt-6 flex justify-center gap-4">
        <a href="./main.php" class="bg-gray-600 hover:bg-gray-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a la página principal</a>
        <a href="./perfil.php?id=<?php echo htmlspecialchars($userId); ?>" class="bg-blue-600 hover:bg-blue-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Volver a perfil</a>
        <a href="../scripts/logout.php" class="bg-red-600 hover:bg-red-700 text-gray-50 font-semibold py-2 px-4 rounded transition duration-300">Cerrar sesión</a>
    </div>
</body>
</html>