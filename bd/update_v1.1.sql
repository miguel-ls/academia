-- =================================================================
-- Script de Actualización de Base de Datos v1.1
-- =================================================================
--
-- Añade el campo 'descripcion' a la tabla 'tipos_curso'.

USE `academia_cursos`;

ALTER TABLE `tipos_curso`
ADD COLUMN `descripcion` VARCHAR(255) NULL AFTER `nombre`;
