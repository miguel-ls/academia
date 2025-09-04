-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.27
-- Corrige el estado de la asistencia para anulaciones y reversiones.
-- =================================================================

USE `academia_cursos`;

-- 1. Añadir el estado 'Cancelado' al ENUM de la tabla de asistencia de clientes.
-- Esto es necesario porque el SP de anulación lo usa, pero no estaba en la definición de la tabla.
ALTER TABLE `asistencia_cliente`
MODIFY COLUMN `estado` ENUM('Programado', 'Asistió', 'Faltó', 'Justificado', 'Postergado', 'Cancelado') NOT NULL DEFAULT 'Programado';


DELIMITER $$

-- 2. Redefinir el SP de anulación para asegurar consistencia.
-- El SP original ya era funcional, pero se redefine aquí para consolidar el arreglo.
DROP PROCEDURE IF EXISTS `sp_matricula_anular`$$
CREATE PROCEDURE `sp_matricula_anular`(IN p_id_matricula INT, IN p_observaciones TEXT)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);
    START TRANSACTION;
    SELECT estado INTO v_estado_actual FROM matriculas WHERE id_matricula = p_id_matricula;
    IF v_estado_actual = 'Activa' THEN
        UPDATE matriculas SET estado = 'Anulada', observaciones = CONCAT(IFNULL(observaciones, ''), '\nANULACIÓN: ', p_observaciones) WHERE id_matricula = p_id_matricula;

        -- Liberar vacantes
        UPDATE cursos_programados cp
        JOIN matriculas_detalle md ON cp.id_curso_programado = md.id_curso_programado
        SET cp.vacantes_disponibles = cp.vacantes_disponibles + 1
        WHERE md.id_matricula = p_id_matricula;

        -- Marcar asistencia como cancelada
        UPDATE asistencia_cliente ac
        JOIN matriculas_detalle md ON ac.id_matricula_detalle = md.id_matricula_detalle
        SET ac.estado = 'Cancelado'
        WHERE md.id_matricula = p_id_matricula;

        COMMIT;
    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La matrícula no se puede anular porque no está activa.';
    END IF;
END$$


-- 3. Redefinir el SP de reversión para asegurar que funciona con el nuevo ENUM.
-- El SP original ya era funcional, pero se redefine para consolidar.
DROP PROCEDURE IF EXISTS `sp_matricula_revertir_anulacion`$$
CREATE PROCEDURE `sp_matricula_revertir_anulacion`(
    IN p_id_matricula INT
)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);
    DECLARE v_curso_sin_vacantes INT DEFAULT 0;
    DECLARE v_nombre_curso_sin_vacantes VARCHAR(150);
    DECLARE v_error_message VARCHAR(255);
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_id_curso_programado INT;

    DECLARE cur_cursos CURSOR FOR
        SELECT id_curso_programado FROM matriculas_detalle WHERE id_matricula = p_id_matricula;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    START TRANSACTION;

    SELECT estado INTO v_estado_actual FROM matriculas WHERE id_matricula = p_id_matricula;
    IF v_estado_actual != 'Anulada' THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La matrícula no se puede revertir porque no está anulada.';
    END IF;

    OPEN cur_cursos;
    read_loop: LOOP
        FETCH cur_cursos INTO cur_id_curso_programado;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SELECT IF(vacantes_disponibles > 0, 0, 1), c.nombre
        INTO v_curso_sin_vacantes, v_nombre_curso_sin_vacantes
        FROM cursos_programados cp
        JOIN cursos c ON cp.id_curso = c.id_curso
        WHERE cp.id_curso_programado = cur_id_curso_programado;

        IF v_curso_sin_vacantes = 1 THEN
            SET done = TRUE;
        END IF;
    END LOOP;
    CLOSE cur_cursos;

    IF v_curso_sin_vacantes = 1 THEN
        SET v_error_message = CONCAT('No se puede revertir. El curso "', v_nombre_curso_sin_vacantes, '" ya no tiene vacantes disponibles.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_message;
    END IF;

    UPDATE matriculas SET estado = 'Activa' WHERE id_matricula = p_id_matricula;

    SET done = FALSE;
    OPEN cur_cursos;
    update_loop: LOOP
        FETCH cur_cursos INTO cur_id_curso_programado;
        IF done THEN
            LEAVE update_loop;
        END IF;

        UPDATE cursos_programados SET vacantes_disponibles = vacantes_disponibles - 1
        WHERE id_curso_programado = cur_id_curso_programado;

        UPDATE asistencia_cliente ac
        JOIN matriculas_detalle md ON ac.id_matricula_detalle = md.id_matricula_detalle
        SET ac.estado = 'Programado'
        WHERE md.id_matricula = p_id_matricula
        AND md.id_curso_programado = cur_id_curso_programado
        AND ac.fecha_clase >= CURDATE()
        AND ac.estado = 'Cancelado'; -- Solo revertir las que estaban canceladas
    END LOOP;
    CLOSE cur_cursos;

    COMMIT;
END$$

DELIMITER ;
