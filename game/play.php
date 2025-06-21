<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$usuario = $_SESSION['user_id'];

if (!isset($_GET['game_id'])) {
    echo "ID de partida no proporcionado.";
    exit();
}

$gameId = $_GET['game_id'];
$stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
$stmt->bind_param("i", $gameId);
$stmt->execute();
$resultado = $stmt->get_result();
$partida = $resultado->fetch_assoc();

if (!$partida) {
    echo "Partida no encontrada.";
    exit();
}

$tablero = json_decode($partida['estado'], true);
if (!is_array($tablero)) {
    $tablero = [['-', '-', '-'], ['-', '-', '-'], ['-', '-', '-']];
}
$jugador1 = $partida['player1_id'];
$jugador2 = $partida['player2_id'];
$turno = $partida['turn'];
$ganador = $partida['ganador'];

if (!$jugador2 && $usuario !== $jugador1) {
    $stmt = $conn->prepare("UPDATE games SET player2_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $usuario, $gameId);
    $stmt->execute();
    $jugador2 = $usuario;
}

$esJugador1 = $usuario === $jugador1;
$esJugador2 = $usuario === $jugador2;
$miTurno = ($esJugador1 && $turno === 1) || ($esJugador2 && $turno === 2);

function verificarGanador($t) {
    for ($i = 0; $i < 3; $i++) {
        if ($t[$i][0] !== '-' && $t[$i][0] === $t[$i][1] && $t[$i][1] === $t[$i][2]) return [$t[$i][0], [[$i,0],[$i,1],[$i,2]]];
        if ($t[0][$i] !== '-' && $t[0][$i] === $t[1][$i] && $t[1][$i] === $t[2][$i]) return [$t[0][$i], [[0,$i],[1,$i],[2,$i]]];
    }
    if ($t[0][0] !== '-' && $t[0][0] === $t[1][1] && $t[1][1] === $t[2][2]) return [$t[0][0], [[0,0],[1,1],[2,2]]];
    if ($t[0][2] !== '-' && $t[0][2] === $t[1][1] && $t[1][1] === $t[2][0]) return [$t[0][2], [[0,2],[1,1],[2,0]]];

    $empate = true;
    foreach ($t as $fila) {
        foreach ($fila as $c) {
            if ($c === '-') $empate = false;
        }
    }
    return $empate ? ['Empate', []] : null;
}

$ganadora = [];
if ($ganador) {
    $ver = verificarGanador($tablero);
    $ganadora = $ver[1];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Partida #<?= $gameId ?></title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            margin: 0;
            background: #0a0a0a;
            font-family: 'Oxanium', sans-serif;
            color: white;
            text-align: center;
            padding-top: 40px;
        }

        h1 {
            color: #00ffe7;
            font-size: 28px;
            margin-bottom: 5px;
        }

        h2 {
            color: #ff00ff;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(3, 100px);
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
        }

        .cell {
            width: 100px;
            height: 100px;
            background: #1c1c1c;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .cell:hover {
            transform: scale(1.05);
        }

        .locked {
            pointer-events: none;
            cursor: default;
        }

        .x { color: #ff3c3c; }
        .o { color: #3c9dff; }

        .ganadora {
            background: linear-gradient(145deg, #00ffc3, #00ccff);
            color: black !important;
            animation: glow 1s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 10px #00fff2; }
            to { box-shadow: 0 0 20px #00ccff, 0 0 30px #00fff2; }
        }

        .btn {
            padding: 12px 22px;
            background: linear-gradient(to right, #5f00ff, #ff00ff);
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            box-shadow: 0 0 10px #a200ff;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px #ff00ff;
        }
    </style>
</head>
<body>
    <h1>Partida #<?= $gameId ?></h1>

    <?php if ($ganador): ?>
        <h2>¡Ganador: <?= $ganador === 'Empate' ? 'Empate' : ($ganador === 'X' ? $jugador1 : $jugador2) ?>!</h2>
    <?php else: ?>
        <?php if ($miTurno): ?>
    <h2>🎯 Tu turno (<?= $esJugador1 ? 'Jugador 1' : 'Jugador 2' ?>)</h2>
<?php else: ?>
    <h2>⌛ Esperando turno del otro jugador...</h2>
<?php endif; ?>
    <?php endif; ?>

    <div class="board">
        <?php for ($i = 0; $i < 3; $i++): ?>
            <?php for ($j = 0; $j < 3; $j++): ?>
                <?php
                $valor = $tablero[$i][$j];
                $tipo = strtolower($valor);
                $bloqueado = $valor !== '-' || !$miTurno || $ganador;
                $clases = "cell " . ($bloqueado ? 'locked ' : '') . ($valor !== '-' ? $tipo : '') . (in_array([$i, $j], $ganadora) ? ' ganadora' : '');
                ?>
                <?php if (!$bloqueado): ?>
                    <form method="POST" action="update_game.php" style="display: contents;">
                        <input type="hidden" name="fila" value="<?= $i ?>">
                        <input type="hidden" name="col" value="<?= $j ?>">
                        <input type="hidden" name="game_id" value="<?= $gameId ?>">
                        <button type="submit" class="<?= $clases ?>"></button>
                    </form>
                <?php else: ?>
                    <div class="<?= $clases ?>"><?= $valor !== '-' ? $valor : '' ?></div>
                <?php endif; ?>
            <?php endfor; ?>
        <?php endfor; ?>
    </div>
    <a class="btn" href="lobby.php">← Volver al lobby</a>

    <?php if ($ganador): ?>
        <script>
            setTimeout(() => {
                Swal.fire({
                    title: 'Resultado',
                    text: '<?= $ganador === "Empate" ? "¡Empate!" : "Ganador: " . ($ganador === "X" ? $jugador1 : $jugador2) ?>',
                    icon: '<?= $ganador === "Empate" ? "info" : "success" ?>',
                    confirmButtonColor: '#a200ff',
                    background: '#1a1a1a',
                    color: '#fff',
                });
            }, 300);
        </script>
    <?php endif; ?>
</body>
</html>

