-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.28
-- Corrige los SPs de eliminación para que devuelvan las vacantes.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP 1: `sp_matricula_detalle_eliminar`
-- Corrige el SP para que incremente la vacante del curso asociado.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_detalle_eliminar`$$
CREATE PROCEDURE `sp_matricula_detalle_eliminar`(
    IN p_id_matricula_detalle INT
)
BEGIN
    DECLARE v_id_curso_programado INT;

    -- Obtener el curso programado antes de eliminar
    SELECT id_curso_programado INTO v_id_curso_programado
    FROM matriculas_detalle
    WHERE id_matricula_detalle = p_id_matricula_detalle;

    -- Si encontramos el detalle, proceder
    IF v_id_curso_programado IS NOT NULL THEN
        -- 1. Devolver la vacante al curso
        UPDATE cursos_programados
        SET vacantes_disponibles = vacantes_disponibles + 1
        WHERE id_curso_programado = v_id_curso_programado;

        -- 2. Eliminar la asistencia generada
        DELETE FROM asistencia_cliente WHERE id_matricula_detalle = p_id_matricula_detalle;

        -- 3. Eliminar el detalle de la matrícula
        DELETE FROM matriculas_detalle WHERE id_matricula_detalle = p_id_matricula_detalle;
    END IF;
END$$


-- -----------------------------------------------------
-- SP 2: `sp_matricula_eliminar`
-- Corrige el SP para que devuelva las vacantes de todos los cursos asociados.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_eliminar`$$
CREATE PROCEDURE `sp_matricula_eliminar`(
    IN p_id_matricula INT
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_id_curso_programado INT;
    DECLARE cur_id_matricula_detalle INT;

    -- Cursor para iterar sobre los detalles de la matrícula a eliminar
    DECLARE cur_detalles CURSOR FOR
        SELECT id_matricula_detalle, id_curso_programado FROM matriculas_detalle WHERE id_matricula = p_id_matricula;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    START TRANSACTION;

    -- 1. Devolver las vacantes de cada curso en la matrícula
    OPEN cur_detalles;
    devolver_loop: LOOP
        FETCH cur_detalles INTO cur_id_matricula_detalle, cur_id_curso_programado;
        IF done THEN
            LEAVE devolver_loop;
        END IF;
        -- Incrementar la vacante
        UPDATE cursos_programados
        SET vacantes_disponibles = vacantes_disponibles + 1
        WHERE id_curso_programado = cur_id_curso_programado;
    END LOOP;
    CLOSE cur_detalles;

    -- 2. Eliminar los registros de asistencia asociados
    DELETE FROM asistencia_cliente
    WHERE id_matricula_detalle IN (
        SELECT id_matricula_detalle
        FROM matriculas_detalle
        WHERE id_matricula = p_id_matricula
    );

    -- 3. Eliminar los registros de detalle
    DELETE FROM matriculas_detalle WHERE id_matricula = p_id_matricula;

    -- 4. Eliminar la cabecera
    DELETE FROM matriculas WHERE id_matricula = p_id_matricula;

    COMMIT;
END$$


DELIMITER ;
