<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class NotFoundEntityException extends Exception  implements HttpExceptionInterface
{
    private string|int $id;
    private string $name;

    /**
     * When the requested object
     * is not found by your system
     *
     * @param string|int $id Any identificator of called object
     * @param string $name Name of the entity
     */
    public function __construct(string|int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Return status code when entity not found
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 404;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function context() {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
