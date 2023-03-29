<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedUserException extends Exception
{
    private string $userId;

    /**
     * When user is not authorized
     *
     * @param string $userId Use any user id
     * ex. value from db, alias
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function context() {
        return [
            'userId' => $this->userId
        ];
    }
}
