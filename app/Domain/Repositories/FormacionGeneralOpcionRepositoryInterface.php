<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\FormacionGeneralOpcion;

interface FormacionGeneralOpcionRepositoryInterface
{
    public function findAll(): array;
}
