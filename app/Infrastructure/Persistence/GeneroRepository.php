<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Genero;
use App\Domain\Repositories\GeneroRepositoryInterface;
use PDO;

class GeneroRepository implements GeneroRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findAll(): array
    {
        $conn = $this->database->getConnection();
        $query = "SELECT cod_genero, descripcion FROM genero ORDER BY cod_genero ASC";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return $this->mapToEntity($row);
        }, $rows);
    }

    private function mapToEntity(array $row): Genero
    {
        return new Genero(
            $row['cod_genero'],
            $row['descripcion']
        );
    }
}
