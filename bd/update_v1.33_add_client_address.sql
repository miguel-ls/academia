-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.33
-- Añade campos de dirección y ubigeo a los clientes.
-- Modifica `apellidos` para que sea opcional (para RUC).
-- =================================================================

USE `academia_cursos`;

-- 1. Modificar la tabla de clientes
ALTER TABLE `clientes`
ADD COLUMN `direccion` VARCHAR(255) NULL AFTER `codigo_erp`,
ADD COLUMN `codigo_ubigeo` VARCHAR(10) NULL AFTER `direccion`,
MODIFY COLUMN `apellidos` VARCHAR(100) NULL; -- Hacer apellidos opcional

-- 2. Actualizar los procedimientos almacenados

DELIMITER $$

-- -----------------------------------------------------
-- `sp_clientes_listar`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_listar`$$
CREATE PROCEDURE `sp_clientes_listar`()
BEGIN
    SELECT
        c.id_cliente,
        c.nombres,
        c.apellidos,
        td.descripcion AS tipo_documento,
        c.numero_documento,
        c.email,
        c.telefono,
        c.direccion,
        c.codigo_ubigeo
    FROM clientes c
    JOIN tipos_documento td ON c.id_tipo_documento = td.id_tipo_documento
    ORDER BY c.apellidos, c.nombres;
END$$

-- -----------------------------------------------------
-- `sp_clientes_obtener_por_id`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_obtener_por_id`$$
CREATE PROCEDURE `sp_clientes_obtener_por_id`(IN p_id_cliente INT)
BEGIN
    SELECT
        id_cliente,
        id_tipo_documento,
        numero_documento,
        nombres,
        apellidos,
        email,
        telefono,
        codigo_erp,
        direccion,
        codigo_ubigeo
    FROM clientes
    WHERE id_cliente = p_id_cliente;
END$$

-- -----------------------------------------------------
-- `sp_clientes_crear`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_crear`$$
CREATE PROCEDURE `sp_clientes_crear`(
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_codigo_erp VARCHAR(20),
    IN p_direccion VARCHAR(255),
    IN p_codigo_ubigeo VARCHAR(10)
)
BEGIN
    DECLARE cliente_existente INT;

    SELECT COUNT(*) INTO cliente_existente
    FROM clientes
    WHERE id_tipo_documento = p_id_tipo_documento AND numero_documento = p_numero_documento;

    IF cliente_existente > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un cliente con el mismo tipo y número de documento.';
    ELSE
        INSERT INTO clientes (id_tipo_documento, numero_documento, nombres, apellidos, email, telefono, codigo_erp, direccion, codigo_ubigeo)
        VALUES (p_id_tipo_documento, p_numero_documento, p_nombres, p_apellidos, p_email, p_telefono, p_codigo_erp, p_direccion, p_codigo_ubigeo);
        SELECT LAST_INSERT_ID() as id_cliente;
    END IF;
END$$

-- -----------------------------------------------------
-- `sp_clientes_actualizar`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_actualizar`$$
CREATE PROCEDURE `sp_clientes_actualizar`(
    IN p_id_cliente INT,
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_codigo_erp VARCHAR(20),
    IN p_direccion VARCHAR(255),
    IN p_codigo_ubigeo VARCHAR(10)
)
BEGIN
    DECLARE cliente_existente INT;
    SELECT COUNT(*) INTO cliente_existente
    FROM clientes
    WHERE id_tipo_documento = p_id_tipo_documento
      AND numero_documento = p_numero_documento
      AND id_cliente != p_id_cliente;

    IF cliente_existente > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El nuevo número de documento ya está en uso por otro cliente.';
    ELSE
        UPDATE clientes SET
            id_tipo_documento = p_id_tipo_documento,
            numero_documento = p_numero_documento,
            nombres = p_nombres,
            apellidos = p_apellidos,
            email = p_email,
            telefono = p_telefono,
            codigo_erp = p_codigo_erp,
            direccion = p_direccion,
            codigo_ubigeo = p_codigo_ubigeo
        WHERE id_cliente = p_id_cliente;
    END IF;
END$$

DELIMITER ;
