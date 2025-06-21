<?php
session_start();

// Inicializar tablero vacío
if (!isset($_SESSION['cpu_tablero'])) {
    $_SESSION['cpu_tablero'] = [
        ['-', '-', '-'],
        ['-', '-', '-'],
        ['-', '-', '-'],
    ];
    $_SESSION['cpu_turno'] = true;
    $_SESSION['resultado'] = null;
    $_SESSION['ganadora'] = [];
}

// Función para evaluar si alguien ganó
function verificarGanador($t) {
    for ($i = 0; $i < 3; $i++) {
        if ($t[$i][0] !== '-' && $t[$i][0] === $t[$i][1] && $t[$i][1] === $t[$i][2]) return [$t[$i][0], [[$i,0],[$i,1],[$i,2]]];
        if ($t[0][$i] !== '-' && $t[0][$i] === $t[1][$i] && $t[1][$i] === $t[2][$i]) return [$t[0][$i], [[0,$i],[1,$i],[2,$i]]];
    }
    if ($t[0][0] !== '-' && $t[0][0] === $t[1][1] && $t[1][1] === $t[2][2]) return [$t[0][0], [[0,0],[1,1],[2,2]]];
    if ($t[0][2] !== '-' && $t[0][2] === $t[1][1] && $t[1][1] === $t[2][0]) return [$t[0][2], [[0,2],[1,1],[2,0]]];

    $empate = true;
    foreach ($t as $fila) foreach ($fila as $celda) if ($celda === '-') $empate = false;
    return $empate ? ['Empate', []] : null;
}

// Reiniciar solo variables del modo CPU
if (isset($_GET['reset'])) {
    unset($_SESSION['cpu_tablero'], $_SESSION['cpu_turno'], $_SESSION['resultado'], $_SESSION['ganadora']);
    header("Location: play_cpu.php");
    exit();
}
// Movimiento del jugador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fila'], $_POST['col'])) {
    $fila = $_POST['fila'];
    $col = $_POST['col'];
    $tablero = &$_SESSION['cpu_tablero'];

    if ($tablero[$fila][$col] === '-' && $_SESSION['cpu_turno'] && !$_SESSION['resultado']) {
        $tablero[$fila][$col] = 'X';
        $_SESSION['cpu_turno'] = false;
        $ver = verificarGanador($tablero);
        if ($ver) {
            $_SESSION['resultado'] = $ver[0];
            $_SESSION['ganadora'] = $ver[1];
        }
    }

    // Movimiento CPU
    if (!$_SESSION['cpu_turno'] && !$_SESSION['resultado']) {
        $vacías = [];
        foreach ($tablero as $i => $filaT) {
            foreach ($filaT as $j => $v) {
                if ($v === '-') $vacías[] = [$i, $j];
            }
        }
        if ($vacías) {
            $cpu = $vacías[array_rand($vacías)];
            $tablero[$cpu[0]][$cpu[1]] = 'O';
            $_SESSION['cpu_turno'] = true;
            $ver = verificarGanador($tablero);
            if ($ver) {
                $_SESSION['resultado'] = $ver[0];
                $_SESSION['ganadora'] = $ver[1];
            }
        }
    }
}

$tablero = $_SESSION['cpu_tablero'];
$resultado = $_SESSION['resultado'] ?? null;
$ganadora = $_SESSION['ganadora'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modo CPU</title>
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

        h1 { color: #00ffe7; font-size: 28px; margin-bottom: 5px; }
        h2 { color: #ff00ff; font-size: 18px; margin-bottom: 30px; }

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

        .cell:hover { transform: scale(1.05); }

        .locked { pointer-events: none; cursor: default; }
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

        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
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
            transition: 0.3s;
            box-shadow: 0 0 10px #a200ff;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px #ff00ff;
        }

        .background {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
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
    </style>
</head>
<body>

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

<h1>Gato vs CPU</h1>
<?php if ($resultado): ?>
    <h2>Resultado: <?= $resultado === 'X' ? 'Tú' : ($resultado === 'O' ? 'CPU' : 'Empate') ?></h2>
<?php endif; ?>

<div class="board">
    <?php for ($i = 0; $i < 3; $i++): ?>
        <?php for ($j = 0; $j < 3; $j++): ?>
            <?php
            $valor = $tablero[$i][$j];
            $isGanadora = in_array([$i, $j], $ganadora);
            $tipo = strtolower($valor);
            $clases = "cell " . ($valor !== '-' || $resultado ? "locked " : "") . ($isGanadora ? "ganadora " : "") . ($valor !== '-' ? $tipo : "");
            ?>
            <?php if ($valor === '-' && !$resultado): ?>
                <form method="POST" style="display: contents;">
                    <input type="hidden" name="fila" value="<?= $i ?>">
                    <input type="hidden" name="col" value="<?= $j ?>">
                    <button type="submit" class="<?= $clases ?>"></button>
                </form>
            <?php else: ?>
                <div class="<?= $clases ?>"><?= $valor === '-' ? '' : $valor ?></div>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endfor; ?>
</div>
<div class="buttons">
    <a class="btn" href="play_cpu.php?reset=1">🔁 Jugar de nuevo</a>
    <a class="btn" href="lobby.php">🏠 Volver al lobby</a>
</div>

<?php if ($resultado): ?>
<script>
    setTimeout(() => {
        Swal.fire({
            title: 'Resultado',
            text: '<?= $resultado === "Empate" ? "Empate" : ($resultado === "X" ? "¡Ganaste!" : "La CPU ganó") ?>',
            icon: '<?= $resultado === "X" ? "success" : ($resultado === "Empate" ? "info" : "error") ?>',
            confirmButtonColor: '#a200ff',
            background: '#1a1a1a',
            color: '#fff',
        });
    }, 500);
</script>
<?php endif; ?>

</body>
</html>
