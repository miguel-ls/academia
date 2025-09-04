-- =================================================================
-- Script de Actualización de la Base de Datos - Versión 1.22
-- Corrige el SP `sp_matricula_eliminar` para que apunte a la tabla correcta.
-- =================================================================

USE `academia_cursos`;

DELIMITER $$

-- -----------------------------------------------------
-- SP: `sp_matricula_eliminar`
-- Definición correcta para la eliminación física de una matrícula.
-- Usa la tabla `matriculas` en lugar de `matriculas_cabecera`.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_matricula_eliminar`$$
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
        WHERE id_matricula_detalle IN (
            SELECT id_matricula_detalle
            FROM matriculas_detalle
            WHERE id_matricula = p_id_matricula
        );

        -- 2. Delete the detail records.
        DELETE FROM matriculas_detalle
        WHERE id_matricula = p_id_matricula;

        -- 3. Delete the header record.
        DELETE FROM matriculas -- This is the corrected table name.
        WHERE id_matricula = p_id_matricula;

    COMMIT;
END$$

DELIMITER ;
