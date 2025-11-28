<?php

namespace App\Application\DTOs;

class LoginResponseDTO
{
    private bool $success;
    private string $message;
    private ?array $data;

    public function __construct(bool $success, string $message, ?array $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message
        ];

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        return $response;
    }
}
