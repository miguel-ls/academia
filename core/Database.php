<?php

// =================================================================
// Clase Database: Wrapper para la conexión PDO y ejecución
// de Procedimientos Almacenados.
// =================================================================

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh; // Database Handler
    private $stmt; // Statement
    private $error;

    public function __construct() {
        // Configurar DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
        $options = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Crear una instancia de PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // En un entorno de producción, loguear el error en lugar de mostrarlo.
            die('Error de conexión: ' . $this->error);
        }
    }

    /**
     * Llama a un procedimiento almacenado.
     *
     * @param string $procedure El nombre del procedimiento almacenado.
     * @param array $params Un array de parámetros para el procedimiento.
     * @return PDOStatement El objeto PDOStatement para su posterior procesamiento.
     */
    public function callStoredProcedure($procedure, $params = []) {
        // Construir la cadena de llamada al procedimiento
        $param_placeholders = implode(',', array_fill(0, count($params), '?'));
        $sql = "CALL $procedure($param_placeholders)";

        try {
            $this->stmt = $this->dbh->prepare($sql);

            // Vincular los parámetros
            $i = 1;
            foreach ($params as $param) {
                $this->stmt->bindValue($i++, $param);
            }

            $this->stmt->execute();
            return $this->stmt;

        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Manejar el error (loguear, mostrar un mensaje genérico, etc.)
            // Por ahora, lo mostraremos para depuración.
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
}
