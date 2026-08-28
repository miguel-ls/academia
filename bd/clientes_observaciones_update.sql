USE academia_cursos;

-- Agrega el campo observaciones (texto libre, longitud maxima) a la tabla clientes
ALTER TABLE clientes
ADD COLUMN observaciones TEXT NULL AFTER codigo_ubigeo;

DROP PROCEDURE IF EXISTS sp_clientes_obtener_por_id;
DROP PROCEDURE IF EXISTS sp_clientes_listar;
DROP PROCEDURE IF EXISTS sp_clientes_crear;
DROP PROCEDURE IF EXISTS sp_clientes_actualizar;
DROP PROCEDURE IF EXISTS sp_clientes_buscar;

DELIMITER $$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_clientes_obtener_por_id (IN p_id_cliente int)
BEGIN
  SELECT
    id_cliente,
    id_tipo_documento,
    numero_documento,
    nombres,
    apellidos,
    email,
    telefono,
    codigo_erp,
    direccion,
    codigo_ubigeo,
    observaciones,
    estado
  FROM clientes
  WHERE id_cliente = p_id_cliente;
END
$$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_clientes_listar ()
BEGIN
  SELECT
    c.id_cliente,
    c.nombres,
    c.apellidos,
    td.descripcion AS tipo_documento,
    c.numero_documento,
    c.email,
    c.telefono,
    c.direccion,
    c.codigo_ubigeo,
    c.observaciones,
    c.estado
  FROM clientes c
    JOIN tipos_documento td
      ON c.id_tipo_documento = td.id_tipo_documento
  ORDER BY c.apellidos, c.nombres;
END
$$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_clientes_crear (IN p_id_tipo_documento int,
IN p_numero_documento varchar(20),
IN p_nombres varchar(100),
IN p_apellidos varchar(100),
IN p_email varchar(100),
IN p_telefono varchar(20),
IN p_codigo_erp varchar(20),
IN p_direccion varchar(255),
IN p_codigo_ubigeo varchar(10),
IN p_estado enum ('Activado', 'Desactivado'),
IN p_observaciones text)
BEGIN
  DECLARE cliente_existente int;

  SELECT
    COUNT(*) INTO cliente_existente
  FROM clientes
  WHERE id_tipo_documento = p_id_tipo_documento
  AND numero_documento = p_numero_documento;

  IF cliente_existente > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un cliente con el mismo tipo y número de documento.';
  ELSE
    INSERT INTO clientes (id_tipo_documento, numero_documento, nombres, apellidos, email, telefono, codigo_erp, direccion, codigo_ubigeo, observaciones, estado)
      VALUES (p_id_tipo_documento, p_numero_documento, p_nombres, p_apellidos, p_email, p_telefono, p_codigo_erp, p_direccion, p_codigo_ubigeo, p_observaciones, p_estado);
    SELECT
      LAST_INSERT_ID() AS id_cliente;
  END IF;
END
$$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_clientes_actualizar (IN p_id_cliente int,
IN p_id_tipo_documento int,
IN p_numero_documento varchar(20),
IN p_nombres varchar(100),
IN p_apellidos varchar(100),
IN p_email varchar(100),
IN p_telefono varchar(20),
IN p_codigo_erp varchar(20),
IN p_direccion varchar(255),
IN p_codigo_ubigeo varchar(10),
IN p_estado enum ('Activado', 'Desactivado'),
IN p_observaciones text)
BEGIN
  DECLARE cliente_existente int;
  SELECT
    COUNT(*) INTO cliente_existente
  FROM clientes
  WHERE id_tipo_documento = p_id_tipo_documento
  AND numero_documento = p_numero_documento
  AND id_cliente != p_id_cliente;

  IF cliente_existente > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El nuevo número de documento ya está en uso por otro cliente.';
  ELSE
    UPDATE clientes
    SET id_tipo_documento = p_id_tipo_documento,
        numero_documento = p_numero_documento,
        nombres = p_nombres,
        apellidos = p_apellidos,
        email = p_email,
        telefono = p_telefono,
        codigo_erp = p_codigo_erp,
        direccion = p_direccion,
        codigo_ubigeo = p_codigo_ubigeo,
        observaciones = p_observaciones,
        estado = p_estado
    WHERE id_cliente = p_id_cliente;
  END IF;
END
$$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_clientes_buscar (IN p_termino varchar(100))
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
    c.direccion,
    c.codigo_ubigeo,
    c.observaciones,
    c.estado
  FROM clientes c
    JOIN tipos_documento td
      ON c.id_tipo_documento = td.id_tipo_documento
  WHERE c.nombres LIKE @termino_busqueda
  OR c.apellidos LIKE @termino_busqueda
  OR c.numero_documento LIKE @termino_busqueda
  ORDER BY c.apellidos, c.nombres;
END
$$

DELIMITER ;
