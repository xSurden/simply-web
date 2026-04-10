<?php

    namespace App\Modules\Database;

    use PDO;
    use PDOException;
    use Exception;

    class SQLite {

        private string $filePath = ABSPATH . "/server/data/modules/sqlite/storage.db";
        private ?PDO $connection = null;

        public function __construct()
        {
            try {
                $this->connection = new PDO("sqlite:" . $this->filePath);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $this->connection->exec("PRAGMA foreign_keys = ON;");
                
            } catch (PDOException $e) {
                throw new Exception("Unable to connect to SQLite database: " . $e->getMessage());
            }
        }

        public function select(string $sql, array $params = []): array
        {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function execute(string $sql, array $params = []): bool
        {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        }

        public function lastInsertId(): string|bool
        {
            return $this->connection->lastInsertId();
        }

        public function close(): void
        {
            $this->connection = null;
        }
    }