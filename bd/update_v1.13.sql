-- =================================================================
-- Update para v1.13 - Matrícula por Alumno Individual
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- 1. Modificar la tabla de detalle de matrícula para añadir el cliente de asistencia
ALTER TABLE `matriculas_detalle`
ADD COLUMN `id_cliente_asistencia` INT NULL AFTER `id_curso_programado`,
ADD INDEX `fk_detalle_cliente_asistencia_idx` (`id_cliente_asistencia` ASC);

ALTER TABLE `matriculas_detalle`
ADD CONSTRAINT `fk_detalle_cliente_asistencia`
  FOREIGN KEY (`id_cliente_asistencia`)
  REFERENCES `clientes` (`id_cliente`)
  ON DELETE SET NULL -- Si se borra el cliente, se anula la asistencia pero no el detalle
  ON UPDATE CASCADE;


-- 2. Crear el SP para registrar la cabecera (si no existe)
DROP PROCEDURE IF EXISTS `sp_matricula_registrar_cabecera`$$
CREATE PROCEDURE `sp_matricula_registrar_cabecera`(
    IN p_id_cliente INT,
    IN p_id_usuario_registro INT,
    IN p_id_forma_pago INT,
    IN p_fecha_inicio_clases DATE,
    IN p_fecha_fin_clases DATE,
    IN p_monto_total DECIMAL(10,2),
    IN p_descuento_total DECIMAL(10,2),
    IN p_monto_final DECIMAL(10,2),
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO matriculas (id_cliente, id_usuario_registro, id_forma_pago, fecha_inicio_clases, fecha_fin_clases, monto_total, descuento_total, monto_final, observaciones)
    VALUES (p_id_cliente, p_id_usuario_registro, p_id_forma_pago, p_fecha_inicio_clases, p_fecha_fin_clases, p_monto_total, p_descuento_total, p_monto_final, p_observaciones);
    SELECT LAST_INSERT_ID() as id_matricula;
END$$


-- 3. Crear el SP para registrar el detalle (modificado)
DROP PROCEDURE IF EXISTS `sp_matricula_registrar_detalle`$$
CREATE PROCEDURE `sp_matricula_registrar_detalle`(
    IN p_id_matricula INT,
    IN p_id_curso_programado INT,
    IN p_id_cliente_asistencia INT, -- Nuevo parámetro
    IN p_precio_pactado DECIMAL(10,2),
    IN p_descuento DECIMAL(10,2),
    IN p_precio_final DECIMAL(10,2)
)
BEGIN
    DECLARE v_vacantes INT;
    -- Verificar vacantes
    SELECT vacantes_disponibles INTO v_vacantes FROM cursos_programados WHERE id_curso_programado = p_id_curso_programado;
    IF v_vacantes > 0 THEN
        -- Insertar detalle
        INSERT INTO matriculas_detalle (id_matricula, id_curso_programado, id_cliente_asistencia, precio_pactado, descuento, precio_final)
        VALUES (p_id_matricula, p_id_curso_programado, p_id_cliente_asistencia, p_precio_pactado, p_descuento, p_precio_final);
        -- Actualizar vacantes
        UPDATE cursos_programados SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id_curso_programado = p_id_curso_programado;
        SELECT LAST_INSERT_ID() as id_matricula_detalle;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay vacantes disponibles para este curso.';
    END IF;
END$$


-- 4. Crear el SP para generar asistencia del cliente (modificado)
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_generar_cronograma`$$
CREATE PROCEDURE `sp_asistencia_cliente_generar_cronograma`(IN p_id_matricula_detalle INT)
BEGIN
    DECLARE v_fecha_actual DATE;
    DECLARE v_fecha_fin DATE;
    DECLARE v_id_cliente INT;
    DECLARE v_id_curso_programado INT;
    DECLARE v_dias_semana VARCHAR(20);

    -- Obtener datos de la programación y el cliente de asistencia
    SELECT cp.fecha_inicio, cp.fecha_fin, md.id_cliente_asistencia, cp.id_curso_programado, th.dias_semana
    INTO v_fecha_actual, v_fecha_fin, v_id_cliente, v_id_curso_programado, v_dias_semana
    FROM matriculas_detalle md
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE md.id_matricula_detalle = p_id_matricula_detalle;

    -- Solo generar si hay un cliente de asistencia asignado
    IF v_id_cliente IS NOT NULL THEN
        -- Limpiar cronograma existente por si se regenera
        DELETE FROM asistencia_cliente WHERE id_matricula_detalle = p_id_matricula_detalle;

        -- Bucle para generar registros de asistencia
        WHILE v_fecha_actual <= v_fecha_fin DO
            IF FIND_IN_SET(WEEKDAY(v_fecha_actual) + 1, v_dias_semana COLLATE utf8mb4_unicode_ci) THEN
                INSERT INTO asistencia_cliente (id_matricula_detalle, id_cliente, fecha_clase, estado)
                VALUES (p_id_matricula_detalle, v_id_cliente, v_fecha_actual, 'Programado');
            END IF;
            SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
        END WHILE;
    END IF;
END$$


DELIMITER ;
