-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.30
-- Añade un SP de solo lectura para validar la capacidad de una Sub Área.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP: `sp_sub_areas_validar_capacidad`
-- Valida si una nueva capacidad para una sub-área es válida.
-- Devuelve una fila con un mensaje de error si no es válida.
-- Devuelve un conjunto de resultados vacío si la validación es exitosa.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_sub_areas_validar_capacidad`$$
CREATE PROCEDURE `sp_sub_areas_validar_capacidad`(
    IN p_id_sub_area INT,
    IN p_capacidad_maxima INT
)
BEGIN
    DECLARE v_inscritos INT;
    DECLARE v_id_curso_programado INT;
    DECLARE v_nombre_curso VARCHAR(150);
    DECLARE v_error_message VARCHAR(255) DEFAULT NULL;
    DECLARE done INT DEFAULT FALSE;

    -- Cursor para iterar sobre los cursos afectados
    DECLARE cur_cursos_afectados CURSOR FOR
        SELECT cp.id_curso_programado, c.nombre
        FROM cursos_programados cp
        JOIN cursos c ON cp.id_curso = c.id_curso
        WHERE cp.id_sub_area = p_id_sub_area
        AND cp.estado IN ('Programado', 'En Curso');

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Bucle de validación
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

        -- Si la nueva capacidad es insuficiente, preparar mensaje de error y salir
        IF p_capacidad_maxima < v_inscritos THEN
            SET v_error_message = CONCAT('No se puede reducir la capacidad a ', p_capacidad_maxima, '. El curso "', v_nombre_curso, '" ya tiene ', v_inscritos, ' alumnos inscritos.');
            LEAVE validation_loop;
        END IF;
    END LOOP;
    CLOSE cur_cursos_afectados;

    -- Devolver el mensaje de error si existe
    IF v_error_message IS NOT NULL THEN
        SELECT v_error_message AS error;
    END IF;

END$$

DELIMITER ;
