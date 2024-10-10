<?php
require_once "./scripts/connection.php";

// Mostrar mensaje de registro exitoso
if (isset($_SESSION['completado'])) {
    echo "<p style='color:green;'>" . $_SESSION['completado'] . "</p>";
    unset($_SESSION['completado']); // Limpiar el mensaje después de mostrarlo
    header("Location: index.php"); // Redirigir para evitar que el mensaje permanezca
    exit();
}

// Mostrar errores si los hay
if (isset($_SESSION['errores']['general'])) {
    echo "<p style='color:red;'>" . $_SESSION['errores']['general'] . "</p>";
    unset($_SESSION['errores']['general']); // Limpiar el mensaje de error después de mostrarlo
    header("Location: index.php"); // Redirigir para evitar que el mensaje permanezca
    exit();
}
// Si hay un mensaje de error en la sesión, lo guardamos en una variable y lo eliminamos de la sesión
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']); // Eliminamos el mensaje de error de la sesión para no mostrarlo varias veces

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Mostrar el mensaje de error, si existe -->
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="../scripts/login.php" method="POST">
            <fieldset>
                <legend>Logeate</legend>

                <div>
                    <label for="username" >Username:</label>
                    <div>
                        <input type="text" id="username" name="username" required />
                    </div>
                </div>

                <div>
                    <label for="password" >Password:</label>
                    <div class="col-sm-10">
                        <input type="password" id="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                            title="Debe contener al menos un número y una mayúscula y una minúscula, y al menos 8 o más carácteres"/>
                    </div>
                </div>

                <div >
                    <input id="sendBttn" type="submit" value="Send" name="submit"/>
                </div>
            </fieldset>
        </form>
        
        <a href="./pages/registroform.php">Registro</a>
</body>
</html>

