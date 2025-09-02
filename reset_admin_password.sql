-- =================================================================
-- Script para Restablecer la Contraseña del Administrador
-- =================================================================
--
-- Ejecute este script en su cliente de base de datos (ej. phpMyAdmin)
-- si está teniendo problemas para iniciar sesión con el usuario 'admin'.
--
-- Esto restablecerá la contraseña del usuario 'admin' a: 123456
--
-- El hash que se usa a continuación fue generado con la función
-- password_hash('123456', PASSWORD_DEFAULT) de PHP.
-- =================================================================

USE `academia_cursos`; -- Asegúrese de que el nombre de su base de datos sea correcto

UPDATE `usuarios`
SET `password_hash` = '$2y$10$t/iagDSBOSf1d2YhA.Lzce4jK2jaxtfr5QyCoLGgUoM5a9WprgI.q'
WHERE `nombre_usuario` = 'admin';

-- Fin del script. Ahora debería poder iniciar sesión con admin / 123456.
