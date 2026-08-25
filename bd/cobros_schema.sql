DROP TABLE IF EXISTS cobros;
DROP PROCEDURE IF EXISTS sp_cobros_listar;
DROP PROCEDURE IF EXISTS sp_cobros_obtener_por_id;
DROP PROCEDURE IF EXISTS sp_cobros_crear;
DROP PROCEDURE IF EXISTS sp_cobros_actualizar;
DROP PROCEDURE IF EXISTS sp_cobros_eliminar;
DROP PROCEDURE IF EXISTS sp_cobros_matriculas_pendientes;
DROP PROCEDURE IF EXISTS sp_cobros_saldo_matricula;

CREATE TABLE cobros (
  id_cobro INT NOT NULL AUTO_INCREMENT,
  id_matricula INT NOT NULL,
  id_forma_pago INT NOT NULL,
  fecha_cobro DATE NOT NULL,
  numero_operacion VARCHAR(20) NOT NULL,
  importe DECIMAL(10,2) NOT NULL,
  observaciones TEXT DEFAULT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_usuario_creacion INT NOT NULL,
  fecha_modificacion DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id_cobro),
  KEY idx_cobros_matricula (id_matricula),
  KEY idx_cobros_forma_pago (id_forma_pago),
  KEY idx_cobros_fecha (fecha_cobro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE cobros
  ADD CONSTRAINT fk_cobros_matricula
  FOREIGN KEY (id_matricula) REFERENCES matriculas (id_matricula) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_cobros_forma_pago
  FOREIGN KEY (id_forma_pago) REFERENCES formas_pago (id_forma_pago) ON DELETE RESTRICT ON UPDATE CASCADE;

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

CREATE PROCEDURE sp_cobros_obtener_por_id (IN p_id_cobro INT)
BEGIN
  SELECT
    c.id_cobro,
    c.id_matricula,
    c.id_forma_pago,
    c.fecha_cobro,
    c.numero_operacion,
    c.importe,
    c.observaciones,
    c.fecha_creacion,
    c.id_usuario_creacion,
    c.fecha_modificacion,
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

CREATE PROCEDURE sp_cobros_crear (
  IN p_id_matricula INT,
  IN p_id_forma_pago INT,
  IN p_fecha_cobro DATE,
  IN p_numero_operacion VARCHAR(20),
  IN p_importe DECIMAL(10,2),
  IN p_observaciones TEXT,
  IN p_id_usuario_creacion INT
)
BEGIN
  INSERT INTO cobros (
    id_matricula,
    id_forma_pago,
    fecha_cobro,
    numero_operacion,
    importe,
    observaciones,
    id_usuario_creacion,
    fecha_modificacion
  ) VALUES (
    p_id_matricula,
    p_id_forma_pago,
    p_fecha_cobro,
    p_numero_operacion,
    p_importe,
    p_observaciones,
    p_id_usuario_creacion,
    NULL
  );
END $$

CREATE PROCEDURE sp_cobros_actualizar (
  IN p_id_cobro INT,
  IN p_id_forma_pago INT,
  IN p_fecha_cobro DATE,
  IN p_numero_operacion VARCHAR(20),
  IN p_importe DECIMAL(10,2),
  IN p_observaciones TEXT
)
BEGIN
  UPDATE cobros
  SET id_forma_pago = p_id_forma_pago,
      fecha_cobro = p_fecha_cobro,
      numero_operacion = p_numero_operacion,
      importe = p_importe,
      observaciones = p_observaciones,
      fecha_modificacion = NOW()
  WHERE id_cobro = p_id_cobro;
END $$

CREATE PROCEDURE sp_cobros_eliminar (IN p_id_cobro INT)
BEGIN
  DELETE FROM cobros WHERE id_cobro = p_id_cobro;
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
          CONCAT(ca.nombres, ' ', ca.apellidos),
          ' | ', cu.nombre,
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

CREATE PROCEDURE sp_cobros_saldo_matricula (
  IN p_id_matricula INT,
  IN p_id_cobro_excluir INT
)
BEGIN
  SELECT
    m.monto_final - COALESCE(SUM(CASE WHEN c.id_cobro = p_id_cobro_excluir THEN 0 ELSE c.importe END), 0) AS saldo_pendiente
  FROM matriculas m
  LEFT JOIN cobros c ON c.id_matricula = m.id_matricula
  WHERE m.id_matricula = p_id_matricula
  GROUP BY m.id_matricula, m.monto_final;
END $$

DELIMITER ;
