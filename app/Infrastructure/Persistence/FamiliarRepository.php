<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Familiar;
use App\Domain\Repositories\FamiliarRepositoryInterface;
use PDO;

class FamiliarRepository implements FamiliarRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findByRut(int $runFamiliar): ?Familiar
    {
        $conn = $this->database->getConnection();
        $query = "SELECT 
                    f.cod_familiar, 
                    f.run_familiar, 
                    f.dv_run_familiar, 
                    f.nombres, 
                    f.apellido_paterno, 
                    f.apellido_materno, 
                    f.direccion, 
                    f.comuna, 
                    f.actividad_laboral, 
                    f.cod_escolaridad, 
                    f.lugar_trabajo, 
                    f.email, 
                    f.numero_telefonico,
                    fpfe.cod_tipo_familiar,
                    fpfe.es_titular,
                    fpfe.es_suplente
                  FROM familiar f
                  LEFT JOIN familiar_por_ficha_estudiante fpfe ON f.cod_familiar = fpfe.cod_familiar
                  WHERE f.run_familiar = :run_familiar
                  LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':run_familiar', $runFamiliar, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapToEntity($row);
    }

    private function mapToEntity(array $row): Familiar
    {
        return new Familiar(
            $row['cod_familiar'],
            $row['run_familiar'],
            $row['dv_run_familiar'],
            $row['nombres'],
            $row['apellido_paterno'],
            $row['apellido_materno'],
            $row['direccion'],
            $row['comuna'],
            $row['actividad_laboral'],
            $row['cod_escolaridad'],
            $row['lugar_trabajo'],
            $row['email'],
            $row['numero_telefonico'],
            $row['cod_tipo_familiar'] ?? 0,
            $row['es_titular'] ?? false,
            $row['es_suplente'] ?? false
        );
    }
}
