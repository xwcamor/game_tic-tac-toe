<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['game_id'])) {
    header('Location: lobby.php');
    exit();
}

$game_id = intval($_GET['game_id']);
$player2_id = $_SESSION['user_id'];

// Evitar que alguien se una a su propia partida
$stmt = $conn->prepare("SELECT player1_id FROM games WHERE id = ? AND status = 'waiting'");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($player1_id);
$stmt->fetch();

if ($stmt->num_rows > 0 && $player1_id != $player2_id) {
    $stmt_update = $conn->prepare("UPDATE games SET player2_id = ?, status = 'playing' WHERE id = ?");
    $stmt_update->bind_param("ii", $player2_id, $game_id);
    $stmt_update->execute();

    header("Location: play.php?game_id=$game_id");
} else {
    echo "No se pudo unir a la partida.";
}
