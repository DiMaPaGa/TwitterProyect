<?php
require_once "./scripts/connection.php";


// Mostrar mensaje de registro exitoso
$mensaje = isset($_SESSION['completado']) ? $_SESSION['completado'] : '';
unset($_SESSION['completado']); // Limpiar el mensaje de la sesión después de mostrarlo

// Mostrar errores si los hay
$errorGeneral = isset($_SESSION['errores']['general']) ? $_SESSION['errores']['general'] : '';
unset($_SESSION['errores']['general']); // Limpiar el error general de la sesión

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']); // Limpiar el error específico de la sesión
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Función para ocultar mensajes después de 5 segundos
        function hideMessage() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 5000); // 5000 milisegundos = 5 segundos
            });
        }

        // Ejecutar la función al cargar la página
        window.onload = function() {
            const messages = document.querySelectorAll('.message');
            if (messages.length > 0) {
                messages.forEach(message => {
                    message.style.display = 'block'; // Mostrar mensaje al cargar
                });
                hideMessage(); // Llamar a la función de ocultar
            }
        };
    </script>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100 relative">

    <!-- Contenedor de mensajes -->
    <div class="fixed top-10 left-1/2 transform -translate-x-1/2 w-[80%]">
        <!-- Mostrar el mensaje de éxito -->
        <?php if (!empty($mensaje)): ?>
            <p class="text-green-600 font-semibold mb-4 text-center message"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <!-- Mostrar el mensaje de error general, si existe -->
        <?php if (!empty($errorGeneral)): ?>
            <p class="text-red-600 font-semibold mb-4 text-center message"><?php echo htmlspecialchars($errorGeneral); ?></p>
        <?php endif; ?>

        <!-- Mostrar el mensaje de error específico, si existe -->
        <?php if (!empty($error)): ?>
            <p class="text-red-600 font-semibold mb-4 text-center message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>

    <!-- Contenedor principal que divide en dos columnas -->
    <div class="flex w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden mt-10">
        
        <!-- Columna Izquierda con el Logo -->
        <div class="w-1/2 bg-gray-50 flex items-center justify-center">
            <img src="img/Logo_sin_fondo.png" alt="Logo" class="w-2/3 h-auto"/>
        </div>
        
        <!-- Columna Derecha con el Formulario -->
        <div class="w-1/2 bg-gray-50 p-8 flex items-center justify-center">
            <form action="../scripts/login.php" method="POST" class="w-full max-w-md">
                <fieldset class="mb-4">
                    <legend class="text-2xl text-[#1DA1F2] font-semibold text-center mb-6">Logéate</legend>

                    <!-- Campo de Usuario -->
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Usuario:</label>
                        <div>
                            <input type="text" id="username" name="username" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                        </div>
                    </div>

                    <!-- Campo de Contraseña -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña:</label>
                        <div>
                            <input type="password" id="password" name="password" required
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                   title="Debe contener al menos un número y una mayúscula y una minúscula, y al menos 8 o más caracteres"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1DA1F2]" />
                        </div>
                    </div>

                    <!-- Botón de Enviar -->
                    <div class="flex justify-center">
                        <input id="sendBttn" type="submit" value="Entrar" name="submit"
                               class="w-full py-2 bg-[#1DA1F2] text-gray-50 font-semibold rounded-md hover:bg-blue-800 transition duration-200"/>
                    </div>
                </fieldset>

                <!-- Enlace al Registro -->
                <div class="text-center mt-4">
                    <a href="./pages/registroform.php" class="text-[#1DA1F2] hover:text-blue-800 font-semibold hover:underline">¿No tienes una cuenta? Regístrate aquí</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
