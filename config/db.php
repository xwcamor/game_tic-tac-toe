<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // por defecto en XAMPP no hay contraseña
$db   = 'tic_tac_toe';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
?>
