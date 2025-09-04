-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.23
-- Añade SPs para el proceso de actualización de matrículas.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP 1: `sp_matricula_detalle_actualizar`
-- Actualiza un detalle de matrícula existente.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_detalle_actualizar`$$
CREATE PROCEDURE `sp_matricula_detalle_actualizar`(
    IN p_id_matricula_detalle INT,
    IN p_id_cliente_asistencia INT,
    IN p_precio_pactado DECIMAL(10,2),
    IN p_descuento DECIMAL(10,2)
)
BEGIN
    DECLARE v_precio_final DECIMAL(10,2);
    DECLARE v_cliente_actual INT;

    -- Obtener el cliente de asistencia actual para comparar
    SELECT id_cliente_asistencia INTO v_cliente_actual
    FROM matriculas_detalle
    WHERE id_matricula_detalle = p_id_matricula_detalle;

    -- Calcular el nuevo precio final
    SET v_precio_final = p_precio_pactado - p_descuento;

    -- Actualizar el registro de detalle
    UPDATE matriculas_detalle
    SET
        id_cliente_asistencia = p_id_cliente_asistencia,
        precio_pactado = p_precio_pactado,
        descuento = p_descuento,
        precio_final = v_precio_final
    WHERE
        id_matricula_detalle = p_id_matricula_detalle;

    -- Si el cliente de asistencia ha cambiado, se debe regenerar el cronograma
    IF v_cliente_actual != p_id_cliente_asistencia THEN
        CALL sp_asistencia_cliente_generar_cronograma(p_id_matricula_detalle);
    END IF;
END$$


-- -----------------------------------------------------
-- SP 2: `sp_matricula_cabecera_actualizar`
-- Actualiza la cabecera de una matrícula (forma de pago y observaciones).
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_cabecera_actualizar`$$
CREATE PROCEDURE `sp_matricula_cabecera_actualizar`(
    IN p_id_matricula INT,
    IN p_id_forma_pago INT,
    IN p_observaciones TEXT
)
BEGIN
    UPDATE matriculas
    SET
        id_forma_pago = p_id_forma_pago,
        observaciones = p_observaciones
    WHERE
        id_matricula = p_id_matricula;
END$$


DELIMITER ;
