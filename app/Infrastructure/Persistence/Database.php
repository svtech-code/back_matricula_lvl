<?php

namespace App\Infrastructure\Persistence;

use PDO;
use PDOException;
use Flight;

class Database implements DatabaseInterface
{
    private ?PDO $connection = null;

    public function __construct()
    {
        $this->registerFlightDatabase();
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connection = Flight::db();
        }

        return $this->connection;
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->getConnection()->inTransaction();
    }

    private function registerFlightDatabase(): void
    {
        if (Flight::has('db')) {
            return;
        }

        try {
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
            $dbName = $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? getenv('DB_DATABASE') ?? getenv('DB_NAME');
            $user = $_ENV['DB_USERNAME'] ?? $_ENV['DB_USER'] ?? getenv('DB_USERNAME') ?? getenv('DB_USER');
            $password = $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? getenv('DB_PASSWORD') ?? getenv('DB_PASS') ?? '';
            $driver = $_ENV['DB_DRIVER'] ?? getenv('DB_DRIVER') ?? 'pgsql';
            $port = (int)($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? 5432);

            $dsn = $this->buildDsn($driver, $host, $port, $dbName);

            Flight::register(
                'db',
                PDO::class,
                [$dsn, $user, $password],
                function($db) {
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                    $db->setAttribute(PDO::ATTR_PERSISTENT, false);
                }
            );
        } catch (PDOException $e) {
            throw new \RuntimeException("Error de conexión: " . $e->getMessage());
        }
    }

    private function buildDsn(string $driver, string $host, int $port, string $dbName): string
    {
        if ($driver === 'pgsql') {
            return "pgsql:host={$host};port={$port};dbname={$dbName}";
        }

        return "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
    }
}
