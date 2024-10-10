<?php
require_once "../scripts/connection.php";
// Mostrar errores si los hay
if (isset($_SESSION['errores'])) {
    foreach ($_SESSION['errores'] as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
    unset($_SESSION['errores']); // Limpiar los errores después de mostrarlos
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        // Función para validar que las contraseñas coincidan
        function validarFormulario() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Las contraseñas no coinciden.");
                return false;  // Evita que el formulario se envíe
            }
            return true;  // Permite enviar el formulario si las contraseñas coinciden
        }
    </script>
</head>
<body>
<form action="../scripts/registro.php" method="POST" onsubmit="return validarFormulario()">
            <fieldset>
                <legend>Regístrate</legend>

                <div>
                    <label for="username" >Username:</label>
                    <div>
                        <input type="text" id="username" name="username" required />
                    </div>
                </div>

                <div>
                    <label for="email" >email:</label>
                    <div>
                        <input type="email" id="email" name="email" required />
                    </div>
                </div>

                <div>
                    <label for="password" >Password:</label>
                    <div class="col-sm-10">
                        <input type="password" id="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            title="Debe contener al menos un número y una mayúscula y una minúscula, y al menos 8 o más carácteres"/>
                    </div>
                </div>

                 <!-- Campo para confirmar la contraseña -->
            <div>
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                    title="Debe coincidir con la contraseña anterior"/>
            </div>

                
                <div>
                    <label for="description" >Descripcion:</label>
                    <div class="col-sm-10">
                        <input type="text" id="description" name="description" />
                    </div>
                </div>

                <div >
                    <input id="sendBttn" type="submit" value="Send" name="submit"/>
                </div>
            </fieldset>
        </form>

          <!-- Enlace a la página de inicio de sesión -->
    <a href="../index.php">¿Ya tienes una cuenta? Inicia sesión aquí</a>

</body>
</html>
