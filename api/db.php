<?php
/**
 * Обработчик подключения к БД (паттерн Singleton)
 * Предоставляет подключение PDO и вспомогательные функции для подготовленных запросов
 */

class Database {
    private static $instance = null;
    private $pdo = null;
    private $dbPath = null;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../db/comfort-rest.db';
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect() {
        if ($this->pdo === null) {
            try {
                $dsn = 'sqlite:' . $this->dbPath;
                $this->pdo = new PDO($dsn);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    public function getPDO() {
        return $this->connect();
    }

    /**
     * Выполнение подготовленного запроса с параметрами
     *
     * @param string $sql SQL запрос с ? заполнителями
     * @param array $params Значения параметров
     * @return PDOStatement
     */
    public function prepare($sql, $params = []) {
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $paramKey = is_int($key) ? $key + 1 : $key;
                $stmt->bindValue($paramKey, $value);
            }
        }

        return $stmt;
    }

    /**
     * Выполнение запроса и получение всех результатов
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Выполнение запроса и получение одного результата
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Выполнение INSERT/UPDATE/DELETE и возврат числа затронутых строк
     */
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Получение последнего вставленного ID (для SQLite)
     */
    public function lastInsertId() {
        return $this->connect()->lastInsertId();
    }

    /**
     * Начало транзакции
     */
    public function beginTransaction() {
        return $this->connect()->beginTransaction();
    }

    /**
     * Подтверждение транзакции
     */
    public function commit() {
        return $this->connect()->commit();
    }

    /**
     * Откат транзакции
     */
    public function rollBack() {
        return $this->connect()->rollBack();
    }
}
?>
