<?php
session_start();
require '../config/db.php';

$query = "
    SELECT u.username, COUNT(*) as victorias
    FROM users u
    JOIN games g ON g.ganador = u.id
    WHERE g.ganador IS NOT NULL AND g.ganador != -1
    GROUP BY u.id
    ORDER BY victorias DESC
    LIMIT 10
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking de jugadores</title>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Oxanium', sans-serif;
            background-color: #0a0a0a;
            color: white;
            text-align: center;
            padding-top: 50px;
            margin: 0;
            overflow: hidden;
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

        h1 {
            color: #00ffe7;
            margin-bottom: 30px;
            font-size: 32px;
            text-shadow: 0 0 10px #00ffe7;
        }

        .table-container {
            background: #1b1b1b;
            border-radius: 16px;
            padding: 30px;
            display: inline-block;
            box-shadow: 0 0 20px #00ffc3;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 24px;
            border-bottom: 1px solid #444;
        }

        th {
            background-color: #00ffc3;
            color: black;
            border-radius: 8px 8px 0 0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #222;
        }

        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: linear-gradient(to right, #5f00ff, #ff00ff);
            color: white;
            font-weight: bold;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0 0 12px #ff00ff;
        }

        .btn:hover {
            box-shadow: 0 0 24px #ff00ff;
        }
    </style>
</head>
<body>

    <!-- Fondo animado X/O -->
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

    <h1>🏆 Ranking de Jugadores</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Victorias</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= $row['victorias'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a class="btn" href="lobby.php">← Volver al lobby</a>

</body>
</html>
