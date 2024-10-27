# TwitterProyect
Proyecto de Twitter: Primer proyecto de la asignatura de Servidor

## Descripción
Este proyecto es una red social simple donde los usuarios pueden enviar twits, visualizarlos según preferencias,
seguir o dejar de hacerlo a otros usuarios, así como ver sus seguidores y seguidos. Se cumplen con todos los requisitos indicados en la actividad. 

Se han incluído algunos botones más para mejorar la navegación entre páginas así como contador de caracteres en el mensaje de twitter.
Se ha incorporado PDO.

También se permite dejar de seguir a otros usuarios entrando en su perfil y pulsando en el botón de "dejar de seguir" o desde el perfil de la propia 
persona si se cliquea en la opción "Puedo dejar de seguir a...". En esta última opción, se carga la página seguimientos.php con boton para dejar de seguir junto a 
cada perfil. Este boton, sin embargo, no aparecerá si se accede a seguimientos.php desde la opción "Sigo a...".

Como extra, se le ha añadido la posibilidad de entrar al perfil de otro usuario y desde allí, dejarle un mensaje privado.
Desde el propio perfil, la persona puede acceder a la bandeja de correos (inbox) y ver los mensajes que ha recibido y enviado.
Pueden borrarse, así como indicar que se han leído. Se ha incorporado un contador que da información sobre el número de mensajes
de cada apartado, y anuncia en rojo la cantidad de no leídos. También es fácil identificarlos por el cambio de la apariencia de los mensajes de cada apartado.



##  Base de Datos
Se adjunta en la carpeta database la base de datos modificada para administrar los mensajes privados.