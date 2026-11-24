-- =============================================================================
-- FLOCK — Agenda Cultural Municipal (Ayuntamiento de Villaverde de la Ribera)
-- Script de Creación y Población de Base de Datos (schema.sql)
-- Arquitectura: Web Tradicional (PHP 8.x + MySQL/MariaDB con PDO)
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `flock_db` 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `flock_db`;

-- Desactivar temporalmente la verificación de claves foráneas para limpieza segura
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `inscripciones`;
DROP TABLE IF EXISTS `eventos`;
DROP TABLE IF EXISTS `espacios_municipales`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `usuarios`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- 1. Tabla: usuarios (Gestión y Control de Acceso de la Concejalía)
-- -----------------------------------------------------------------------------
CREATE TABLE `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `nombre_completo` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `rol` ENUM('admin', 'gestor') NOT NULL DEFAULT 'gestor',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Tabla: categorias (Ejes Temáticos Culturales)
-- -----------------------------------------------------------------------------
CREATE TABLE `categorias` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(60) NOT NULL UNIQUE,
  `slug` VARCHAR(60) NOT NULL UNIQUE,
  `color_badge` VARCHAR(20) NOT NULL DEFAULT '#4a5568',
  `descripcion` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Tabla: espacios_municipales (Red de Sedes y Recintos Públicos)
-- -----------------------------------------------------------------------------
CREATE TABLE `espacios_municipales` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `direccion` VARCHAR(150) NOT NULL,
  `aforo_maximo` INT UNSIGNED NOT NULL,
  `descripcion` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. Tabla: eventos (Entidad Principal del Catálogo de la Agenda)
-- -----------------------------------------------------------------------------
CREATE TABLE `eventos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `categoria_id` INT UNSIGNED NOT NULL,
  `espacio_id` INT UNSIGNED NOT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `fecha` DATE NOT NULL,
  `hora` TIME NOT NULL,
  `aforo_total` INT UNSIGNED NOT NULL,
  `precio` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `imagen_url` VARCHAR(255) NULL,
  `estado` ENUM('activo', 'cancelado', 'finalizado') NOT NULL DEFAULT 'activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_eventos_categoria` 
    FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_eventos_espacio` 
    FOREIGN KEY (`espacio_id`) REFERENCES `espacios_municipales` (`id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_eventos_fecha` (`fecha`),
  INDEX `idx_eventos_categoria` (`categoria_id`),
  INDEX `idx_eventos_espacio` (`espacio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. Tabla: inscripciones (Entidad Relacionada: Reservas de Vecinos/Asistentes)
-- -----------------------------------------------------------------------------
CREATE TABLE `inscripciones` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `evento_id` INT UNSIGNED NOT NULL,
  `localizador` VARCHAR(20) NOT NULL UNIQUE,
  `nombre_asistente` VARCHAR(100) NOT NULL,
  `email_asistente` VARCHAR(120) NOT NULL,
  `telefono_asistente` VARCHAR(20) NOT NULL,
  `plazas_reservadas` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `observaciones` TEXT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('confirmada', 'cancelada') NOT NULL DEFAULT 'confirmada',
  CONSTRAINT `fk_inscripciones_evento` 
    FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) 
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX `idx_inscripciones_evento` (`evento_id`),
  INDEX `idx_inscripciones_email` (`email_asistente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- INSERCIÓN DE DATOS SEMILLA FICTICIOS DE PRUEBA
-- =============================================================================

-- Usuarios administradores y gestores (Contraseñas hasheadas con BCRYPT)
-- admin: admin123
-- gestor_cultura: cultura2026
INSERT INTO `usuarios` (`username`, `password_hash`, `nombre_completo`, `email`, `rol`) VALUES
('admin', '$2y$10$Jg1vGc9vA64soh4RDcq5i.nNyBBrJTgLDMN0xLixWn7h.MDhYGdKy', 'Administrador General', 'cultura@villaverde.es', 'admin'),
('gestor_cultura', '$2y$10$UpafeD1ydt7kJXhWYpGhmeb2zmiukQCJ.idEVjq02dZE4CJCAB8N.', 'Laura Morales (Técnica de Cultura)', 'lmorales@villaverde.es', 'gestor');

-- Categorías culturales contemporáneas
INSERT INTO `categorias` (`nombre`, `slug`, `color_badge`, `descripcion`) VALUES
('Festivales y Encuentros', 'festivales', '#e63946', 'Encuentros participativos, ferias comunitarias y macroeventos urbanos.'),
('Cinefórum y Audiovisual', 'cineforum', '#457b9d', 'Proyecciones de cine independiente, cortometrajes y debates temáticos.'),
('Música en Directo', 'musica-directo', '#2a9d8f', 'Conciertos acústicos, bandas emergentes y ciclos de música contemporánea.'),
('Arte Urbano y Exposiciones', 'arte-urbano', '#e76f51', 'Muestras de fotografía, escultura, ilustración y muralismo en espacios públicos.'),
('Charlas y Conferencias', 'charlas-conferencias', '#6a4c93', 'Ponencias divulgativas, foros de debate y encuentros participativos.');

-- Espacios municipales emblemáticos
INSERT INTO `espacios_municipales` (`nombre`, `direccion`, `aforo_maximo`, `descripcion`) VALUES
('Espacio Joven "La Nave"', 'Calle Ribera 14', 120, 'Antigua nave industrial rehabilitada para eventos juveniles, arte y acústicos.'),
('Auditorio Municipal Ribera', 'Avenida de la Cultura 2', 350, 'Recinto escénico principal con acústica profesional y graderío accesible.'),
('Centro Cívico "El Molino"', 'Plaza del Molino 5', 60, 'Salas polivalentes vecinales para conferencias, charlas y mesas redondas.'),
('Plaza de las Artes', 'Paseo Central s/n', 500, 'Ágora peatonal al aire libre para ferias culturales y festivales masivos.'),
('Cinefórum Central', 'Calle Mayor 31', 90, 'Sala cinematográfica municipal equipada con proyector digital y debate.');

-- Eventos programados (Distribuidos en noviembre y diciembre 2026)
INSERT INTO `eventos` (`categoria_id`, `espacio_id`, `titulo`, `descripcion`, `fecha`, `hora`, `aforo_total`, `precio`, `imagen_url`, `estado`) VALUES
(1, 4, 'Festival Urbano de Otoño Villaverde', 'Encuentro cultural interdisciplinar al aire libre con música en directo, creadores emergentes y food trucks comunitarios.', '2026-11-20', '18:00:00', 400, 0.00, 'festival-otono.webp', 'activo'),
(2, 5, 'Ciclo Cine Social: Proyección y Coloquio "Voces Vecinales"', 'Proyección del documental galardonado seguido de mesa redonda participativa con su director y asociaciones locales.', '2026-11-21', '19:30:00', 80, 0.00, 'cine-social.webp', 'activo'),
(3, 1, 'Noche de Música Acústica Emergente', 'Concierto íntimo con tres cantautores y bandas locales emergentes en formato desenchufado.', '2026-11-22', '20:00:00', 100, 3.50, 'musica-acustica.webp', 'activo'),
(4, 1, 'Inauguración: Exposición de Fotografía y Arte Urbano', 'Muestra colectiva de jóvenes talentos visuales de la comarca con visita guiada y encuentro con los artistas.', '2026-11-24', '18:30:00', 90, 0.00, 'arte-urbano-exp.webp', 'activo'),
(5, 3, 'Conferencia: Sostenibilidad Urbana y Huertos Comunitarios', 'Charla interactiva a cargo de especialistas en agroecología urbana y modelos de huertos ciudadanos participativos.', '2026-11-25', '17:30:00', 50, 0.00, 'charla-huertos.webp', 'activo'),
(3, 2, 'Gala Coral de Invierno de la Ribera', 'Recital coral sinfónico con agrupaciones locales interpretando repertorio clásico y contemporáneo.', '2026-11-27', '20:30:00', 300, 5.00, 'gala-coral.webp', 'activo'),
(1, 4, 'Feria de Intercambio Cultural y Autoedición (Fanzines)', 'Jornada matinal con puestos de colectivos locales, fanzines independientes, ilustración y música ambiental.', '2026-11-28', '11:00:00', 250, 0.00, 'feria-fanzines.webp', 'activo'),
(2, 5, 'Cinefórum Juvenil: Nuevas Narrativas Digitales', 'Sesión matinal con cortometrajes independientes y debate abierto sobre el impacto de la inteligencia visual.', '2026-11-29', '11:30:00', 85, 0.00, 'cineforum-digital.webp', 'activo'),
(5, 3, 'Mesa de Debate: El Futuro de las Artes Escénicas de Cercanía', 'Encuentro participativo entre compañías de teatro de barrio y gestores públicos para trazar el plan cultural 2027.', '2026-12-01', '19:00:00', 55, 0.00, 'debate-artes.webp', 'activo'),
(3, 1, 'Muestra de Jazz y Fusión de Medianoche', 'Sesión especial de jazz contemporáneo con músicos de conservatorio y jam session posterior.', '2026-12-02', '21:00:00', 110, 4.00, 'jazz-fusion.webp', 'activo');

-- Inscripciones ciudadanas ficticias de prueba (Demuestran el cálculo de aforo disponible)
INSERT INTO `inscripciones` (`evento_id`, `localizador`, `nombre_asistente`, `email_asistente`, `telefono_asistente`, `plazas_reservadas`, `observaciones`, `estado`) VALUES
(1, 'FLK-2026-A1B2', 'Carlos Méndez Vega', 'carlos.mendez@example.com', '611223344', 2, 'Acceso con carrito infantil.', 'confirmada'),
(1, 'FLK-2026-C3D4', 'Beatriz Gómez Ruiz', 'beatriz.gomez@example.com', '622334455', 4, NULL, 'confirmada'),
(2, 'FLK-2026-E5F6', 'Alejandro Sanz Vidal', 'alejandro.sv@example.com', '633445566', 1, 'Asistencia al coloquio posterior.', 'confirmada'),
(2, 'FLK-2026-G7H8', 'Elena Morales Prado', 'elena.morales@example.com', '644556677', 2, NULL, 'confirmada'),
(3, 'FLK-2026-I9J0', 'David Navarro Cruz', 'david.navarro@example.com', '655667788', 3, 'Mesa cercana al escenario si es posible.', 'confirmada'),
(5, 'FLK-2026-K1L2', 'Lucía Torres Fuentes', 'lucia.torres@example.com', '666778899', 2, 'Interés en participar en el huerto comunitario.', 'confirmada');
