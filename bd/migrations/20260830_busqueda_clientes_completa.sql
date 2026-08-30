DROP PROCEDURE IF EXISTS sp_clientes_buscar;

DELIMITER $$

CREATE PROCEDURE sp_clientes_buscar (IN p_termino varchar(100))
BEGIN
  SET @termino_busqueda = CONCAT('%', p_termino, '%');

  SELECT
    c.id_cliente,
    c.nombres,
    c.apellidos,
    td.descripcion AS tipo_documento,
    c.numero_documento,
    c.email,
    c.telefono,
    c.codigo_erp,
    c.direccion,
    c.codigo_ubigeo,
    c.observaciones,
    c.estado
  FROM clientes c
    JOIN tipos_documento td
      ON c.id_tipo_documento = td.id_tipo_documento
  WHERE CAST(c.id_cliente AS CHAR) LIKE @termino_busqueda
  OR c.codigo_erp LIKE @termino_busqueda
  OR c.nombres LIKE @termino_busqueda
  OR c.apellidos LIKE @termino_busqueda
  OR td.descripcion LIKE @termino_busqueda
  OR c.numero_documento LIKE @termino_busqueda
  OR c.email LIKE @termino_busqueda
  OR c.telefono LIKE @termino_busqueda
  OR c.estado LIKE @termino_busqueda
  OR c.direccion LIKE @termino_busqueda
  OR c.codigo_ubigeo LIKE @termino_busqueda
  OR c.observaciones LIKE @termino_busqueda
  ORDER BY c.apellidos, c.nombres;
END$$

DELIMITER ;
