<?php

namespace App\Exceptions;

use Exception;

class InvalidCookieException extends Exception
{
    private string $reason;

    /**
     * When received cookie is invalid for some reasons
     *
     * @param string $reason For example is not a json
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public function context() {
        return [
            'reason' => $this->reason
        ];
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
