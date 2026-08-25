USE academia_cursos;

DROP PROCEDURE IF EXISTS sp_cobros_listar;

DELIMITER $$

CREATE PROCEDURE sp_cobros_listar (
  IN p_id_cliente INT,
  IN p_fecha_cobro DATE,
  IN p_id_matricula INT,
  IN p_numero_operacion VARCHAR(20)
)
BEGIN
  SELECT
    c.id_cobro,
    c.id_matricula,
    m.id_cliente,
    GROUP_CONCAT(
      CONCAT(
        COALESCE(CONCAT(ca.nombres, ' ', ca.apellidos), CONCAT(cli.nombres, ' ', cli.apellidos)),
        ' | ', cu.nombre,
        ' | ', CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area),
        ' | ', CONCAT(p.nombres, ' ', p.apellidos),
        ' | ', th.descripcion, ' ', TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')
      )
      ORDER BY md.id_matricula_detalle
      SEPARATOR '\n'
    ) AS alumnos_cursos,
    GROUP_CONCAT(DISTINCT COALESCE(md.id_cliente_asistencia, m.id_cliente) ORDER BY md.id_matricula_detalle) AS clientes_asistencia_ids,
    GROUP_CONCAT(DISTINCT cu.id_curso ORDER BY md.id_matricula_detalle) AS cursos_ids,
    GROUP_CONCAT(DISTINCT sa.id_sub_area ORDER BY md.id_matricula_detalle) AS ubicaciones_ids,
    GROUP_CONCAT(DISTINCT p.id_profesor ORDER BY md.id_matricula_detalle) AS profesores_ids,
    GROUP_CONCAT(DISTINCT cp.id_curso_programado ORDER BY md.id_matricula_detalle) AS horarios_ids,
    c.id_forma_pago,
    fp.nombre AS forma_pago,
    c.fecha_cobro,
    c.numero_operacion,
    c.importe,
    c.observaciones,
    c.fecha_creacion,
    u.nombre_usuario AS usuario_creacion
  FROM cobros c
  INNER JOIN matriculas m ON m.id_matricula = c.id_matricula
  INNER JOIN clientes cli ON cli.id_cliente = m.id_cliente
  INNER JOIN formas_pago fp ON fp.id_forma_pago = c.id_forma_pago
  INNER JOIN usuarios u ON u.id_usuario = c.id_usuario_creacion
  LEFT JOIN matriculas_detalle md ON md.id_matricula = m.id_matricula
  LEFT JOIN clientes ca ON ca.id_cliente = md.id_cliente_asistencia
  LEFT JOIN cursos_programados cp ON cp.id_curso_programado = md.id_curso_programado
  LEFT JOIN cursos cu ON cu.id_curso = cp.id_curso
  LEFT JOIN sub_areas sa ON sa.id_sub_area = cp.id_sub_area
  LEFT JOIN areas a ON a.id_area = sa.id_area
  LEFT JOIN profesores p ON p.id_profesor = cp.id_profesor
  LEFT JOIN tipos_horario th ON th.id_tipo_horario = cp.id_tipo_horario
  WHERE (p_id_cliente IS NULL OR p_id_cliente = 0 OR m.id_cliente = p_id_cliente)
    AND (p_fecha_cobro IS NULL OR p_fecha_cobro = '' OR c.fecha_cobro = p_fecha_cobro)
    AND (p_id_matricula IS NULL OR p_id_matricula = 0 OR c.id_matricula = p_id_matricula)
    AND (p_numero_operacion IS NULL OR p_numero_operacion = '' OR c.numero_operacion LIKE CONCAT('%', p_numero_operacion, '%'))
  GROUP BY c.id_cobro, c.id_matricula, m.id_cliente, c.id_forma_pago, fp.nombre, c.fecha_cobro, c.numero_operacion, c.importe, c.observaciones, c.fecha_creacion, u.nombre_usuario
  ORDER BY c.fecha_cobro DESC, c.id_cobro DESC;
END $$

DELIMITER ;
