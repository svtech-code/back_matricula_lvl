<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\FormacionGeneralOpcion;
use App\Domain\Repositories\FormacionGeneralOpcionRepositoryInterface;
use PDO;

class FormacionGeneralOpcionRepository implements FormacionGeneralOpcionRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findAll(): array
    {
        $conn = $this->database->getConnection();
        $query = "SELECT cod_fg_opciones, nombre_asignatura, categoria FROM formacion_general_opciones ORDER BY categoria ASC, nombre_asignatura ASC";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return $this->mapToEntity($row);
        }, $rows);
    }

    private function mapToEntity(array $row): FormacionGeneralOpcion
    {
        return new FormacionGeneralOpcion(
            $row['cod_fg_opciones'],
            $row['nombre_asignatura'],
            $row['categoria']
        );
    }
}
