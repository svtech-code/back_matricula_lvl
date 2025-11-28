<?php

namespace App\Application\UseCases;

use App\Domain\Entities\FichaMatricula;
use App\Domain\Repositories\FichaMatriculaRepositoryInterface;

class CreateFichaMatriculaUseCase
{
    private FichaMatriculaRepositoryInterface $repository;

    public function __construct(FichaMatriculaRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): FichaMatricula
    {
        $ficha = FichaMatricula::fromArray($data);
        
        $id = $this->repository->create($ficha);
        
        $ficha->setId($id);
        
        return $ficha;
    }
}
