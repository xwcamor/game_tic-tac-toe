<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Obtener victorias
$stmt = $conn->prepare("SELECT COUNT(*) FROM games WHERE ganador = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($victorias);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lobby - Tic Tac Toe</title>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Oxanium', sans-serif;
            background-color: #0a0a0a;
            color: white;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .background {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: 0;
            pointer-events: none; /* <- Esta línea habilita los clics en el contenido */
        }

        .background span {
            position: absolute;
            font-size: 3rem;
            animation: float 10s linear infinite;
            opacity: 0.08;
        }

        .background .x { color: cyan; }
        .background .o { color: magenta; }

        @keyframes float {
            from { transform: translateY(0) rotate(0deg); }
            to   { transform: translateY(-100vh) rotate(360deg); }
        }

        h2 {
            font-size: 32px;
            color: #00ffe7;
            margin-bottom: 8px;
            text-shadow: 0 0 10px #00ffe7;
        }

        h3 {
            font-size: 20px;
            color: #ff4ef8;
            margin-bottom: 30px;
            text-shadow: 0 0 6px #ff4ef8;
        }

        .button-group {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .btn {
            padding: 14px 24px;
            background: linear-gradient(135deg, #5f00ff, #ff00ff);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 0 16px #ff00ff;
        }

        .btn:hover {
            transform: scale(1.07);
            background: linear-gradient(135deg, #ff00ff, #5f00ff);
            box-shadow: 0 0 20px #ffffff;
        }

        .section-title {
            color: #ff44aa;
            font-size: 22px;
            margin-top: 30px;
            text-shadow: 0 0 8px #ff44aa;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        li a {
            color: #ffffff;
            font-weight: bold;
            text-decoration: none;
            font-size: 18px;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px 15px;
            margin: 5px auto;
            display: inline-block;
            border-radius: 10px;
            box-shadow: 0 0 8px #ffffff22;
            transition: all 0.25s ease-in-out;
        }

        li a:hover {
            background: #00ffc3;
            color: #000;
            box-shadow: 0 0 12px #00ffc3, 0 0 20px #00ffc3;
        }
    </style>
</head>
<body>

    <!-- Fondo decorativo -->
    <div class="background">
        <?php
        $chars = ['x', 'o'];
        for ($i = 0; $i < 40; $i++):
            $char = $chars[rand(0, 1)];
            $top = rand(0, 100);
            $left = rand(0, 100);
            $delay = rand(0, 20) / 10;
        ?>
            <span class="<?= $char ?>" style="top: <?= $top ?>vh; left: <?= $left ?>vw; animation-delay: <?= $delay ?>s;">
                <?= strtoupper($char) ?>
            </span>
        <?php endfor; ?>
    </div>

    <h2>Bienvenido, <?= htmlspecialchars($username) ?> 👋</h2>
    <h3>🏆 Has ganado <?= $victorias ?> partida<?= $victorias === 1 ? '' : 's' ?>.</h3>

    <div class="button-group">
        <form action="create_game.php" method="post" style="display:inline;">
    <button type="submit" class="btn">➕ Crear nueva partida multijugador</button>
</form>
        <a href="play_cpu.php" class="btn">🧠 Jugar contra la CPU</a>
        <a href="ranking.php" class="btn">🏅 Ver Ranking</a>
        <a href="../auth/logout.php" class="btn">🚪 Cerrar sesión</a>
    </div>

    <h3 class="section-title">Partidas disponibles para unirse:</h3>
    <ul>
    <?php
    $result = $conn->query("SELECT id FROM games WHERE status = 'waiting' AND player1_id != $user_id");

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='join_game.php?game_id={$row['id']}'>Unirse a partida #{$row['id']}</a></li>";
        }
    } else {
        echo "<li>No hay partidas disponibles en este momento.</li>";
    }
    ?>
    </ul>

</body>
</html>



