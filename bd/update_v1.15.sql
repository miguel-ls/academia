DELIMITER $$

CREATE PROCEDURE `sp_matricula_eliminar`(
    IN p_id_matricula INT
)
BEGIN
    -- This procedure performs a HARD DELETE of an enrollment.
    -- It should be used with extreme caution.

    -- Start a transaction to ensure all or nothing is deleted.
    START TRANSACTION;

    -- 1. Delete associated attendance records.
    -- We need to get the list of matricula_detalle_ids first.
    DELETE FROM asistencia_cliente
    WHERE id_matricula_detalle IN (SELECT id_matricula_detalle FROM matriculas_detalle WHERE id_matricula = p_id_matricula);

    -- 2. Delete the detail records.
    DELETE FROM matriculas_detalle
    WHERE id_matricula = p_id_matricula;

    -- 3. Delete the header record.
    DELETE FROM matriculas
    WHERE id_matricula = p_id_matricula;

    COMMIT;
END$$

DELIMITER ;
