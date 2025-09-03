-- =================================================================
-- Script de Creación de Procedimientos Almacenados para Profesores
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `profesores`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_profesores_listar`$$
CREATE PROCEDURE `sp_profesores_listar`()
BEGIN
    SELECT
        p.id_profesor,
        p.nombres,
        p.apellidos,
        p.email,
        p.telefono,
        p.especialidad,
        td.descripcion AS tipo_documento,
        p.numero_documento
    FROM profesores p
    JOIN tipos_documento td ON p.id_tipo_documento = td.id_tipo_documento
    ORDER BY p.apellidos, p.nombres;
END$$

DROP PROCEDURE IF EXISTS `sp_profesores_obtener_por_id`$$
CREATE PROCEDURE `sp_profesores_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT
        id_profesor,
        id_tipo_documento,
        numero_documento,
        nombres,
        apellidos,
        email,
        telefono,
        especialidad
    FROM profesores
    WHERE id_profesor = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_profesores_crear`$$
CREATE PROCEDURE `sp_profesores_crear`(
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_especialidad VARCHAR(255)
)
BEGIN
    INSERT INTO profesores (id_tipo_documento, numero_documento, nombres, apellidos, email, telefono, especialidad)
    VALUES (p_id_tipo_documento, p_numero_documento, p_nombres, p_apellidos, p_email, p_telefono, p_especialidad);
END$$

DROP PROCEDURE IF EXISTS `sp_profesores_actualizar`$$
CREATE PROCEDURE `sp_profesores_actualizar`(
    IN p_id_profesor INT,
    IN p_id_tipo_documento INT,
    IN p_numero_documento VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_especialidad VARCHAR(255)
)
BEGIN
    UPDATE profesores
    SET
        id_tipo_documento = p_id_tipo_documento,
        numero_documento = p_numero_documento,
        nombres = p_nombres,
        apellidos = p_apellidos,
        email = p_email,
        telefono = p_telefono,
        especialidad = p_especialidad
    WHERE id_profesor = p_id_profesor;
END$$

DROP PROCEDURE IF EXISTS `sp_profesores_eliminar`$$
CREATE PROCEDURE `sp_profesores_eliminar`(IN p_id INT)
BEGIN
    -- Se podría añadir lógica para verificar dependencias antes de eliminar
    DELETE FROM profesores WHERE id_profesor = p_id;
END$$

DROP PROCEDURE IF EXISTS `sp_profesores_buscar`$$
CREATE PROCEDURE `sp_profesores_buscar`(IN p_termino VARCHAR(100))
BEGIN
    SELECT
        id_profesor,
        CONCAT(nombres, ' ', apellidos) as nombre_completo
    FROM profesores
    WHERE CONCAT(nombres, ' ', apellidos) LIKE CONCAT('%', p_termino, '%')
    ORDER BY apellidos, nombres
    LIMIT 10;
END$$

DELIMITER ;
