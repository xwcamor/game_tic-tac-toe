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
    <title>Registro - Tic Tac Toe</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Oxanium:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Oxanium', cursive;
            background: radial-gradient(circle, #0a0f1e, #020409);
            color: #e0ffe5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .register-box {
            background: rgba(25, 25, 35, 0.9);
            padding: 40px 30px;
            border-radius: 18px;
            box-shadow: 0 0 20px #00ffbb55;
            width: 340px;
            text-align: center;
            animation: fadeInUp 1s ease-out;
        }

        .register-box h2 {
            margin-bottom: 25px;
            color: #00ffcc;
            text-shadow: 0 0 8px #00ffd5;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #00ffbb44;
            border-radius: 10px;
            background: #101622;
            color: #e0ffe5;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #00ffc3;
            box-shadow: 0 0 8px #00ffc3;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #00ffbb, #0099ff);
            border: none;
            border-radius: 10px;
            color: #000;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: linear-gradient(45deg, #0099ff, #00ffbb);
            box-shadow: 0 0 15px #00ffc3;
            transform: scale(1.05);
        }

        .link {
            margin-top: 18px;
            display: block;
            color: #00ffc3;
            font-size: 13px;
            text-decoration: none;
        }

        .link:hover {
            text-shadow: 0 0 10px #00ffc3;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <div class="register-box">
        <h2>🛡 Crear Cuenta</h2>

        <form action="procesar_registro.php" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Usuario" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
            </div>

            <button class="btn-register" type="submit">Registrarse</button>
        </form>

        <a class="link" href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
    </div>

</body>
</html>
