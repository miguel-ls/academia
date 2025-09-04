-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.29
-- Mejora el SP de actualización de Sub Áreas para validar y recalcular vacantes.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP: `sp_sub_areas_actualizar`
-- Reescribe el SP para añadir lógica de negocio compleja:
-- 1. Valida que la nueva capacidad no sea menor a los alumnos ya inscritos.
-- 2. Actualiza la capacidad de la sub-área.
-- 3. Recalcula y actualiza las vacantes disponibles en todos los cursos afectados.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_sub_areas_actualizar`$$
CREATE PROCEDURE `sp_sub_areas_actualizar`(
    IN p_id_sub_area INT,
    IN p_id_area INT,
    IN p_descripcion VARCHAR(100),
    IN p_numero_sub_area VARCHAR(20),
    IN p_capacidad_maxima INT
)
BEGIN
    DECLARE v_inscritos INT;
    DECLARE v_id_curso_programado INT;
    DECLARE v_nombre_curso VARCHAR(150);
    DECLARE v_error_message VARCHAR(255);
    DECLARE done INT DEFAULT FALSE;

    -- Cursor para iterar sobre los cursos afectados
    DECLARE cur_cursos_afectados CURSOR FOR
        SELECT cp.id_curso_programado, c.nombre
        FROM cursos_programados cp
        JOIN cursos c ON cp.id_curso = c.id_curso
        WHERE cp.id_sub_area = p_id_sub_area
        AND cp.estado IN ('Programado', 'En Curso'); -- Solo considerar cursos activos o futuros

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    START TRANSACTION;

    -- 1. Validación: La nueva capacidad no puede ser menor a los inscritos
    OPEN cur_cursos_afectados;
    validation_loop: LOOP
        FETCH cur_cursos_afectados INTO v_id_curso_programado, v_nombre_curso;
        IF done THEN
            LEAVE validation_loop;
        END IF;

        -- Contar inscritos para este curso
        SELECT COUNT(*) INTO v_inscritos
        FROM matriculas_detalle md
        JOIN matriculas m ON md.id_matricula = m.id_matricula
        WHERE md.id_curso_programado = v_id_curso_programado AND m.estado = 'Activa';

        -- Si la nueva capacidad es insuficiente, lanzar error
        IF p_capacidad_maxima < v_inscritos THEN
            SET v_error_message = CONCAT('No se puede reducir la capacidad a ', p_capacidad_maxima, '. El curso "', v_nombre_curso, '" ya tiene ', v_inscritos, ' alumnos inscritos.');
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_message;
        END IF;
    END LOOP;
    CLOSE cur_cursos_afectados;
    SET done = FALSE; -- Resetear 'done' para el próximo cursor

    -- 2. Si la validación pasa, actualizar la sub-área
    UPDATE sub_areas
    SET
        id_area = p_id_area,
        descripcion = p_descripcion,
        numero_sub_area = p_numero_sub_area,
        capacidad_maxima = p_capacidad_maxima
    WHERE id_sub_area = p_id_sub_area;

    -- 3. Recalcular y actualizar las vacantes de los cursos afectados
    OPEN cur_cursos_afectados;
    update_loop: LOOP
        FETCH cur_cursos_afectados INTO v_id_curso_programado, v_nombre_curso;
        IF done THEN
            LEAVE update_loop;
        END IF;

        -- Contar inscritos de nuevo (para estar seguros)
        SELECT COUNT(*) INTO v_inscritos
        FROM matriculas_detalle md
        JOIN matriculas m ON md.id_matricula = m.id_matricula
        WHERE md.id_curso_programado = v_id_curso_programado AND m.estado = 'Activa';

        -- Actualizar vacantes disponibles
        UPDATE cursos_programados
        SET vacantes_disponibles = p_capacidad_maxima - v_inscritos
        WHERE id_curso_programado = v_id_curso_programado;
    END LOOP;
    CLOSE cur_cursos_afectados;

    COMMIT;
END$$

DELIMITER ;
