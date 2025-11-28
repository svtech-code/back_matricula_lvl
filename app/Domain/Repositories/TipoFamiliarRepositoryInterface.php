<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\TipoFamiliar;

interface TipoFamiliarRepositoryInterface
{
    public function findAll(): array;
}
