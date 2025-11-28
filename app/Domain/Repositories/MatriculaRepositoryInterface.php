<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Matricula;

interface MatriculaRepositoryInterface
{
    public function findById(int $id): ?Matricula;
    public function findAll(): array;
    public function save(Matricula $matricula): bool;
    public function update(Matricula $matricula): bool;
}
