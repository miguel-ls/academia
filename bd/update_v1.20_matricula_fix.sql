-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.20
-- Script maestro para corregir todos los SPs relacionados con la matrícula.
-- Este script redefine todos los procedimientos para asegurar que la base de datos
-- del usuario esté sincronizada y no contenga versiones antiguas o corruptas.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP 1: `sp_matricula_registrar_cabecera`
-- Definición correcta para registrar la cabecera de una matrícula.
-- -----------------------------------------------------
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

-- -----------------------------------------------------
-- SP 2: `sp_matricula_registrar_detalle`
-- Definición correcta para registrar el detalle de una matrícula.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_registrar_detalle`$$
CREATE PROCEDURE `sp_matricula_registrar_detalle`(
    IN p_id_matricula INT,
    IN p_id_curso_programado INT,
    IN p_id_cliente_asistencia INT,
    IN p_precio_pactado DECIMAL(10,2),
    IN p_descuento DECIMAL(10,2),
    IN p_precio_final DECIMAL(10,2)
)
BEGIN
    DECLARE v_vacantes INT;
    SELECT vacantes_disponibles INTO v_vacantes FROM cursos_programados WHERE id_curso_programado = p_id_curso_programado;
    IF v_vacantes > 0 THEN
        INSERT INTO matriculas_detalle (id_matricula, id_curso_programado, id_cliente_asistencia, precio_pactado, descuento, precio_final)
        VALUES (p_id_matricula, p_id_curso_programado, p_id_cliente_asistencia, p_precio_pactado, p_descuento, p_precio_final);
        UPDATE cursos_programados SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id_curso_programado = p_id_curso_programado;
        SELECT LAST_INSERT_ID() as id_matricula_detalle;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay vacantes disponibles para este curso.';
    END IF;
END$$

-- -----------------------------------------------------
-- SP 3: `sp_asistencia_cliente_generar_cronograma`
-- Definición correcta para generar el cronograma de asistencia.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_asistencia_cliente_generar_cronograma`$$
CREATE PROCEDURE `sp_asistencia_cliente_generar_cronograma`(IN p_id_matricula_detalle INT)
BEGIN
    DECLARE v_fecha_actual DATE;
    DECLARE v_fecha_fin DATE;
    DECLARE v_id_cliente INT;
    DECLARE v_id_curso_programado INT;
    DECLARE v_dias_semana VARCHAR(20);

    SELECT cp.fecha_inicio, cp.fecha_fin, md.id_cliente_asistencia, cp.id_curso_programado, th.dias_semana
    INTO v_fecha_actual, v_fecha_fin, v_id_cliente, v_id_curso_programado, v_dias_semana
    FROM matriculas_detalle md
    JOIN cursos_programados cp ON md.id_curso_programado = cp.id_curso_programado
    JOIN tipos_horario th ON cp.id_tipo_horario = th.id_tipo_horario
    WHERE md.id_matricula_detalle = p_id_matricula_detalle;

    IF v_id_cliente IS NOT NULL THEN
        DELETE FROM asistencia_cliente WHERE id_matricula_detalle = p_id_matricula_detalle;
        WHILE v_fecha_actual <= v_fecha_fin DO
            IF FIND_IN_SET(WEEKDAY(v_fecha_actual) + 1, v_dias_semana COLLATE utf8mb4_unicode_ci) THEN
                INSERT INTO asistencia_cliente (id_matricula_detalle, id_cliente, fecha_clase, estado)
                VALUES (p_id_matricula_detalle, v_id_cliente, v_fecha_actual, 'Programado');
            END IF;
            SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL 1 DAY);
        END WHILE;
    END IF;
END$$

-- -----------------------------------------------------
-- SP 4: `sp_matricula_detalle_eliminar`
-- Definición correcta para eliminar un detalle de matrícula.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_detalle_eliminar`$$
CREATE PROCEDURE `sp_matricula_detalle_eliminar`(
    IN p_id_matricula_detalle INT
)
BEGIN
    DELETE FROM asistencia_cliente WHERE id_matricula_detalle = p_id_matricula_detalle;
    DELETE FROM matriculas_detalle WHERE id_matricula_detalle = p_id_matricula_detalle;
END$$

-- -----------------------------------------------------
-- SP 5: `sp_matricula_cabecera_recalcular`
-- Definición correcta para recalcular los totales de la matrícula.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_cabecera_recalcular`$$
CREATE PROCEDURE `sp_matricula_cabecera_recalcular`(
    IN p_id_matricula INT
)
BEGIN
    DECLARE v_monto_total DECIMAL(10, 2);
    DECLARE v_descuento_total DECIMAL(10, 2);
    DECLARE v_monto_final DECIMAL(10, 2);

    SELECT IFNULL(SUM(precio_pactado), 0), IFNULL(SUM(descuento), 0)
    INTO v_monto_total, v_descuento_total
    FROM matriculas_detalle
    WHERE id_matricula = p_id_matricula;

    SET v_monto_final = v_monto_total - v_descuento_total;

    UPDATE matriculas
    SET
        monto_total = v_monto_total,
        descuento_total = v_descuento_total,
        monto_final = v_monto_final
    WHERE id_matricula = p_id_matricula;
END$$

DELIMITER ;
