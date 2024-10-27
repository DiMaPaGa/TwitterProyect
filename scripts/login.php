<?php
require_once "./connection.php"; 

$error = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST["username"]);
    $pass = $_POST["password"];

    // Preparar la consulta
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);

    // Enlazar el parámetro
    $stmt->bindParam(':username', $username, PDO::PARAM_STR); 

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Verificar si se encontró el usuario
        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña
            if (password_verify($pass, $usuario["password"])) {
                // Iniciar sesión
                $_SESSION["username"] = $usuario["username"];
                $_SESSION["id"] = $usuario["id"];
                header("Location: ../pages/main.php");
                exit;
            } else {
                // Almacenar mensaje de error en la sesión
                $_SESSION['error'] = "Contraseña incorrecta";
                header("Location: ../index.php"); // Redirigir de nuevo a index
                exit;
            }
        } else {
            // Almacenar mensaje de error en la sesión
            $_SESSION['error'] = "Usuario no encontrado";
            header("Location: ../index.php"); // Redirigir de nuevo a index
            exit;
        }
    } else {
        // Manejo de error al ejecutar la consulta
        $_SESSION['error'] = "Error al ejecutar la consulta.";
        header("Location: ../index.php"); // Redirigir de nuevo a index
        exit;
    }
}
?>

