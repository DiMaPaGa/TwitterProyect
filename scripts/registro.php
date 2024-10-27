<?php
require_once "./connection.php";

if (isset($_POST["submit"])) {
    // Recoger los datos del formulario
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : false;
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : false;
    $password = isset($_POST["password"]) ? $_POST["password"] : false;
    $confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : false;
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : null;

    // Array para guardar errores de validación
    $arrayErrores = array();

    // Validar username
    if (!empty($username) && !is_numeric($username)) {
        $queryCompareUsername = "SELECT id FROM social_network.users WHERE username = :username";
        $stmt = $pdo->prepare($queryCompareUsername);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $arrayErrores["username"] = "El username ya está en uso. Por favor elige otro.";
        }
    } else {
        $arrayErrores["username"] = "El username no es válido.";
    }

    // Validar el correo electrónico
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $queryCompareEmail = "SELECT id FROM social_network.users WHERE email = :email";
        $stmt = $pdo->prepare($queryCompareEmail);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $arrayErrores["email"] = "El email ya está en uso. Por favor elige otro.";
        }
    } else {
        $arrayErrores["email"] = "El email no es válido.";
    }

    // Validar las contraseñas (y si coinciden)
    if (!empty($password) && $password === $confirm_password) {
        // Contraseña válida
    } else {
        $arrayErrores["password"] = "Las contraseñas no coinciden o no son válidas.";
    }

    // Guardar el valor de descripción
    $descriptionValue = $description ? $description : null;

    // Validar si no hay errores antes de proceder
    if (count($arrayErrores) == 0) {
        // Si no hay errores, hashear la contraseña
        $passSegura = password_hash($password, PASSWORD_BCRYPT, ["cost" => 4]);

        // Intentar guardar el usuario en la base de datos
        $sql = "INSERT INTO social_network.users (username, email, password, description, createDate) 
                VALUES (:username, :email, :password, :description, CURRENT_DATE())";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $passSegura, PDO::PARAM_STR);
        $stmt->bindParam(':description', $descriptionValue, PDO::PARAM_STR);

        try {
            // Ejecutar la inserción
            if ($stmt->execute()) {
                // Éxito en el registro
                $_SESSION["completado"] = "Registro completado con éxito.";
                header("Location: ../index.php"); // Redirigir a index.php
                exit();
            } else {
                // Fallo en el registro
                $_SESSION["errores"]["general"] = "Hubo un fallo al registrar al usuario.";
                header("Location: ../pages/registroform.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION["errores"]["general"] = "Error en la base de datos: " . $e->getMessage();
            header("Location: ../pages/registroform.php");
            exit();
        }
    } else {
        // Si hay errores, almacenarlos en la sesión
        $_SESSION["errores"] = $arrayErrores;
        header("Location: ../pages/registroform.php");
    }
}
?>
