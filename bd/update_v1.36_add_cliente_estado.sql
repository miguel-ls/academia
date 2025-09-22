-- =================================================================
-- Update Script v1.36
-- Description: Adds the 'estado' column to the 'clientes' table.
-- =================================================================

ALTER TABLE `clientes`
ADD COLUMN `estado` ENUM('Activado', 'Desactivado') NOT NULL DEFAULT 'Activado' COMMENT 'Estado del cliente: Activado o Desactivado' AFTER `codigo_erp`;

-- =================================================================
-- Update Stored Procedures related to clients
-- =================================================================

-- Note: The full definitions of the updated stored procedures are in 'stored_procedures_clientes.sql'.
-- This note is for context. The actual changes will be applied directly to that file.
