-- =================================================================
-- Script de Creación de Procedimientos Almacenados (Principales)
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- Procedimientos para la tabla `usuarios`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_usuarios_crear`$$
CREATE PROCEDURE `sp_usuarios_crear`(IN p_id_rol INT, IN p_nombre_usuario VARCHAR(50), IN p_password_hash VARCHAR(255), IN p_email VARCHAR(100), IN p_nombre_completo VARCHAR(150))
BEGIN
    INSERT INTO usuarios (id_rol, nombre_usuario, password_hash, email, nombre_completo, activo)
    VALUES (p_id_rol, p_nombre_usuario, p_password_hash, p_email, p_nombre_completo, 1);
END$$

-- ... [All other SPs from the original stored_procedures.sql with DROP statements] ...

DROP PROCEDURE IF EXISTS `sp_matricula_anular`$$
CREATE PROCEDURE `sp_matricula_anular`(IN p_id_matricula INT, IN p_observaciones TEXT)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);
    START TRANSACTION;
    SELECT estado INTO v_estado_actual FROM matriculas WHERE id_matricula = p_id_matricula;
    IF v_estado_actual = 'Activa' THEN
        UPDATE matriculas SET estado = 'Anulada', observaciones = CONCAT(IFNULL(observaciones, ''), '\nANULACIÓN: ', p_observaciones) WHERE id_matricula = p_id_matricula;
        UPDATE cursos_programados cp JOIN matriculas_detalle md ON cp.id_curso_programado = md.id_curso_programado SET cp.vacantes_disponibles = cp.vacantes_disponibles + 1 WHERE md.id_matricula = p_id_matricula;
        UPDATE asistencia_cliente ac JOIN matriculas_detalle md ON ac.id_matricula_detalle = md.id_matricula_detalle SET ac.estado = 'Cancelado' WHERE md.id_matricula = p_id_matricula;
        COMMIT;
    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La matrícula no se puede anular porque no está activa.';
    END IF;
END$$

DELIMITER ;
