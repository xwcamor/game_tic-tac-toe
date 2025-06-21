<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../game/lobby.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Tic Tac Toe Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&family=Oxanium:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Oxanium', sans-serif;
            background: radial-gradient(circle at center, #0a0a0a, #111111 90%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
            overflow: hidden;
        }

        .floating {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .xo {
            position: absolute;
            font-size: 50px;
            opacity: 0.08;
            font-weight: bold;
            animation: floatXO 12s linear infinite;
            user-select: none;
        }

        @keyframes floatXO {
            0%   { transform: translateY(0) rotate(0deg); opacity: 0.1; }
            50%  { transform: translateY(-40px) rotate(180deg); opacity: 0.2; }
            100% { transform: translateY(0) rotate(360deg); opacity: 0.1; }
        }

        .login-container {
            background: #111;
            padding: 40px 30px;
            border-radius: 18px;
            box-shadow: 0 0 20px #ff00ff55;
            width: 340px;
            text-align: center;
            z-index: 1;
            position: relative;
            animation: fadeIn 1s ease-out;
        }

        .game-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 22px;
            color: #00ffe7;
            text-shadow: 0 0 10px #00ffe7;
            margin-bottom: 5px;
        }

        .game-subtitle {
            font-size: 13px;
            color: #ff69e1;
            margin-bottom: 25px;
            font-style: italic;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: #1a1a1a;
            border: 2px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #00ffc3;
            box-shadow: 0 0 10px #00ffc3;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #5f00ff, #ff00ff);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px #ff00ff80;
        }

        .btn-login:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px #ffffffaa;
        }

        .link {
            margin-top: 20px;
            display: block;
            color: #00ffc3;
            font-size: 13px;
            text-decoration: none;
        }

        .link:hover {
            text-shadow: 0 0 10px #00ffc3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Fondo animado con muchos X y O -->
    <div class="floating">
        <?php
        $colors = ['#00ffc3', '#ff00ff', '#00ffff', '#39ff14', '#ff0077', '#00ccff', '#f0f', '#ff6666', '#bada55', '#ff9933'];
        $letters = ['X', 'O'];
        for ($i = 0; $i < 50; $i++) {
            $top = rand(2, 95) . '%';
            $left = rand(2, 95) . '%';
            $color = $colors[array_rand($colors)];
            $letter = $letters[array_rand($letters)];
            $delay = rand(0, 100) / 10 . 's';
            echo "<span class='xo' style='top:$top; left:$left; color:$color; animation-delay:$delay;'>$letter</span>";
        }
        ?>
    </div>

    <!-- Login Box -->
    <div class="login-container">
        <div class="icon">🎯</div>
        <div class="game-title">TIC-TAC-TOE ARENA</div>
        <div class="game-subtitle">Domina el tablero, jugador...</div>

        <form action="procesar_login.php" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Usuario" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <button class="btn-login" type="submit">Ingresar</button>
        </form>

        <a href="register.php" class="link">¿No tienes cuenta? Regístrate</a>
    </div>

</body>
</html>
