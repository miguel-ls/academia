DELIMITER $$

CREATE PROCEDURE `sp_matriculas_contar_por_curso`(
    IN p_id_curso_programado INT
)
BEGIN
    SELECT
        COUNT(md.id_matricula_detalle) AS inscritos
    FROM
        matriculas_detalle md
    JOIN
        matriculas_cabecera mc ON md.id_matricula = mc.id_matricula
    WHERE
        md.id_curso_programado = p_id_curso_programado
        AND mc.estado = 1; -- Contar solo matrículas activas
END$$

DELIMITER ;
