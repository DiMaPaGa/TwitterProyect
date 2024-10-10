<?php
    require_once "./connection.php";

    if (isset($_POST["submit"])) {
        // Recoger los datos del formulario y evitar SQL Injection
        $username = isset($_POST["username"]) ? mysqli_real_escape_string($connect, trim($_POST["username"])) : false;
        $email = isset($_POST["email"]) ? mysqli_real_escape_string($connect, trim($_POST["email"])) : false;
        $password = isset($_POST["password"]) ? mysqli_real_escape_string($connect, $_POST["password"]) : false;
        $confirm_password = isset($_POST["confirm_password"]) ? mysqli_real_escape_string($connect, $_POST["confirm_password"]) : false;
        $description = isset($_POST["description"]) ? mysqli_real_escape_string($connect, trim($_POST["description"])) : null;

        // Array para guardar errores de validación
        $arrayErrores = array();

        // Validar username
        if (!empty($username) && !is_numeric($username)) {
            // Comprobar si el nombre de usuario ya existe en la base de datos
        $queryCompareUsername = "SELECT id FROM social_network.users WHERE username = '$username'";
        $resultCompareUsername = mysqli_query($connect, $queryCompareUsername);
        
        if (mysqli_num_rows($resultCompareUsername) > 0) {
            $usernameValidado = false;
            $arrayErrores["username"] = "El username ya está en uso. Por favor elige otro.";
        } else {
            $usernameValidado = true;
        }
    } else {
        $usernameValidado = false;
        $arrayErrores["username"] = "El username no es válido.";
    }

        // Validar el correo electrónico
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
             // Comprobar si el email ya existe en la base de datos
            $queryCompareEmail = "SELECT id FROM social_network.users WHERE email = '$email'";
            $resultCompareEmail = mysqli_query($connect, $queryCompareEmail);
            
            if (mysqli_num_rows($resultCompareEmail) > 0) {
                $emailValidado = false;
                $arrayErrores["email"] = "El email ya está en uso. Por favor elige otro.";
            } else {
                $emailValidado = true;
            }
        } else {
            $emailValidado = false;
            $arrayErrores["email"] = "El email no es válido";
        }

        // Validar las contraseñas (y si coinciden)
        if (!empty($password) && $password === $confirm_password) {
            $passValidado = true;
        } else {
            $passValidado = false;
            $arrayErrores["password"] = "Las contraseñas no coinciden o no son válidas.";
        }

        // Validar si no hay errores antes de proceder
        if (count($arrayErrores) == 0) {
            // Si no hay errores, hash de la contraseña
            $passSegura = password_hash($password, PASSWORD_BCRYPT, ["cost" => 4]);

            // Intentar guardar el usuario en la base de datos
            $sql = "INSERT INTO social_network.users (username, email, password, description, createDate) 
                    VALUES('$username', '$email', '$passSegura', ".($description ? "'$description'" : "NULL").", CURRENT_DATE());";

            $guardar = mysqli_query($connect, $sql);

            // Verificar si la inserción fue exitosa
            if ($guardar) {
                // Éxito en el registro
                $_SESSION["completado"] = "Registro completado con éxito.";
                header("Location: ../index.php?registro=exitoso");
                exit();
            } else {
                // Fallo en el registro
                $_SESSION["errores"]["general"] = "Hubo un fallo al registrar al usuario.";
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
