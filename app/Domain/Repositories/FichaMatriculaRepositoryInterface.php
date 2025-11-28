<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\FichaMatricula;

interface FichaMatriculaRepositoryInterface
{
    public function create(FichaMatricula $fichaMatricula): ?int;
    
    public function findById(int $codFichaMatricula): ?FichaMatricula;
    
    public function findAll(): array;

    public function verificarPrematricula(int $runEstudiante, int $periodoLectivo, int $estadoFichaMatricula): ?array;
}
