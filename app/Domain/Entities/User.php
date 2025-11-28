<?php

namespace App\Domain\Entities;

class User
{
    private ?int $id;
    private string $email;
    private string $password;
    private string $nombre;
    private string $rol;

    public function __construct(?int $id, string $email, string $password, string $nombre, string $rol)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->rol = $rol;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'nombre' => $this->nombre,
            'rol' => $this->rol
        ];
    }
}
