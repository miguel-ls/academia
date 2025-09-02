<?php

// =================================================================
// Clase Database: Implementa el patrón Singleton para asegurar
// una única conexión a la base de datos.
// =================================================================

class Database {
    private static $instance = null;
    private $dbh;
    private $stmt;
    private $error;

    // El constructor es privado, no se puede instanciar con 'new' desde fuera.
    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->dbh = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die('Error de conexión: ' . $this->error);
        }
    }

    /**
     * Obtiene la única instancia de la clase Database.
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Llama a un procedimiento almacenado.
     * @param string $procedure El nombre del procedimiento almacenado.
     * @param array $params Un array de parámetros para el procedimiento.
     * @return PDOStatement
     */
    public function callStoredProcedure($procedure, $params = []) {
        $param_placeholders = implode(',', array_fill(0, count($params), '?'));
        $sql = "CALL $procedure($param_placeholders)";

        try {
            $this->stmt = $this->dbh->prepare($sql);
            $i = 1;
            foreach ($params as $param) {
                $this->stmt->bindValue($i++, $param);
            }
            $this->stmt->execute();
            return $this->stmt;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die('Error al ejecutar procedimiento: ' . $this->error);
        }
    }

    /**
     * Obtiene un conjunto de resultados (múltiples filas).
     */
    public function resultSet() {
        $result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->stmt->closeCursor();
        return $result;
    }

    /**
     * Obtiene un único resultado (una fila).
     */
    public function single() {
        $result = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $this->stmt->closeCursor();
        return $result;
    }

    /**
     * Devuelve el número de filas afectadas por la última operación.
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // --- Métodos para Transacciones ---
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }

    public function commit() {
        return $this->dbh->commit();
    }

    public function rollBack() {
        return $this->dbh->rollBack();
    }

    /**
     * Cierra la conexión y destruye la instancia Singleton.
     * Usado para forzar una reconexión.
     */
    public static function close() {
        if (self::$instance !== null) {
            self::$instance->dbh = null;
            self::$instance = null;
        }
    }
}
