<?php

namespace App\Exceptions;

use Exception;

class ReceivingInspectionException extends Exception
{
    public int $status;

    public array $rejectedFields;

    public array $unknownFields;

    public function __construct(string $message, int $status = 400, array $rejectedFields = [], array $unknownFields = [])
    {
        parent::__construct($message);
        $this->status = $status;
        $this->rejectedFields = $rejectedFields;
        $this->unknownFields = $unknownFields;
    }

    public static function rejectedFields(array $fields): self
    {
        return new self(
            'You are not allowed to write the following fields: ' . implode(', ', $fields),
            403,
            $fields
        );
    }

    public static function unknownFields(array $fields): self
    {
        return new self(
            'Unknown fields: ' . implode(', ', $fields),
            400,
            [],
            $fields
        );
    }

    public static function conflict(string $message): self
    {
        return new self($message, 409);
    }

    public static function badRequest(string $message): self
    {
        return new self($message, 400);
    }
}
