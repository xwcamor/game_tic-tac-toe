<?php
session_start();
require '../config/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || !isset($_POST['game_id']) || !isset($_POST['fila']) || !isset($_POST['col'])) {
    header('Location: lobby.php');
    exit();
}

$game_id = intval($_POST['game_id']);
$fila = intval($_POST['fila']);
$col = intval($_POST['col']);
$user_id = $_SESSION['user_id'];

// Obtener datos de la partida
$stmt = $conn->prepare("SELECT player1_id, player2_id, estado, turn, status FROM games WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$stmt->bind_result($p1, $p2, $estado, $turn, $status);
$stmt->fetch();
$stmt->close();

// Verificar estado
if (!in_array($status, ['waiting', 'playing'])) {
    header("Location: play.php?game_id=$game_id");
    exit();
}

$is_player1 = ($user_id === $p1);
$is_player2 = ($user_id === $p2);
$player_number = $is_player1 ? 1 : ($is_player2 ? 2 : 0);

if ($player_number !== $turn || $player_number === 0) {
    header("Location: play.php?game_id=$game_id");
    exit();
}

// Preparar tablero
$tablero = json_decode($estado, true);
if (!is_array($tablero)) {
    $tablero = [['-', '-', '-'], ['-', '-', '-'], ['-', '-', '-']];
}

if ($fila < 0 || $fila > 2 || $col < 0 || $col > 2 || $tablero[$fila][$col] !== '-') {
    header("Location: play.php?game_id=$game_id");
    exit();
}

$symbol = $player_number === 1 ? 'X' : 'O';
$tablero[$fila][$col] = $symbol;

// Verificar ganador
function checkWinner($t, $s) {
    for ($i = 0; $i < 3; $i++) {
        if ($t[$i][0] === $s && $t[$i][1] === $s && $t[$i][2] === $s) return true;
        if ($t[0][$i] === $s && $t[1][$i] === $s && $t[2][$i] === $s) return true;
    }
    if ($t[0][0] === $s && $t[1][1] === $s && $t[2][2] === $s) return true;
    if ($t[0][2] === $s && $t[1][1] === $s && $t[2][0] === $s) return true;
    return false;
}

$winner = null;
$estado_final = json_encode($tablero);
$hay_ganador = checkWinner($tablero, $symbol);

if ($hay_ganador) {
    $winner = $user_id;
    $status = 'finished';
} elseif (!in_array('-', array_merge(...$tablero))) {
    $status = 'finished'; // empate
    $winner = null;
} else {
    $turn = $turn === 1 ? 2 : 1; // cambiar turno
}

// Actualizar base de datos
$stmt = $conn->prepare("UPDATE games SET estado = ?, turn = ?, status = ?, ganador = ? WHERE id = ?");
$stmt->bind_param("sissi", $estado_final, $turn, $status, $winner, $game_id);
$stmt->execute();
$stmt->close();

header("Location: play.php?game_id=$game_id");
exit();
?>
