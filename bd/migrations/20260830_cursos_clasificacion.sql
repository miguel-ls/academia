DROP PROCEDURE IF EXISTS sp_categorias_listar;
DROP PROCEDURE IF EXISTS sp_grupos_listar_por_categoria;
DROP PROCEDURE IF EXISTS sp_clases_listar_por_categoria_grupo;
DROP PROCEDURE IF EXISTS sp_familias_listar_por_categoria_grupo_clase;
DROP PROCEDURE IF EXISTS sp_cursos_crear;
DROP PROCEDURE IF EXISTS sp_cursos_actualizar;
DROP PROCEDURE IF EXISTS sp_cursos_obtener_por_id;

DELIMITER $$

CREATE PROCEDURE sp_categorias_listar ()
BEGIN
  SELECT Aca_cCategoria AS codigo, Aca_cDescripLarga AS descripcion
  FROM categoria
  WHERE COALESCE(Aca_cDeleted, '') <> '*'
  ORDER BY Aca_cCategoria;
END$$

CREATE PROCEDURE sp_grupos_listar_por_categoria (IN p_categoria varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)
BEGIN
  SELECT Gru_cGrupo AS codigo, Gru_cDescripLarga AS descripcion
  FROM grupo
  WHERE Aca_cCategoria = p_categoria
    AND COALESCE(Gru_cDeleted, '') <> '*'
  ORDER BY Gru_cGrupo;
END$$

CREATE PROCEDURE sp_clases_listar_por_categoria_grupo (IN p_categoria varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, IN p_grupo varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)
BEGIN
  SELECT Cla_cClase AS codigo, Cla_cDescripLarga AS descripcion
  FROM clase
  WHERE Aca_cCategoria = p_categoria
    AND Gru_cGrupo = p_grupo
    AND COALESCE(Cla_cDeleted, '') <> '*'
  ORDER BY Cla_cClase;
END$$

CREATE PROCEDURE sp_familias_listar_por_categoria_grupo_clase (IN p_categoria varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, IN p_grupo varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, IN p_clase varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)
BEGIN
  SELECT Fam_cFamilia AS codigo, Fam_cDescripLarga AS descripcion
  FROM familia
  WHERE Aca_cCategoria = p_categoria
    AND Gru_cGrupo = p_grupo
    AND Cla_cClase = p_clase
    AND COALESCE(Fam_cDeleted, '') <> '*'
  ORDER BY Fam_cFamilia;
END$$

CREATE PROCEDURE sp_cursos_crear (IN p_id_tipo_curso int, IN p_categoria_erp varchar(2), IN p_grupo_erp varchar(5), IN p_clase_erp varchar(4), IN p_familia_erp varchar(7), IN p_nombre varchar(150), IN p_descripcion text, IN p_codigo_erp varchar(20))
BEGIN
  INSERT INTO cursos (id_tipo_curso, categoria_erp, grupo_erp, clase_erp, familia_erp, nombre, descripcion, codigo_erp)
  VALUES (p_id_tipo_curso, p_categoria_erp, p_grupo_erp, p_clase_erp, p_familia_erp, p_nombre, p_descripcion, p_codigo_erp);
END$$

CREATE PROCEDURE sp_cursos_actualizar (IN p_id_curso int, IN p_id_tipo_curso int, IN p_categoria_erp varchar(2), IN p_grupo_erp varchar(5), IN p_clase_erp varchar(4), IN p_familia_erp varchar(7), IN p_nombre varchar(150), IN p_descripcion text, IN p_codigo_erp varchar(20))
BEGIN
  UPDATE cursos
  SET id_tipo_curso = p_id_tipo_curso,
      categoria_erp = p_categoria_erp,
      grupo_erp = p_grupo_erp,
      clase_erp = p_clase_erp,
      familia_erp = p_familia_erp,
      nombre = p_nombre,
      descripcion = p_descripcion,
      codigo_erp = p_codigo_erp
  WHERE id_curso = p_id_curso;
END$$

CREATE PROCEDURE sp_cursos_obtener_por_id (IN p_id int)
BEGIN
  SELECT id_curso, id_tipo_curso,
         TRIM(categoria_erp) AS categoria_erp,
         TRIM(grupo_erp) AS grupo_erp,
         TRIM(clase_erp) AS clase_erp,
         TRIM(familia_erp) AS familia_erp,
         nombre, descripcion, codigo_erp
  FROM cursos
  WHERE id_curso = p_id;
END$$

DELIMITER ;
