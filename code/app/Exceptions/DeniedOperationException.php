<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class DeniedOperationException extends Exception implements HttpExceptionInterface
{
    /**
     * This status code will be returned
     * when you offer to solving this problem
     * for you client
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }

    public function getHeaders(): array
    {
        return [];
    }

    /**
     * Getting instruction for allow this operation
     *
     * @param string $flag Flag for you need to send
     * for allow this operation
     * @param string $operation What kind of operation need to solve
     * @return string Solve message
     */
    public function getSolve(string $flag, string $operation): string
    {
        return "Send flag {$flag}=1 for allow {$operation}";
    }
}
