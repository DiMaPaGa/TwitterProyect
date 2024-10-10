<?php
require_once "./connection.php"; 

$error = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($connect, trim($_POST["username"]));
    $pass = $_POST["password"];

    // Consultar la base de datos
    $sql = "SELECT * FROM social_network.users WHERE username = '$username'";
    $res = mysqli_query($connect, $sql);

    // Verificar si se encontró el usuario
    if ($res && mysqli_num_rows($res) == 1) {
        $usuario = mysqli_fetch_assoc($res);

        // Verificar la contraseña
        if ($usuario && password_verify($pass, $usuario["password"])) {
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
}
?>

