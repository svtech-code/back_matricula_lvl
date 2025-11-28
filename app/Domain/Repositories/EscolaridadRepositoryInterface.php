<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Escolaridad;

interface EscolaridadRepositoryInterface
{
    public function findAll(): array;
}
