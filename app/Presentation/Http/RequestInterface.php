<?php

namespace App\Presentation\Http;

interface RequestInterface
{
    public function getData(): object;
    public function getParam(string $name): ?string;
    public function getQueryParams(): array;
}
