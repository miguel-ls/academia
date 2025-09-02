-- =================================================================
-- Script de Creación de Procedimientos Almacenados para Clientes
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_clientes_listar`
-- Lista todos los clientes.
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
        c.telefono
    FROM clientes c
    JOIN tipos_documento td ON c.id_tipo_documento = td.id_tipo_documento
    ORDER BY c.apellidos, c.nombres;
END$$

-- -----------------------------------------------------
-- `sp_clientes_buscar`
-- Busca clientes por un término de búsqueda en nombres, apellidos o nro de documento.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_buscar`$$
CREATE PROCEDURE `sp_clientes_buscar`(IN p_termino VARCHAR(100))
BEGIN
    SET @termino_busqueda = CONCAT('%', p_termino, '%');
    SELECT
        c.id_cliente,
        c.nombres,
        c.apellidos,
        td.descripcion AS tipo_documento,
        c.numero_documento,
        c.email,
        c.telefono
    FROM clientes c
    JOIN tipos_documento td ON c.id_tipo_documento = td.id_tipo_documento
    WHERE c.nombres LIKE @termino_busqueda
       OR c.apellidos LIKE @termino_busqueda
       OR c.numero_documento LIKE @termino_busqueda
    ORDER BY c.apellidos, c.nombres;
END$$

-- -----------------------------------------------------
-- `sp_clientes_obtener_por_id`
-- Obtiene un cliente específico por su ID.
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
        codigo_erp
    FROM clientes
    WHERE id_cliente = p_id_cliente;
END$$

-- -----------------------------------------------------
-- `sp_clientes_crear`
-- Crea un nuevo cliente, validando que no exista el mismo documento.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_crear`$$
CREATE PROCEDURE `sp_clientes_crear`(
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_codigo_erp VARCHAR(20)
)
BEGIN
    DECLARE cliente_existente INT;

    SELECT COUNT(*) INTO cliente_existente
    FROM clientes
    WHERE id_tipo_documento = p_id_tipo_documento AND numero_documento = p_numero_documento;

    IF cliente_existente > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un cliente con el mismo tipo y número de documento.';
    ELSE
        INSERT INTO clientes (id_tipo_documento, numero_documento, nombres, apellidos, email, telefono, codigo_erp)
        VALUES (p_id_tipo_documento, p_numero_documento, p_nombres, p_apellidos, p_email, p_telefono, p_codigo_erp);
        SELECT LAST_INSERT_ID() as id_cliente;
    END IF;
END$$

-- -----------------------------------------------------
-- `sp_clientes_actualizar`
-- Actualiza los datos de un cliente existente.
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
    IN p_codigo_erp VARCHAR(20)
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
            codigo_erp = p_codigo_erp
        WHERE id_cliente = p_id_cliente;
    END IF;
END$$

-- -----------------------------------------------------
-- `sp_cliente_verificar_matriculas`
-- Verifica si un cliente tiene matrículas asociadas.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cliente_verificar_matriculas`$$
CREATE PROCEDURE `sp_cliente_verificar_matriculas`(IN p_id_cliente INT)
BEGIN
    SELECT COUNT(*) as numero_matriculas FROM matriculas WHERE id_cliente = p_id_cliente;
END$$

-- -----------------------------------------------------
-- `sp_clientes_eliminar`
-- Elimina un cliente por su ID.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_clientes_eliminar`$$
CREATE PROCEDURE `sp_clientes_eliminar`(IN p_id_cliente INT)
BEGIN
    DELETE FROM clientes WHERE id_cliente = p_id_cliente;
END$$


DELIMITER ;
