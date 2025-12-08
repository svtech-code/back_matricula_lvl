<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\FichaMatricula;

interface FichaMatriculaRepositoryInterface
{
    public function create(FichaMatricula $fichaMatricula): ?int;
    
    public function findById(int $codFichaMatricula): ?FichaMatricula;
    
    public function findAll(): array;

    public function verificarPrematricula(int $runEstudiante, int $periodoLectivo, int $estadoFichaMatricula): ?array;
    
    public function findByIdWithAllDetails(int $codFichaMatricula): ?FichaMatricula;
    
    public function findByEstudianteAndPeriodoCodigo(int $runEstudiante, int $codPeriodoLectivo): ?FichaMatricula;
    
    public function findEstudiantesByPeriodoLectivo(int $codPeriodoLectivo): array;
    
    public function updateFichaMatriculaCompleta(int $codFichaMatricula, array $data): bool;
}
