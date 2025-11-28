<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Escolaridad;
use App\Domain\Repositories\EscolaridadRepositoryInterface;
use PDO;

class EscolaridadRepository implements EscolaridadRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findAll(): array
    {
        $conn = $this->database->getConnection();
        $query = "SELECT cod_escolaridad, descripcion FROM escolaridad ORDER BY cod_escolaridad ASC";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return $this->mapToEntity($row);
        }, $rows);
    }

    private function mapToEntity(array $row): Escolaridad
    {
        return new Escolaridad(
            $row['cod_escolaridad'],
            $row['descripcion']
        );
    }
}
