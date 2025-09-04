DELIMITER $$

-- Obtiene la cabecera de una matrícula por su ID
CREATE PROCEDURE `sp_matricula_obtener_cabecera_por_id`(
    IN p_id_matricula INT
)
BEGIN
    SELECT
        mc.id_matricula,
        mc.id_cliente,
        CONCAT(c.nombres, ' ', c.apellidos) AS nombre_cliente,
        mc.fecha_matricula,
        mc.monto_total,
        mc.descuento_total,
        mc.monto_final,
        mc.observaciones,
        fp.nombre AS forma_pago,
        CASE mc.estado WHEN 1 THEN 'Activa' WHEN 0 THEN 'Anulada' END AS estado
    FROM
        matriculas mc
    JOIN
        clientes c ON mc.id_cliente = c.id_cliente
    JOIN
        formas_pago fp ON mc.id_forma_pago = fp.id_forma_pago
    WHERE
        mc.id_matricula = p_id_matricula;
END$$

-- Obtiene los detalles (cursos) de una matrícula por el ID de la cabecera
CREATE PROCEDURE `sp_matricula_obtener_detalles_por_id_matricula`(
    IN p_id_matricula INT
)
BEGIN
    SELECT
        md.id_matricula_detalle,
        md.id_curso_programado,
        cur.nombre AS nombre_curso,
        md.id_cliente_asistencia,
        CONCAT(cli_asist.nombres, ' ', cli_asist.apellidos) AS nombre_cliente_asistencia,
        md.precio_pactado,
        md.descuento,
        md.precio_final,
        -- Campos adicionales para la vista de edición
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
        CONCAT(p.nombres, ' ', p.apellidos) AS profesor,
        th.descripcion AS horario_dias,
        cp.hora_inicio,
        cp.hora_fin,
        th.dias_semana,
        cp.fecha_inicio,
        cp.fecha_fin
    FROM
        matriculas_detalle md
    JOIN
        cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN
        cursos cur ON cp.id_curso = cur.id_curso
    JOIN
        clientes cli_asist ON md.id_cliente_asistencia = cli_asist.id_cliente
    JOIN
        profesores p ON cp.id_profesor = p.id_profesor
    JOIN
        sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN
        areas a ON sa.id_area = a.id_area
    JOIN
        tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE
        md.id_matricula = p_id_matricula
    ORDER BY
        md.id_matricula_detalle ASC;
END$$

DELIMITER ;
