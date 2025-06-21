-- Crear base de datos (ajusta el nombre si ya la tienes)
CREATE DATABASE IF NOT EXISTS tic_tac_toe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tic_tac_toe;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de partidas
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player1_id INT NOT NULL,
    player2_id INT DEFAULT NULL,
    turn INT NOT NULL,
    estado TEXT DEFAULT NULL,
    ganador INT DEFAULT NULL,
    status ENUM('waiting', 'playing', 'finished') DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player1_id) REFERENCES users(id),
    FOREIGN KEY (player2_id) REFERENCES users(id),
    FOREIGN KEY (ganador) REFERENCES users(id)
);
