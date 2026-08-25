USE academia_cursos;

DROP PROCEDURE IF EXISTS sp_cobros_obtener_por_id;
DROP PROCEDURE IF EXISTS sp_cobros_matriculas_pendientes;

DELIMITER $$

CREATE PROCEDURE sp_cobros_obtener_por_id (IN p_id_cobro INT)
BEGIN
  SELECT
    c.id_cobro, c.id_matricula, c.id_forma_pago, c.fecha_cobro, c.numero_operacion,
    c.importe, c.observaciones, c.fecha_creacion, c.id_usuario_creacion, c.fecha_modificacion,
    CONCAT(cli.nombres, ' ', cli.apellidos) AS nombre_cliente,
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
    m.monto_final,
    (m.monto_final - COALESCE((SELECT SUM(c2.importe) FROM cobros c2 WHERE c2.id_matricula = c.id_matricula AND c2.id_cobro <> c.id_cobro), 0)) AS saldo_pendiente,
    fp.nombre AS forma_pago
  FROM cobros c
  INNER JOIN matriculas m ON m.id_matricula = c.id_matricula
  INNER JOIN clientes cli ON cli.id_cliente = m.id_cliente
  INNER JOIN formas_pago fp ON fp.id_forma_pago = c.id_forma_pago
  LEFT JOIN matriculas_detalle md ON md.id_matricula = m.id_matricula
  LEFT JOIN clientes ca ON ca.id_cliente = md.id_cliente_asistencia
  LEFT JOIN cursos_programados cp ON cp.id_curso_programado = md.id_curso_programado
  LEFT JOIN cursos cu ON cu.id_curso = cp.id_curso
  LEFT JOIN sub_areas sa ON sa.id_sub_area = cp.id_sub_area
  LEFT JOIN areas a ON a.id_area = sa.id_area
  LEFT JOIN profesores p ON p.id_profesor = cp.id_profesor
  LEFT JOIN tipos_horario th ON th.id_tipo_horario = cp.id_tipo_horario
  WHERE c.id_cobro = p_id_cobro
  GROUP BY c.id_cobro, c.id_matricula, c.id_forma_pago, c.fecha_cobro, c.numero_operacion, c.importe, c.observaciones, c.fecha_creacion, c.id_usuario_creacion, c.fecha_modificacion, cli.nombres, cli.apellidos, m.monto_final, fp.nombre;
END $$

CREATE PROCEDURE sp_cobros_matriculas_pendientes (IN p_termino VARCHAR(100))
BEGIN
  SELECT
    m.id_matricula,
    COALESCE(alumnos.nombres_clientes, CONCAT(cli.nombres, ' ', cli.apellidos)) AS nombres_clientes,
    COALESCE(alumnos.alumnos_cursos, CONCAT(cli.nombres, ' ', cli.apellidos)) AS alumnos_cursos,
    m.fecha_matricula,
    m.monto_final,
    COALESCE(cobros.total_cobros, 0) AS total_cobros,
    (m.monto_final - COALESCE(cobros.total_cobros, 0)) AS saldo_pendiente
  FROM matriculas m
  INNER JOIN clientes cli ON cli.id_cliente = m.id_cliente
  LEFT JOIN (
    SELECT id_matricula, SUM(importe) AS total_cobros
    FROM cobros
    GROUP BY id_matricula
  ) cobros ON cobros.id_matricula = m.id_matricula
  LEFT JOIN (
    SELECT
      md.id_matricula,
      GROUP_CONCAT(CONCAT(ca.nombres, ' ', ca.apellidos) ORDER BY md.id_matricula_detalle SEPARATOR ' | ') AS nombres_clientes,
      GROUP_CONCAT(
        CONCAT(
          CONCAT(ca.nombres, ' ', ca.apellidos), ' | ', cu.nombre,
          ' | ', CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area),
          ' | ', CONCAT(p.nombres, ' ', p.apellidos),
          ' | ', th.descripcion, ' ', TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')
        )
        ORDER BY md.id_matricula_detalle
        SEPARATOR '\n'
      ) AS alumnos_cursos
    FROM matriculas_detalle md
    INNER JOIN clientes ca ON ca.id_cliente = md.id_cliente_asistencia
    INNER JOIN cursos_programados cp ON cp.id_curso_programado = md.id_curso_programado
    INNER JOIN cursos cu ON cu.id_curso = cp.id_curso
    INNER JOIN sub_areas sa ON sa.id_sub_area = cp.id_sub_area
    INNER JOIN areas a ON a.id_area = sa.id_area
    INNER JOIN profesores p ON p.id_profesor = cp.id_profesor
    INNER JOIN tipos_horario th ON th.id_tipo_horario = cp.id_tipo_horario
    GROUP BY md.id_matricula
  ) alumnos ON alumnos.id_matricula = m.id_matricula
  WHERE m.estado = 'Activa'
    AND (
      p_termino IS NULL OR p_termino = ''
      OR COALESCE(alumnos.nombres_clientes, CONCAT(cli.nombres, ' ', cli.apellidos)) LIKE CONCAT('%', p_termino, '%')
      OR m.id_matricula LIKE CONCAT('%', p_termino, '%')
    )
    AND (m.monto_final - COALESCE(cobros.total_cobros, 0)) > 0
  ORDER BY m.fecha_matricula DESC;
END $$

DELIMITER ;
