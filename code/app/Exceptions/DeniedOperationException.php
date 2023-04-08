<?php

namespace App\Exceptions;

use Exception;

class DeniedOperationException extends Exception
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
