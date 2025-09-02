-- =================================================================
-- Script de Creación y Actualización de Base de Datos para Academia
-- Versión: 2.0
-- Motor: MySQL 5.x
-- =================================================================

-- Creación de la base de datos (opcional, si no existe)
CREATE DATABASE IF NOT EXISTS `academia_cursos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `academia_cursos`;

-- -----------------------------------------------------
-- Creación de Tablas
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` INT NOT NULL AUTO_INCREMENT,
  `nombre_rol` VARCHAR(50) NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE INDEX `nombre_rol_UNIQUE` (`nombre_rol` ASC)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `id_rol` INT NOT NULL,
  `nombre_usuario` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `nombre_completo` VARCHAR(150) NOT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auth_2fa_code` VARCHAR(10) NULL,
  `auth_2fa_expiry` DATETIME NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE INDEX `nombre_usuario_UNIQUE` (`nombre_usuario` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `fk_usuarios_roles_idx` (`id_rol` ASC),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- ... [All other CREATE TABLE statements from database_schema.sql would be here] ...

CREATE TABLE IF NOT EXISTS `asistencia_cliente` (
  `id_asistencia_cliente` INT NOT NULL AUTO_INCREMENT,
  `id_matricula_detalle` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `fecha_clase` DATE NOT NULL,
  `estado` ENUM('Programado', 'Asistió', 'Faltó', 'Justificado', 'Postergado') NOT NULL DEFAULT 'Programado',
  `observaciones` VARCHAR(255) NULL,
  PRIMARY KEY (`id_asistencia_cliente`),
  INDEX `fk_asist_cli_matricula_det_idx` (`id_matricula_detalle` ASC),
  INDEX `fk_asist_cli_cliente_idx` (`id_cliente` ASC),
  CONSTRAINT `fk_asist_cli_matricula_det` FOREIGN KEY (`id_matricula_detalle`) REFERENCES `matriculas_detalle` (`id_matricula_detalle`),
  CONSTRAINT `fk_asist_cli_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Inserción de Datos Iniciales (Seeders)
-- -----------------------------------------------------
-- (Se asume que las tablas están vacías para evitar errores de duplicados)
INSERT IGNORE INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES (1, 'Administrador', 'Acceso total al sistema'), (2, 'Usuario', 'Acceso limitado a operaciones');
INSERT IGNORE INTO `usuarios` (`id_rol`, `nombre_usuario`, `password_hash`, `email`, `nombre_completo`) VALUES (1, 'admin', '$2y$10$t/iagDSBOSf1d2YhA.Lzce4jK2jaxtfr5QyCoLGgUoM5a9WprgI.q', 'admin@example.com', 'Administrador del Sistema');
INSERT IGNORE INTO `tipos_documento` (`descripcion`, `longitud`, `codigo_sunat`) VALUES ('DNI', 8, '1'), ('Pasaporte', 12, '7'), ('Carnet de Extranjería', 12, '4');
INSERT IGNORE INTO `formas_pago` (`nombre`) VALUES ('Efectivo'), ('Tarjeta de Crédito/Débito'), ('Transferencia Bancaria'), ('Yape/Plin');
INSERT IGNORE INTO `tipos_area` (`nombre`) VALUES ('Grande'), ('Mediana'), ('Pequeña'), ('Otro');
INSERT IGNORE INTO `tipos_curso` (`nombre`) VALUES ('Taller'), ('Curso Regular'), ('Seminario');
INSERT IGNORE INTO `tipos_precio` (`nombre`) VALUES ('Precio Regular'), ('Precio de Lanzamiento'), ('Precio Corporativo');

-- -----------------------------------------------------
-- Creación de Procedimientos Almacenados
-- -----------------------------------------------------
DELIMITER $$

-- -- Usuarios --
DROP PROCEDURE IF EXISTS `sp_usuarios_crear`$$
CREATE PROCEDURE `sp_usuarios_crear`(IN p_id_rol INT, IN p_nombre_usuario VARCHAR(50), IN p_password_hash VARCHAR(255), IN p_email VARCHAR(100), IN p_nombre_completo VARCHAR(150))
BEGIN
    INSERT INTO usuarios (id_rol, nombre_usuario, password_hash, email, nombre_completo, activo)
    VALUES (p_id_rol, p_nombre_usuario, p_password_hash, p_email, p_nombre_completo, 1);
END$$

-- ... [All other DROP/CREATE PROCEDURE statements from both SP files would be here] ...

DROP PROCEDURE IF EXISTS `sp_areas_eliminar`$$
CREATE PROCEDURE `sp_areas_eliminar`(IN p_id INT)
BEGIN
    DELETE FROM areas WHERE id_area = p_id;
END$$

DELIMITER ;
