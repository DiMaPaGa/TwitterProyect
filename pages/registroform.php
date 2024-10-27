<?php
require_once "../scripts/connection.php";

// Mostrar errores si los hay
$erroresHtml = '';
if (isset($_SESSION['errores'])) {
    foreach ($_SESSION['errores'] as $error) {
        $erroresHtml .= "<p class='mensaje-error text-red-600 font-semibold mb-4 text-center'>$error</p>";
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
    <script src="https://cdn.tailwindcss.com"></script>
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

        // Función para ocultar mensajes después de 5 segundos
        function ocultarMensajes() {
            const mensajes = document.querySelectorAll('.mensaje-error');
            mensajes.forEach(mensaje => {
                setTimeout(() => {
                    mensaje.style.display = 'none';
                }, 5000);
            });
        }

        // Llamar a la función para ocultar mensajes al cargar la página
        window.onload = ocultarMensajes;
    </script>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl text-center font-semibold text-[#1DA1F2] mb-6">Regístrate</h2>
        
        <!-- Mostrar mensajes de error -->
        <div class="mb-4">
            <?php if ($erroresHtml): ?>
                <?php echo $erroresHtml; ?>
            <?php endif; ?>
        </div>

        <form action="../scripts/registro.php" method="POST" onsubmit="return validarFormulario()">
            <fieldset>
                <!-- Campo de Usuario -->
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username:</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                </div>

                <!-- Campo de Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                </div>

                <!-- Campo de Contraseña -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
                    <input type="password" id="password" name="password" required
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                           title="Debe contener al menos un número y una mayúscula y una minúscula, y al menos 8 o más caracteres"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                </div>

                <!-- Campo para confirmar la contraseña -->
                <div class="mb-4">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                           title="Debe coincidir con la contraseña anterior"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                </div>

                <!-- Campo de Descripción -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                    <input type="text" id="description" name="description"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                </div>

                <!-- Botón de Envío -->
                <div class="flex justify-center">
                    <input id="sendBttn" type="submit" value="Enviar" name="submit"
                           class="w-full py-2 bg-[#1DA1F2] text-gray-50 font-semibold rounded-md hover:bg-blue-800 transition duration-200" />
                </div>
            </fieldset>
        </form>

        <!-- Enlace a la página de inicio de sesión -->
        <div class="text-center mt-4">
            <a href="../index.php" class="text-[#1DA1F2] hover:text-blue-800 font-semibold hover:underline">¿Ya tienes una cuenta? Inicia sesión aquí</a>
        </div>
    </div>
</body>
</html>

