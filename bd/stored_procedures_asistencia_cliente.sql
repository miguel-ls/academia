-- =================================================================
-- Script de Creación de Procedimientos Almacenados para Asistencia de Clientes
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- Nota: El procedimiento para generar el cronograma (`sp_asistencia_cliente_generar_cronograma`)
-- ya fue creado en el archivo `update_v1.13.sql`.

-- `sp_asistencia_cliente_listar_cursos`
-- Lista las matrículas activas para la vista de asistencia de clientes.
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_listar_cursos`$$
CREATE PROCEDURE `sp_asistencia_cliente_listar_cursos`(
    IN p_id_cliente INT,
    IN p_id_curso INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT
        mc.id_matricula,
        md.id_matricula_detalle,
        cp.id_curso_programado,
        c.nombre AS curso_nombre,
        CONCAT(cli.nombres, ' ', cli.apellidos) AS cliente_nombre,
        cp.fecha_inicio,
        cp.fecha_fin,
        th.descripcion AS dias,
        CONCAT(TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')) AS horas,
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
        mc.estado
    FROM matriculas_cabecera mc
    JOIN matriculas_detalle md ON mc.id_matricula = md.id_matricula
    JOIN clientes cli ON md.id_cliente_asistencia = cli.id_cliente
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    WHERE
        mc.estado = 1 -- Solo matrículas activas
        AND (p_id_cliente IS NULL OR md.id_cliente_asistencia = p_id_cliente)
        AND (p_id_curso IS NULL OR cp.id_curso = p_id_curso)
        AND (p_fecha_inicio IS NULL OR cp.fecha_inicio >= p_fecha_inicio)
        AND (p_fecha_fin IS NULL OR cp.fecha_fin <= p_fecha_fin)
    ORDER BY cp.fecha_inicio DESC, cliente_nombre ASC;
END$$


-- `sp_asistencia_cliente_obtener_detalle_curso`
-- Obtiene los detalles de la cabecera para la página de marcar asistencia.
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_obtener_detalle_curso`$$
CREATE PROCEDURE `sp_asistencia_cliente_obtener_detalle_curso`(IN p_id_matricula_detalle INT)
BEGIN
    SELECT
        c.nombre AS curso_nombre,
        CONCAT(cli.nombres, ' ', cli.apellidos) AS cliente_nombre,
        CONCAT(p.nombres, ' ', p.apellidos) AS profesor_nombre,
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
        th.descripcion AS tipo_horario_nombre,
        cp.fecha_inicio,
        cp.fecha_fin,
        cp.hora_inicio,
        cp.hora_fin
    FROM matriculas_detalle md
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN clientes cli ON md.id_cliente_asistencia = cli.id_cliente
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE md.id_matricula_detalle = p_id_matricula_detalle;
END$$


-- `sp_asistencia_cliente_obtener_clases`
-- Obtiene la lista de clases individuales para un detalle de matrícula (paginado).
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_obtener_clases`$$
CREATE PROCEDURE `sp_asistencia_cliente_obtener_clases`(
    IN p_id_matricula_detalle INT,
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT
        id_asistencia_cliente,
        fecha_clase,
        estado,
        observaciones
    FROM asistencia_cliente
    WHERE id_matricula_detalle = p_id_matricula_detalle
    ORDER BY fecha_clase ASC
    LIMIT p_limit OFFSET p_offset;
END$$


-- `sp_asistencia_cliente_contar_clases`
-- Cuenta el total de clases para un detalle de matrícula.
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_contar_clases`$$
CREATE PROCEDURE `sp_asistencia_cliente_contar_clases`(IN p_id_matricula_detalle INT)
BEGIN
    SELECT COUNT(id_asistencia_cliente) as total
    FROM asistencia_cliente
    WHERE id_matricula_detalle = p_id_matricula_detalle;
END$$


-- `sp_asistencia_cliente_actualizar_asistencia`
-- Actualiza el estado y las observaciones para una clase de un cliente.
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_actualizar_asistencia`$$
CREATE PROCEDURE `sp_asistencia_cliente_actualizar_asistencia`(
    IN p_id_asistencia_cliente INT,
    IN p_estado ENUM('Programado', 'Asistió', 'Faltó', 'Justificado'),
    IN p_observaciones VARCHAR(255)
)
BEGIN
    UPDATE asistencia_cliente
    SET
        estado = p_estado,
        observaciones = p_observaciones
    WHERE id_asistencia_cliente = p_id_asistencia_cliente;
END$$


DELIMITER ;
