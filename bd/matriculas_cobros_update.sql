USE academia_cursos;

ALTER TABLE matriculas
  MODIFY COLUMN id_forma_pago INT NULL;

DROP PROCEDURE IF EXISTS sp_matricula_obtener_cabecera_por_id;
DROP PROCEDURE IF EXISTS sp_matricula_cabecera_actualizar;

DELIMITER $$

CREATE PROCEDURE sp_matricula_obtener_cabecera_por_id (IN p_id_matricula INT)
BEGIN
  SELECT
    mc.id_matricula,
    mc.id_cliente,
    mc.id_forma_pago,
    CONCAT(c.nombres, ' ', c.apellidos) AS nombre_cliente,
    mc.fecha_matricula,
    mc.fecha_inicio_clases,
    mc.fecha_fin_clases,
    mc.monto_total,
    mc.descuento_total,
    mc.monto_final,
    mc.observaciones,
    fp.nombre AS forma_pago,
    mc.estado
  FROM matriculas mc
  INNER JOIN clientes c ON mc.id_cliente = c.id_cliente
  LEFT JOIN formas_pago fp ON mc.id_forma_pago = fp.id_forma_pago
  WHERE mc.id_matricula = p_id_matricula;
END $$

CREATE PROCEDURE sp_matricula_cabecera_actualizar (
  IN p_id_matricula INT,
  IN p_id_forma_pago INT,
  IN p_observaciones TEXT
)
BEGIN
  UPDATE matriculas
  SET id_forma_pago = p_id_forma_pago,
      observaciones = p_observaciones
  WHERE id_matricula = p_id_matricula;
END $$

DROP PROCEDURE IF EXISTS sp_matriculas_listar $$

CREATE PROCEDURE sp_matriculas_listar ()
BEGIN
  SELECT
    m.id_matricula,
    GROUP_CONCAT(
      CONCAT(
        COALESCE(CONCAT(ca.nombres, ' ', ca.apellidos), CONCAT(c.nombres, ' ', c.apellidos)),
        ' | ',
        cu.nombre,
        ' | ',
        CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area),
        ' | ',
        CONCAT(p.nombres, ' ', p.apellidos),
        ' | ',
        th.descripcion, ' ', TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')
      )
      ORDER BY md.id_matricula_detalle
      SEPARATOR '\n'
    ) AS alumnos_cursos,
    GROUP_CONCAT(DISTINCT COALESCE(md.id_cliente_asistencia, m.id_cliente) ORDER BY md.id_matricula_detalle) AS clientes_asistencia_ids,
    GROUP_CONCAT(DISTINCT cu.id_curso ORDER BY md.id_matricula_detalle) AS cursos_ids,
    GROUP_CONCAT(DISTINCT sa.id_sub_area ORDER BY md.id_matricula_detalle) AS ubicaciones_ids,
    GROUP_CONCAT(DISTINCT p.id_profesor ORDER BY md.id_matricula_detalle) AS profesores_ids,
    GROUP_CONCAT(DISTINCT cp.id_curso_programado ORDER BY md.id_matricula_detalle) AS horarios_ids,
    m.fecha_matricula,
    m.monto_final,
    m.estado,
    u.nombre_usuario AS registrado_por
  FROM matriculas m
  INNER JOIN clientes c ON m.id_cliente = c.id_cliente
  INNER JOIN usuarios u ON m.id_usuario_registro = u.id_usuario
  LEFT JOIN matriculas_detalle md ON md.id_matricula = m.id_matricula
  LEFT JOIN clientes ca ON ca.id_cliente = md.id_cliente_asistencia
  LEFT JOIN cursos_programados cp ON cp.id_curso_programado = md.id_curso_programado
  LEFT JOIN cursos cu ON cu.id_curso = cp.id_curso
  LEFT JOIN sub_areas sa ON sa.id_sub_area = cp.id_sub_area
  LEFT JOIN areas a ON a.id_area = sa.id_area
  LEFT JOIN profesores p ON p.id_profesor = cp.id_profesor
  LEFT JOIN tipos_horario th ON th.id_tipo_horario = cp.id_tipo_horario
  GROUP BY m.id_matricula, c.nombres, c.apellidos, m.fecha_matricula, m.monto_final, m.estado, u.nombre_usuario
  ORDER BY m.fecha_matricula DESC;
END $$

DROP PROCEDURE IF EXISTS sp_matriculas_horarios_buscar $$

CREATE PROCEDURE sp_matriculas_horarios_buscar (IN p_termino VARCHAR(100))
BEGIN
  SELECT
    cp.id_curso_programado,
    CONCAT(th.descripcion, ' ', TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')) AS horario
  FROM cursos_programados cp
  INNER JOIN tipos_horario th ON th.id_tipo_horario = cp.id_tipo_horario
  WHERE CONCAT(th.descripcion, ' ', TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')) LIKE CONCAT('%', p_termino, '%')
  ORDER BY th.descripcion, cp.hora_inicio
  LIMIT 20;
END $$

DELIMITER ;
