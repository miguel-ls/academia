-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.18
-- Añade SP para obtener los horarios activos de un cliente.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_cliente_horarios_activos`
-- Devuelve los horarios de los cursos en los que un cliente
-- está actualmente matriculado y cuya matrícula está 'Activa'.
-- Esto es útil para validar cruces de horarios al registrar nuevas matrículas.
-- -----------------------------------------------------

DROP PROCEDURE IF EXISTS `sp_cliente_horarios_activos`$$
CREATE PROCEDURE `sp_cliente_horarios_activos`(
    IN p_id_cliente INT
)
BEGIN
    SELECT
        cp.id_sub_area,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin,
        th.dias_semana
    FROM matriculas m
    JOIN matriculas_detalle md ON m.id_matricula = md.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE
        md.id_cliente_asistencia = p_id_cliente
        AND m.estado = 'Activa';
END$$

DELIMITER ;
