<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['game_id'])) {
    header('Location: lobby.php');
    exit();
}

$game_id = intval($_POST['game_id']);

// Reiniciar tablero
$nuevo_tablero = json_encode([
    ['-', '-', '-'],
    ['-', '-', '-'],
    ['-', '-', '-']
]);

// Turno empieza por jugador 1
$stmt = $conn->prepare("UPDATE games SET estado = ?, turn = 1, ganador = NULL, status = 'playing' WHERE id = ?");
$stmt->bind_param("si", $nuevo_tablero, $game_id);
$stmt->execute();
$stmt->close();

header("Location: play.php?game_id=$game_id");
exit();
?>
