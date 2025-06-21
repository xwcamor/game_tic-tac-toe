<?php
session_start();        // Inicia sesión por si no se ha hecho aún
session_unset();        // Limpia todas las variables de sesión
session_destroy();      // Destruye la sesión actual

// Redirecciona al formulario de login
header('Location: login.php');
exit();
