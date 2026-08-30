DROP PROCEDURE IF EXISTS sp_cursos_buscar;

DELIMITER $$

CREATE PROCEDURE sp_cursos_buscar (IN p_term varchar(100))
BEGIN
  SELECT
    c.id_curso,
    c.nombre,
    c.descripcion,
    c.codigo_erp,
    tc.nombre AS tipo_curso
  FROM cursos c
    JOIN tipos_curso tc
      ON c.id_tipo_curso = tc.id_tipo_curso
  WHERE CAST(c.id_curso AS CHAR) LIKE CONCAT('%', p_term, '%')
  OR c.codigo_erp LIKE CONCAT('%', p_term, '%')
  OR c.nombre LIKE CONCAT('%', p_term, '%')
  OR tc.nombre LIKE CONCAT('%', p_term, '%')
  OR c.descripcion LIKE CONCAT('%', p_term, '%')
  ORDER BY c.nombre;
END$$

DELIMITER ;
