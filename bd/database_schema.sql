-- =================================================================
-- Script de Creación de Base de Datos para Academia de Cursos
-- Versión: 1.0
-- Motor: MySQL 5.x
-- =================================================================

-- Creación de la base de datos (opcional, si no existe)
CREATE DATABASE IF NOT EXISTS `academia_cursos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `academia_cursos`;

-- -----------------------------------------------------
-- Tabla `roles`
-- Almacena los roles de los usuarios del sistema (Administrador, Usuario, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` INT NOT NULL AUTO_INCREMENT,
  `nombre_rol` VARCHAR(50) NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE INDEX `nombre_rol_UNIQUE` (`nombre_rol` ASC)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `usuarios`
-- Almacena los usuarios que pueden acceder al sistema.
-- -----------------------------------------------------
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
  CONSTRAINT `fk_usuarios_roles`
    FOREIGN KEY (`id_rol`)
    REFERENCES `roles` (`id_rol`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `tipos_documento`
-- Maestro de tipos de documento de identidad (DNI, Pasaporte, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_documento` (
  `id_tipo_documento` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(50) NOT NULL,
  `longitud` INT NULL,
  `codigo_sunat` VARCHAR(2) NULL,
  PRIMARY KEY (`id_tipo_documento`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `clientes`
-- Almacena la información de los clientes/alumnos.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `id_cliente` INT NOT NULL AUTO_INCREMENT,
  `id_tipo_documento` INT NOT NULL,
  `numero_documento` VARCHAR(20) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NULL,
  `telefono` VARCHAR(20) NULL,
  `codigo_erp` VARCHAR(20) NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cliente`),
  UNIQUE INDEX `documento_UNIQUE` (`id_tipo_documento` ASC, `numero_documento` ASC),
  INDEX `fk_clientes_tipos_documento_idx` (`id_tipo_documento` ASC),
  CONSTRAINT `fk_clientes_tipos_documento`
    FOREIGN KEY (`id_tipo_documento`)
    REFERENCES `tipos_documento` (`id_tipo_documento`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `tipos_area`
-- Maestro de tipos de área (Grande, Mediana, Pequeña, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_area` (
  `id_tipo_area` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_tipo_area`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `areas`
-- Áreas principales de la academia.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `areas` (
  `id_area` INT NOT NULL AUTO_INCREMENT,
  `id_tipo_area` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_area`),
  INDEX `fk_areas_tipos_area_idx` (`id_tipo_area` ASC),
  CONSTRAINT `fk_areas_tipos_area`
    FOREIGN KEY (`id_tipo_area`)
    REFERENCES `tipos_area` (`id_tipo_area`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `sub_areas`
-- Sub-áreas o salones donde se dictan los cursos.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sub_areas` (
  `id_sub_area` INT NOT NULL AUTO_INCREMENT,
  `id_area` INT NOT NULL,
  `descripcion` VARCHAR(100) NOT NULL,
  `numero_sub_area` VARCHAR(20) NULL,
  `capacidad_maxima` INT NOT NULL,
  PRIMARY KEY (`id_sub_area`),
  INDEX `fk_sub_areas_areas_idx` (`id_area` ASC),
  CONSTRAINT `fk_sub_areas_areas`
    FOREIGN KEY (`id_area`)
    REFERENCES `areas` (`id_area`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `profesores`
-- Mantenimiento de profesores.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `profesores` (
  `id_profesor` INT NOT NULL AUTO_INCREMENT,
  `id_tipo_documento` INT NOT NULL,
  `numero_documento` VARCHAR(20) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NULL,
  `telefono` VARCHAR(20) NULL,
  `especialidad` VARCHAR(255) NULL,
  PRIMARY KEY (`id_profesor`),
  UNIQUE INDEX `documento_UNIQUE` (`id_tipo_documento` ASC, `numero_documento` ASC),
  INDEX `fk_profesores_tipos_documento_idx` (`id_tipo_documento` ASC),
  CONSTRAINT `fk_profesores_tipos_documento`
    FOREIGN KEY (`id_tipo_documento`)
    REFERENCES `tipos_documento` (`id_tipo_documento`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `tipos_curso`
-- Maestro de tipos de curso.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_curso` (
  `id_tipo_curso` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_tipo_curso`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `cursos`
-- Mantenimiento de cursos que ofrece la academia.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cursos` (
  `id_curso` INT NOT NULL AUTO_INCREMENT,
  `id_tipo_curso` INT NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NULL,
  `codigo_erp` VARCHAR(20) NULL,
  PRIMARY KEY (`id_curso`),
  INDEX `fk_cursos_tipos_curso_idx` (`id_tipo_curso` ASC),
  CONSTRAINT `fk_cursos_tipos_curso`
    FOREIGN KEY (`id_tipo_curso`)
    REFERENCES `tipos_curso` (`id_tipo_curso`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `tipos_horario`
-- Permite crear patrones de días (L-M-V, S-D, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_horario` (
  `id_tipo_horario` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(100) NOT NULL,
  `dias_semana` VARCHAR(20) NOT NULL COMMENT 'Ej: 1,3,5 para L,M,V (Lunes=1)',
  PRIMARY KEY (`id_tipo_horario`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `cursos_programados`
-- Almacena la programación de un curso en un horario y lugar específico.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cursos_programados` (
  `id_curso_programado` INT NOT NULL AUTO_INCREMENT,
  `id_curso` INT NOT NULL,
  `id_profesor` INT NOT NULL,
  `id_sub_area` INT NOT NULL,
  `id_tipo_horario` INT NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `vacantes_disponibles` INT NOT NULL,
  `estado` ENUM('Programado', 'En Curso', 'Finalizado', 'Cancelado') NOT NULL DEFAULT 'Programado',
  PRIMARY KEY (`id_curso_programado`),
  INDEX `fk_prog_curso_idx` (`id_curso` ASC),
  INDEX `fk_prog_profesor_idx` (`id_profesor` ASC),
  INDEX `fk_prog_sub_area_idx` (`id_sub_area` ASC),
  INDEX `fk_prog_tipo_horario_idx` (`id_tipo_horario` ASC),
  CONSTRAINT `fk_prog_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `fk_prog_profesor` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`),
  CONSTRAINT `fk_prog_sub_area` FOREIGN KEY (`id_sub_area`) REFERENCES `sub_areas` (`id_sub_area`),
  CONSTRAINT `fk_prog_tipo_horario` FOREIGN KEY (`id_tipo_horario`) REFERENCES `tipos_horario` (`id_tipo_horario`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `tipos_precio`
-- Maestro de tipos de precio (Regular, Corporativo, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_precio` (
  `id_tipo_precio` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_tipo_precio`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `lista_precios`
-- Lista de precios por curso, con vigencia.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lista_precios` (
  `id_lista_precio` INT NOT NULL AUTO_INCREMENT,
  `id_curso` INT NOT NULL,
  `id_tipo_precio` INT NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `vigencia_inicio` DATE NOT NULL,
  `vigencia_fin` DATE NOT NULL,
  PRIMARY KEY (`id_lista_precio`),
  INDEX `fk_precios_curso_idx` (`id_curso` ASC),
  INDEX `fk_precios_tipo_precio_idx` (`id_tipo_precio` ASC),
  CONSTRAINT `fk_precios_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `fk_precios_tipo_precio` FOREIGN KEY (`id_tipo_precio`) REFERENCES `tipos_precio` (`id_tipo_precio`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `formas_pago`
-- Maestro de formas de pago (Efectivo, Tarjeta, etc.)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `formas_pago` (
  `id_forma_pago` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_forma_pago`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `matriculas`
-- Cabecera de la matrícula de un cliente.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matriculas` (
  `id_matricula` INT NOT NULL AUTO_INCREMENT,
  `id_cliente` INT NOT NULL,
  `id_usuario_registro` INT NOT NULL,
  `id_forma_pago` INT NOT NULL,
  `fecha_matricula` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_inicio_clases` DATE NOT NULL,
  `fecha_fin_clases` DATE NOT NULL,
  `monto_total` DECIMAL(10,2) NOT NULL,
  `descuento_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `monto_final` DECIMAL(10,2) NOT NULL,
  `observaciones` TEXT NULL,
  `estado` ENUM('Activa', 'Completada', 'Anulada') NOT NULL DEFAULT 'Activa',
  PRIMARY KEY (`id_matricula`),
  INDEX `fk_matriculas_cliente_idx` (`id_cliente` ASC),
  INDEX `fk_matriculas_usuario_idx` (`id_usuario_registro` ASC),
  INDEX `fk_matriculas_forma_pago_idx` (`id_forma_pago` ASC),
  CONSTRAINT `fk_matriculas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_matriculas_usuario` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_matriculas_forma_pago` FOREIGN KEY (`id_forma_pago`) REFERENCES `formas_pago` (`id_forma_pago`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `matriculas_detalle`
-- Detalle de los cursos incluidos en una matrícula.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matriculas_detalle` (
  `id_matricula_detalle` INT NOT NULL AUTO_INCREMENT,
  `id_matricula` INT NOT NULL,
  `id_curso_programado` INT NOT NULL,
  `precio_pactado` DECIMAL(10,2) NOT NULL,
  `descuento` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `precio_final` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_matricula_detalle`),
  INDEX `fk_detalle_matricula_idx` (`id_matricula` ASC),
  INDEX `fk_detalle_curso_prog_idx` (`id_curso_programado` ASC),
  CONSTRAINT `fk_detalle_matricula`
    FOREIGN KEY (`id_matricula`)
    REFERENCES `matriculas` (`id_matricula`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_curso_prog`
    FOREIGN KEY (`id_curso_programado`)
    REFERENCES `cursos_programados` (`id_curso_programado`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `asistencia_profesor`
-- Registro de asistencia para cada clase programada del profesor.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `asistencia_profesor` (
  `id_asistencia_profesor` INT NOT NULL AUTO_INCREMENT,
  `id_curso_programado` INT NOT NULL,
  `id_profesor` INT NOT NULL,
  `fecha_clase` DATE NOT NULL,
  `estado` ENUM('Programado', 'Asistió', 'Faltó', 'Reprogramado') NOT NULL DEFAULT 'Programado',
  `observaciones` VARCHAR(255) NULL,
  PRIMARY KEY (`id_asistencia_profesor`),
  INDEX `fk_asist_prof_curso_prog_idx` (`id_curso_programado` ASC),
  INDEX `fk_asist_prof_profesor_idx` (`id_profesor` ASC),
  CONSTRAINT `fk_asist_prof_curso_prog` FOREIGN KEY (`id_curso_programado`) REFERENCES `cursos_programados` (`id_curso_programado`),
  CONSTRAINT `fk_asist_prof_profesor` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabla `asistencia_cliente`
-- Registro de asistencia para cada clase del cliente matriculado.
-- -----------------------------------------------------
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

-- Inserción de datos iniciales
INSERT IGNORE INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES (1, 'Administrador', 'Acceso total al sistema'), (2, 'Usuario', 'Acceso limitado a operaciones');
INSERT IGNORE INTO `usuarios` (`id_rol`, `nombre_usuario`, `password_hash`, `email`, `nombre_completo`) VALUES (1, 'admin', '$2y$10$t/iagDSBOSf1d2YhA.Lzce4jK2jaxtfr5QyCoLGgUoM5a9WprgI.q', 'admin@example.com', 'Administrador del Sistema');
INSERT IGNORE INTO `tipos_documento` (`descripcion`, `longitud`, `codigo_sunat`) VALUES ('DNI', 8, '1'), ('Pasaporte', 12, '7'), ('Carnet de Extranjería', 12, '4');
INSERT IGNORE INTO `formas_pago` (`nombre`) VALUES ('Efectivo'), ('Tarjeta de Crédito/Débito'), ('Transferencia Bancaria'), ('Yape/Plin');
INSERT IGNORE INTO `tipos_area` (`nombre`) VALUES ('Grande'), ('Mediana'), ('Pequeña'), ('Otro');
INSERT IGNORE INTO `tipos_curso` (`nombre`) VALUES ('Taller'), ('Curso Regular'), ('Seminario');
INSERT IGNORE INTO `tipos_precio` (`nombre`) VALUES ('Precio Regular'), ('Precio de Lanzamiento'), ('Precio Corporativo');
