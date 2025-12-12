-- db_init.sql
-- Script para crear la base de datos y tablas mínimas para BusEdu

CREATE DATABASE IF NOT EXISTS `busedu_mysql` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `busedu_mysql`;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(191) NOT NULL,
  `username` VARCHAR(191) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` VARCHAR(32) NOT NULL DEFAULT 'user',
  `password_reset_token` VARCHAR(255) DEFAULT NULL,
  `password_reset_expires` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla estudiantes
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(191) NOT NULL,
  `parada` VARCHAR(255) DEFAULT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla asistencia
CREATE TABLE IF NOT EXISTS `asistencia` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `estudiante_id` INT UNSIGNED NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` ENUM('presente','ausente') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY (`estudiante_id`),
  CONSTRAINT `asistencia_estudiante_fk` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla rutas (básica)
CREATE TABLE IF NOT EXISTS `rutas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(191) NOT NULL,
  `conductor` VARCHAR(191) DEFAULT NULL,
  `paradas` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario admin por defecto (contraseña: 123456) - solo para desarrollo
INSERT INTO `usuarios` (`nombre`, `username`, `password`, `rol`)
SELECT 'Administrador BusEdu', 'admin@busedu.com', '$2y$10$e0NRpWlXSv5G9jz2VY9S7OXc2Yqj1GQ1/3yq7dV8X1z9c8c0hQwqK', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM `usuarios` WHERE `username` = 'admin@busedu.com');


