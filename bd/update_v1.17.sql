DELIMITER $$

-- Elimina una línea de detalle de una matrícula y su asistencia asociada.
CREATE PROCEDURE `sp_matricula_detalle_eliminar`(
    IN p_id_matricula_detalle INT
)
BEGIN
    -- No se necesita transacción aquí porque son solo dos operaciones
    -- y la recalculación se hará en un paso separado.

    -- 1. Eliminar la asistencia generada para este detalle.
    DELETE FROM asistencia_cliente WHERE id_matricula_detalle = p_id_matricula_detalle;

    -- 2. Eliminar el detalle de la matrícula.
    DELETE FROM matriculas_detalle WHERE id_matricula_detalle = p_id_matricula_detalle;
END$$


-- Recalcula los montos totales de la cabecera de una matrícula.
CREATE PROCEDURE `sp_matricula_cabecera_recalcular`(
    IN p_id_matricula INT
)
BEGIN
    DECLARE v_monto_total DECIMAL(10, 2);
    DECLARE v_descuento_total DECIMAL(10, 2);
    DECLARE v_monto_final DECIMAL(10, 2);

    -- Calcular los nuevos totales desde los detalles restantes.
    SELECT
        IFNULL(SUM(precio_pactado), 0),
        IFNULL(SUM(descuento), 0)
    INTO
        v_monto_total,
        v_descuento_total
    FROM
        matriculas_detalle
    WHERE
        id_matricula = p_id_matricula;

    SET v_monto_final = v_monto_total - v_descuento_total;

    -- Actualizar la cabecera de la matrícula.
    UPDATE matriculas_cabecera
    SET
        monto_total = v_monto_total,
        descuento_total = v_descuento_total,
        monto_final = v_monto_final
    WHERE
        id_matricula = p_id_matricula;
END$$

DELIMITER ;
