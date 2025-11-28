<?php

namespace App\Infrastructure\Persistence;

use PDO;

interface DatabaseInterface
{
    public function getConnection(): PDO;
    
    public function beginTransaction(): bool;
    
    public function commit(): bool;
    
    public function rollback(): bool;
    
    public function inTransaction(): bool;
}
