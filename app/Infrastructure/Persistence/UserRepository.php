<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findByEmail(string $email): ?User
    {
        $conn = $this->database->getConnection();
        $query = "SELECT id, email, password, nombre, rol FROM usuarios WHERE email = :email LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new User(
            $row['id'],
            $row['email'],
            $row['password'],
            $row['nombre'],
            $row['rol']
        );
    }

    public function save(User $user): bool
    {
        $conn = $this->database->getConnection();
        
        if ($user->getId() === null) {
            $query = "INSERT INTO usuarios (email, password, nombre, rol) VALUES (:email, :password, :nombre, :rol)";
            $stmt = $conn->prepare($query);
        } else {
            $query = "UPDATE usuarios SET email = :email, password = :password, nombre = :nombre, rol = :rol WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $user->getId());
        }

        $stmt->bindParam(':email', $user->getEmail());
        $stmt->bindParam(':password', $user->getPassword());
        $stmt->bindParam(':nombre', $user->getNombre());
        $stmt->bindParam(':rol', $user->getRol());

        return $stmt->execute();
    }
}
