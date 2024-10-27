<?php
require_once "../scripts/connection.php";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    $sender_id = $_SESSION['id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    if ($receiver_id != $sender_id && !empty($message)) {
        try {
            // Insertar el mensaje en la base de datos
            $sql = "INSERT INTO social_network.private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sender_id, $receiver_id, $message]);

            // Grardo mensaje de éxito en la sesión
            $_SESSION['status-message'] = "Mensaje enviado con éxito.";

            // Redirigir a perfil.php con el ID del receptor
            header("Location: ../pages/perfil.php?id=$receiver_id&msg=Mensaje enviado");
            exit;
        } catch (PDOException $e) {
            echo "Error al enviar el mensaje: " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "Error: no puedes enviarte mensajes a ti mismo ni mensajes vacíos.";
    }
}
?>
