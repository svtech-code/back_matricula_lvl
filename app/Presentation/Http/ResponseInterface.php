<?php

namespace App\Presentation\Http;

interface ResponseInterface
{
    public function json(array $data, int $statusCode = 200): void;
}
