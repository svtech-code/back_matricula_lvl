<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Matricula;
use App\Domain\Repositories\MatriculaRepositoryInterface;
use PDO;
use DateTime;

class MatriculaRepository implements MatriculaRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findById(int $id): ?Matricula
    {
        $conn = $this->database->getConnection();
        $query = "SELECT id, nombres, nombre_social, apellido_paterno, apellido_materno, fecha_nacimiento, grado 
                  FROM matriculas 
                  WHERE id = :id LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapToEntity($row);
    }

    public function findAll(): array
    {
        $conn = $this->database->getConnection();
        $query  = "SELECT * from estudiante";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return $this->mapToEntity($row);
        }, $rows);
    }

    public function save(Matricula $matricula): bool
    {
        $conn = $this->database->getConnection();

        $query = "INSERT INTO matriculas 
                  (nombres, nombre_social, apellido_paterno, apellido_materno, fecha_nacimiento, grado) 
                  VALUES (:nombres, :nombre_social, :apellido_paterno, :apellido_materno, :fecha_nacimiento, :grado)";

        $stmt = $conn->prepare($query);

        $nombres = $matricula->getNombres();
        $nombreSocial = $matricula->getNombreSocial();
        $apellidoPaterno = $matricula->getApellidoPaterno();
        $apellidoMaterno = $matricula->getApellidoMaterno();
        $fechaNacimiento = $matricula->getFechaNacimiento()->format('Y-m-d');
        $grado = $matricula->getGrado();

        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':nombre_social', $nombreSocial);
        $stmt->bindParam(':apellido_paterno', $apellidoPaterno);
        $stmt->bindParam(':apellido_materno', $apellidoMaterno);
        $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento);
        $stmt->bindParam(':grado', $grado);

        return $stmt->execute();
    }

    public function update(Matricula $matricula): bool
    {
        $conn = $this->database->getConnection();

        $query = "UPDATE matriculas 
                  SET nombres = :nombres, 
                      nombre_social = :nombre_social, 
                      apellido_paterno = :apellido_paterno, 
                      apellido_materno = :apellido_materno, 
                      fecha_nacimiento = :fecha_nacimiento, 
                      grado = :grado 
                  WHERE id = :id";

        $stmt = $conn->prepare($query);

        $id = $matricula->getId();
        $nombres = $matricula->getNombres();
        $nombreSocial = $matricula->getNombreSocial();
        $apellidoPaterno = $matricula->getApellidoPaterno();
        $apellidoMaterno = $matricula->getApellidoMaterno();
        $fechaNacimiento = $matricula->getFechaNacimiento()->format('Y-m-d');
        $grado = $matricula->getGrado();

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':nombre_social', $nombreSocial);
        $stmt->bindParam(':apellido_paterno', $apellidoPaterno);
        $stmt->bindParam(':apellido_materno', $apellidoMaterno);
        $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento);
        $stmt->bindParam(':grado', $grado);

        return $stmt->execute();
    }

    private function mapToEntity(array $row): Matricula
    {
        return new Matricula(
            $row['id'],
            $row['nombres'],
            $row['nombre_social'],
            $row['apellido_paterno'],
            $row['apellido_materno'],
            new DateTime($row['fecha_nacimiento']),
            $row['grado']
        );
    }
}
