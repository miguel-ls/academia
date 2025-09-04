-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.25
-- Añade SP para revertir la anulación de una matrícula.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP: `sp_matricula_revertir_anulacion`
-- Reverte una matrícula anulada a activa, validando vacantes.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_revertir_anulacion`$$
CREATE PROCEDURE `sp_matricula_revertir_anulacion`(
    IN p_id_matricula INT
)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);
    DECLARE v_curso_sin_vacantes INT DEFAULT 0;
    DECLARE v_nombre_curso_sin_vacantes VARCHAR(150);
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_id_curso_programado INT;

    -- Cursor para iterar sobre los cursos de la matrícula
    DECLARE cur_cursos CURSOR FOR
        SELECT id_curso_programado FROM matriculas_detalle WHERE id_matricula = p_id_matricula;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Iniciar transacción
    START TRANSACTION;

    -- 1. Verificar que la matrícula esté 'Anulada'
    SELECT estado INTO v_estado_actual FROM matriculas WHERE id_matricula = p_id_matricula;
    IF v_estado_actual != 'Anulada' THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La matrícula no se puede revertir porque no está anulada.';
    END IF;

    -- 2. Validar que haya vacantes para TODOS los cursos
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
            SET done = TRUE; -- Salir del bucle si se encuentra un curso sin vacantes
        END IF;
    END LOOP;
    CLOSE cur_cursos;

    -- Si se encontró un curso sin vacantes, fallar
    IF v_curso_sin_vacantes = 1 THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = CONCAT('No se puede revertir. El curso "', v_nombre_curso_sin_vacantes, '" ya no tiene vacantes disponibles.');
    END IF;

    -- 3. Si todo está bien, proceder con la actualización
    -- Reactivar la cabecera
    UPDATE matriculas SET estado = 'Activa' WHERE id_matricula = p_id_matricula;

    -- Restar vacantes y reactivar asistencia
    SET done = FALSE;
    OPEN cur_cursos;
    update_loop: LOOP
        FETCH cur_cursos INTO cur_id_curso_programado;
        IF done THEN
            LEAVE update_loop;
        END IF;

        -- Decrementar vacante
        UPDATE cursos_programados SET vacantes_disponibles = vacantes_disponibles - 1
        WHERE id_curso_programado = cur_id_curso_programado;

        -- Reactivar asistencia futura
        UPDATE asistencia_cliente ac
        JOIN matriculas_detalle md ON ac.id_matricula_detalle = md.id_matricula_detalle
        SET ac.estado = 'Programado'
        WHERE md.id_matricula = p_id_matricula
        AND md.id_curso_programado = cur_id_curso_programado
        AND ac.fecha_clase >= CURDATE();
    END LOOP;
    CLOSE cur_cursos;

    COMMIT;
END$$

DELIMITER ;
