ALTER TABLE matriculas
  MODIFY fecha_matricula DATE NOT NULL;

DROP PROCEDURE IF EXISTS sp_matricula_registrar_cabecera;
DROP PROCEDURE IF EXISTS sp_matricula_cabecera_actualizar;
DROP PROCEDURE IF EXISTS sp_matricula_registrar_detalle;
DROP PROCEDURE IF EXISTS sp_matricula_detalle_actualizar;
DROP PROCEDURE IF EXISTS sp_matricula_obtener_detalles_por_id_matricula;
DROP PROCEDURE IF EXISTS sp_asistencia_cliente_generar_cronograma;
DROP PROCEDURE IF EXISTS sp_cliente_horarios_activos;
DROP PROCEDURE IF EXISTS sp_asistencia_cliente_listar_cursos;
DROP PROCEDURE IF EXISTS sp_asistencia_cliente_agregar_dias;
DROP PROCEDURE IF EXISTS sp_asistencia_cliente_obtener_detalle_curso;
DROP PROCEDURE IF EXISTS sp_matriculas_listar;
DROP PROCEDURE IF EXISTS sp_calendario_cursos_activos;

DELIMITER $$

CREATE PROCEDURE sp_matricula_registrar_cabecera (
  IN p_id_cliente INT,
  IN p_id_usuario_registro INT,
  IN p_id_forma_pago INT,
  IN p_fecha_matricula DATE,
  IN p_fecha_inicio_clases DATE,
  IN p_fecha_fin_clases DATE,
  IN p_monto_total DECIMAL(10, 2),
  IN p_descuento_total DECIMAL(10, 2),
  IN p_monto_final DECIMAL(10, 2),
  IN p_observaciones TEXT
)
BEGIN
  INSERT INTO matriculas (
    id_cliente, id_usuario_registro, id_forma_pago, fecha_matricula,
    fecha_inicio_clases, fecha_fin_clases, monto_total, descuento_total,
    monto_final, observaciones
  ) VALUES (
    p_id_cliente, p_id_usuario_registro, p_id_forma_pago, p_fecha_matricula,
    p_fecha_inicio_clases, p_fecha_fin_clases, p_monto_total, p_descuento_total,
    p_monto_final, p_observaciones
  );

  SELECT LAST_INSERT_ID() AS id_matricula;
END$$

CREATE PROCEDURE sp_matricula_cabecera_actualizar (
  IN p_id_matricula INT,
  IN p_id_forma_pago INT,
  IN p_fecha_matricula DATE,
  IN p_observaciones TEXT
)
BEGIN
  UPDATE matriculas
  SET id_forma_pago = p_id_forma_pago,
      fecha_matricula = p_fecha_matricula,
      observaciones = p_observaciones
  WHERE id_matricula = p_id_matricula;
END$$

CREATE PROCEDURE sp_matricula_registrar_detalle (
  IN p_id_matricula INT,
  IN p_id_curso_programado INT,
  IN p_id_cliente_asistencia INT,
  IN p_precio_pactado DECIMAL(10, 2),
  IN p_descuento DECIMAL(10, 2),
  IN p_precio_final DECIMAL(10, 2),
  IN p_fecha_inicio_clases DATE,
  IN p_fecha_fin_clases DATE
)
BEGIN
  DECLARE v_vacantes INT;

  SELECT vacantes_disponibles INTO v_vacantes
  FROM cursos_programados
  WHERE id_curso_programado = p_id_curso_programado;

  IF v_vacantes > 0 THEN
    INSERT INTO matriculas_detalle (
      id_matricula, id_curso_programado, id_cliente_asistencia,
      precio_pactado, descuento, precio_final, fecha_inicio_clases, fecha_fin_clases
    ) VALUES (
      p_id_matricula, p_id_curso_programado, p_id_cliente_asistencia,
      p_precio_pactado, p_descuento, p_precio_final, p_fecha_inicio_clases, p_fecha_fin_clases
    );

    UPDATE cursos_programados
    SET vacantes_disponibles = vacantes_disponibles - 1
    WHERE id_curso_programado = p_id_curso_programado;

    SELECT LAST_INSERT_ID() AS id_matricula_detalle;
  ELSE
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay vacantes disponibles para este curso.';
  END IF;
END$$

CREATE PROCEDURE sp_matricula_obtener_detalles_por_id_matricula (IN p_id_matricula INT)
BEGIN
  SELECT
    md.id_matricula_detalle,
    md.id_curso_programado,
    cur.nombre AS nombre_curso,
    md.id_cliente_asistencia,
    CONCAT_WS(' ', cli_asist.nombres, NULLIF(cli_asist.apellidos, '')) AS nombre_cliente_asistencia,
    md.precio_pactado,
    md.descuento,
    md.precio_final,
    md.fecha_inicio_clases,
    md.fecha_fin_clases,
    CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
    CONCAT(p.nombres, ' ', p.apellidos) AS profesor,
    th.descripcion AS horario_dias,
    cp.hora_inicio,
    cp.hora_fin,
    th.dias_semana,
    cp.fecha_inicio,
    cp.fecha_fin
  FROM matriculas_detalle md
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN cursos cur ON cp.id_curso = cur.id_curso
    JOIN clientes cli_asist ON md.id_cliente_asistencia = cli_asist.id_cliente
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
  WHERE md.id_matricula = p_id_matricula
  ORDER BY md.id_matricula_detalle ASC;
END$$

CREATE PROCEDURE sp_asistencia_cliente_generar_cronograma (IN p_id_matricula_detalle INT)
BEGIN
  DECLARE v_fecha_actual DATE;
  DECLARE v_fecha_fin DATE;
  DECLARE v_id_cliente INT;
  DECLARE v_id_curso_programado INT;
  DECLARE v_dias_semana VARCHAR(20);

  SELECT
    md.fecha_inicio_clases,
    md.fecha_fin_clases,
    md.id_cliente_asistencia,
    cp.id_curso_programado,
    th.dias_semana
  INTO v_fecha_actual, v_fecha_fin, v_id_cliente, v_id_curso_programado, v_dias_semana
  FROM matriculas_detalle md
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
  WHERE md.id_matricula_detalle = p_id_matricula_detalle;

  IF v_id_cliente IS NOT NULL THEN
    DELETE FROM asistencia_cliente
    WHERE id_matricula_detalle = p_id_matricula_detalle;

    WHILE v_fecha_actual <= v_fecha_fin DO
      IF FIND_IN_SET(WEEKDAY(v_fecha_actual) + 1, v_dias_semana COLLATE utf8mb4_unicode_ci) THEN
        INSERT INTO asistencia_cliente (id_matricula_detalle, id_cliente, fecha_clase, estado)
        VALUES (p_id_matricula_detalle, v_id_cliente, v_fecha_actual, 'Programado');
      END IF;
      SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
    END WHILE;
  END IF;
END$$

CREATE PROCEDURE sp_matricula_detalle_actualizar (
  IN p_id_matricula_detalle INT,
  IN p_id_cliente_asistencia INT,
  IN p_precio_pactado DECIMAL(10, 2),
  IN p_descuento DECIMAL(10, 2),
  IN p_fecha_inicio_clases DATE,
  IN p_fecha_fin_clases DATE
)
BEGIN
  DECLARE v_precio_final DECIMAL(10, 2);
  DECLARE v_cliente_actual INT;
  DECLARE v_fecha_inicio_actual DATE;
  DECLARE v_fecha_fin_actual DATE;

  SELECT id_cliente_asistencia, fecha_inicio_clases, fecha_fin_clases
  INTO v_cliente_actual, v_fecha_inicio_actual, v_fecha_fin_actual
  FROM matriculas_detalle
  WHERE id_matricula_detalle = p_id_matricula_detalle;

  SET v_precio_final = p_precio_pactado - p_descuento;

  UPDATE matriculas_detalle
  SET id_cliente_asistencia = p_id_cliente_asistencia,
      precio_pactado = p_precio_pactado,
      descuento = p_descuento,
      precio_final = v_precio_final,
      fecha_inicio_clases = p_fecha_inicio_clases,
      fecha_fin_clases = p_fecha_fin_clases
  WHERE id_matricula_detalle = p_id_matricula_detalle;

  IF NOT (v_cliente_actual <=> p_id_cliente_asistencia)
     OR NOT (v_fecha_inicio_actual <=> p_fecha_inicio_clases)
     OR NOT (v_fecha_fin_actual <=> p_fecha_fin_clases) THEN
    CALL sp_asistencia_cliente_generar_cronograma(p_id_matricula_detalle);
  END IF;
END$$

CREATE PROCEDURE sp_cliente_horarios_activos (IN p_id_cliente INT)
BEGIN
  SELECT
    cp.id_sub_area,
    md.fecha_inicio_clases AS fecha_inicio,
    md.fecha_fin_clases AS fecha_fin,
    cp.hora_inicio,
    cp.hora_fin,
    th.dias_semana
  FROM matriculas m
    JOIN matriculas_detalle md ON m.id_matricula = md.id_matricula
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
  WHERE md.id_cliente_asistencia = p_id_cliente
    AND m.estado = 'Activa';
END$$

CREATE PROCEDURE sp_asistencia_cliente_listar_cursos (
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
    CONCAT_WS(' ', cli.nombres, NULLIF(cli.apellidos, '')) AS cliente_nombre,
    MIN(ac.fecha_clase) AS fecha_inicio,
    MAX(ac.fecha_clase) AS fecha_fin,
    th.descripcion AS dias,
    CONCAT(TIME_FORMAT(cp.hora_inicio, '%h:%i %p'), ' - ', TIME_FORMAT(cp.hora_fin, '%h:%i %p')) AS horas,
    CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
    mc.estado
  FROM matriculas mc
    JOIN matriculas_detalle md ON mc.id_matricula = md.id_matricula
    JOIN clientes cli ON md.id_cliente_asistencia = cli.id_cliente
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    LEFT JOIN asistencia_cliente ac ON ac.id_matricula_detalle = md.id_matricula_detalle
  WHERE (p_id_cliente IS NULL OR md.id_cliente_asistencia = p_id_cliente)
    AND (p_id_curso IS NULL OR cp.id_curso = p_id_curso)
  GROUP BY mc.id_matricula,
           md.id_matricula_detalle,
           cp.id_curso_programado,
           c.nombre,
           cli.nombres,
           cli.apellidos,
           th.descripcion,
           cp.hora_inicio,
           cp.hora_fin,
           a.nombre,
           sa.descripcion,
           sa.numero_sub_area,
           mc.estado
  HAVING (p_fecha_inicio IS NULL OR MIN(ac.fecha_clase) >= p_fecha_inicio)
     AND (p_fecha_fin IS NULL OR MAX(ac.fecha_clase) <= p_fecha_fin)
  ORDER BY fecha_inicio DESC, cliente_nombre ASC;
END$$

CREATE PROCEDURE sp_asistencia_cliente_agregar_dias (
  IN p_id_matricula_detalle INT,
  IN p_fecha_fin DATE
)
BEGIN
  DECLARE v_fecha DATE;
  DECLARE v_fecha_inicio_clases DATE;
  DECLARE v_ultima_fecha_asistencia DATE;
  DECLARE v_id_cliente INT;
  DECLARE v_dias_semana VARCHAR(20);
  DECLARE v_dias_agregados INT DEFAULT 0;

  SELECT md.fecha_inicio_clases, md.id_cliente_asistencia, th.dias_semana
  INTO v_fecha_inicio_clases, v_id_cliente, v_dias_semana
  FROM matriculas_detalle md
    JOIN cursos_programados cp ON cp.id_curso_programado = md.id_curso_programado
    JOIN tipos_horario th ON th.id_tipo_horario = cp.id_tipo_horario
  WHERE md.id_matricula_detalle = p_id_matricula_detalle;

  IF v_id_cliente IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Detalle de matrícula no encontrado.';
  END IF;

  SELECT MAX(fecha_clase) INTO v_ultima_fecha_asistencia
  FROM asistencia_cliente
  WHERE id_matricula_detalle = p_id_matricula_detalle
    AND fecha_clase >= v_fecha_inicio_clases;

  IF v_ultima_fecha_asistencia IS NOT NULL THEN
    SET v_fecha = DATE_ADD(v_ultima_fecha_asistencia, INTERVAL 1 DAY);
  ELSE
    SET v_fecha = v_fecha_inicio_clases;
  END IF;

  IF p_fecha_fin < v_fecha THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha final debe ser posterior a la última asistencia registrada.';
  END IF;

  WHILE v_fecha <= p_fecha_fin DO
    IF FIND_IN_SET(WEEKDAY(v_fecha) + 1, v_dias_semana) > 0
      AND NOT EXISTS (
        SELECT 1
        FROM asistencia_cliente
        WHERE id_matricula_detalle = p_id_matricula_detalle
          AND fecha_clase = v_fecha
      ) THEN
      INSERT INTO asistencia_cliente (
        id_matricula_detalle, id_cliente, fecha_clase, estado, observaciones
      ) VALUES (
        p_id_matricula_detalle, v_id_cliente, v_fecha, 'Programado', NULL
      );
      SET v_dias_agregados = v_dias_agregados + 1;
    END IF;
    SET v_fecha = DATE_ADD(v_fecha, INTERVAL 1 DAY);
  END WHILE;

  UPDATE matriculas_detalle
  SET fecha_fin_clases = p_fecha_fin
  WHERE id_matricula_detalle = p_id_matricula_detalle;

  SELECT v_dias_agregados AS dias_agregados;
END$$

CREATE PROCEDURE sp_asistencia_cliente_obtener_detalle_curso (IN p_id_matricula_detalle INT)
BEGIN
  SELECT
    c.nombre AS curso_nombre,
    CONCAT(cli.nombres, ' ', cli.apellidos) AS cliente_nombre,
    CONCAT(p.nombres, ' ', p.apellidos) AS profesor_nombre,
    CONCAT(a.nombre, ' - ', sa.descripcion, ' ', sa.numero_sub_area) AS ubicacion,
    th.descripcion AS tipo_horario_nombre,
    md.fecha_inicio_clases,
    md.fecha_fin_clases,
    COALESCE(
      (SELECT MAX(ac.fecha_clase)
       FROM asistencia_cliente ac
       WHERE ac.id_matricula_detalle = md.id_matricula_detalle),
      md.fecha_fin_clases
    ) AS fecha_fin_actual,
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

CREATE PROCEDURE sp_matriculas_listar ()
BEGIN
  SELECT
    m.id_matricula,
    GROUP_CONCAT(
      CONCAT(
        COALESCE(
          CONCAT_WS(' ', ca.nombres, NULLIF(ca.apellidos, '')),
          CONCAT_WS(' ', c.nombres, NULLIF(c.apellidos, ''))
        ),
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
  GROUP BY m.id_matricula,
           c.nombres,
           c.apellidos,
           m.fecha_matricula,
           m.monto_final,
           m.estado,
           u.nombre_usuario
  ORDER BY m.fecha_matricula DESC;
END$$

CREATE PROCEDURE sp_calendario_cursos_activos ()
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
    CONCAT_WS(' ', cli.nombres, NULLIF(cli.apellidos, '')) AS nombre_cliente,
    ac.fecha_clase,
    cp.hora_inicio,
    cp.hora_fin,
    th.descripcion AS horario_dias
  FROM matriculas_detalle md
    JOIN matriculas m ON md.id_matricula = m.id_matricula
    JOIN asistencia_cliente ac ON ac.id_matricula_detalle = md.id_matricula_detalle
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN clientes cli ON md.id_cliente_asistencia = cli.id_cliente
    JOIN cursos c ON cp.id_curso = c.id_curso
    JOIN profesores p ON cp.id_profesor = p.id_profesor
    JOIN sub_areas sa ON cp.id_sub_area = sa.id_sub_area
    JOIN areas a ON sa.id_area = a.id_area
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
  WHERE m.estado = 'Activa'
  ORDER BY ac.fecha_clase, cp.hora_inicio;
END$$

DELIMITER ;