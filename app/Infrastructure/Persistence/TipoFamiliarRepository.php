<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\TipoFamiliar;
use App\Domain\Repositories\TipoFamiliarRepositoryInterface;
use PDO;

class TipoFamiliarRepository implements TipoFamiliarRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findAll(): array
    {
        $conn = $this->database->getConnection();
        $query = "SELECT cod_tipo_familiar, descripcion_familiar FROM tipo_familiar ORDER BY cod_tipo_familiar ASC";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return $this->mapToEntity($row);
        }, $rows);
    }

    private function mapToEntity(array $row): TipoFamiliar
    {
        return new TipoFamiliar(
            $row['cod_tipo_familiar'],
            $row['descripcion_familiar']
        );
    }
}
