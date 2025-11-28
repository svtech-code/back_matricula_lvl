<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Familiar;

interface FamiliarRepositoryInterface
{
    public function findByRut(int $runFamiliar): ?Familiar;
}
