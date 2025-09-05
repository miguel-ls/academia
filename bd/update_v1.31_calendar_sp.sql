-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.31
-- Añade SP para obtener los datos del calendario.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- `sp_calendario_cursos_activos`
-- Devuelve todos los cursos activos de todos los clientes
-- para ser mostrados en el calendario general.
-- -----------------------------------------------------

DROP PROCEDURE IF EXISTS `sp_calendario_cursos_activos`$$
CREATE PROCEDURE `sp_calendario_cursos_activos`()
BEGIN
    SELECT
        m.id_matricula,
        md.id_matricula_detalle,
        cp.id_curso_programado,
        c.id_curso,
        c.nombre AS nombre_curso,
        a.id_area,
        a.nombre AS nombre_area,
        sa.id_sub_area,
        sa.descripcion AS nombre_sub_area,
        sa.numero_sub_area,
        CONCAT(p.nombres, ' ', p.apellidos) AS nombre_profesor,
        cli.id_cliente,
        CONCAT(cli.nombres, ' ', cli.apellidos) AS nombre_cliente,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin,
        th.descripcion AS horario_dias,
        th.dias_semana -- Formato '1,3,5' para L,M,V
    FROM matriculas_detalle md
    JOIN matriculas m ON md.id_matricula = m.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN clientes cli ON md.id_cliente_asistencia = cli.id_cliente
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE
        m.estado = 'Activa';
END$$

DELIMITER ;
