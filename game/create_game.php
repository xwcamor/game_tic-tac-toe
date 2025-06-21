<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "⚠️ No has iniciado sesión.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player1_id = $_SESSION['user_id'];
    $turn = 1;
    $status = 'waiting';

    $stmt = $conn->prepare("INSERT INTO games (player1_id, turn, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $player1_id, $turn, $status);

    if ($stmt->execute()) {
        $game_id = $stmt->insert_id;
        echo "✅ Partida creada. Redirigiendo a play.php?game_id=$game_id";
        header("Location: play.php?game_id=$game_id");
        exit();
    } else {
        echo "❌ Error al crear partida: " . $stmt->error;
        var_dump($_SESSION);
    }

    $stmt->close();
    $conn->close();
} else {
    echo "🚫 Método incorrecto. Se esperaba POST.";
}
?>

