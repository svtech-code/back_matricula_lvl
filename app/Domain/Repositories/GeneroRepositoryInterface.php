<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Genero;

interface GeneroRepositoryInterface
{
    public function findAll(): array;
}
